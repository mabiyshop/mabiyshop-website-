<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MetaConversionSenderService
{
    private const API_VERSION = 'v26.0';

    public function send(array $payload, ?string $phone, array $options = []): array
    {
        if (!$this->isEligible($payload)) {
            return [
                'sent' => false,
                'reason' => 'ineligible_payload',
            ];
        }

        $normalizedPhone = $this->normalizePhone($phone);

        if ($normalizedPhone === null) {
            return [
                'sent' => false,
                'reason' => 'invalid_phone',
            ];
        }

        $dryRun = array_key_exists('dry_run', $options)
            ? (bool) $options['dry_run']
            : true;
        $pixelId = $this->stringOption($options, 'pixel_id');
        $accessToken = $this->stringOption($options, 'access_token');
        $testEventCode = $this->stringOption($options, 'test_event_code');
        $clientIpAddress = $this->ipOption($options, 'client_ip_address');
        $clientUserAgent = $this->stringOption($options, 'client_user_agent');
        $fbp = $this->stringOption($options, 'fbp');
        $fbc = $this->stringOption($options, 'fbc');
        $hashedEmail = $this->hashedEmailOption($options, 'email');
        $eventId = (string) $payload['event_id'];
        $endpoint = $this->endpoint($pixelId);
        $requestBody = $this->requestBody(
            $payload,
            $normalizedPhone,
            $testEventCode,
            $clientIpAddress,
            $clientUserAgent,
            $fbp,
            $fbc,
            $hashedEmail
        );

        unset($normalizedPhone);

        if ($dryRun) {
            return [
                'sent' => false,
                'dry_run' => true,
                'reason' => 'dry_run',
                'endpoint' => $endpoint,
                'event_id' => $eventId,
                'request_body' => $requestBody,
            ];
        }

        if ($pixelId === null || $accessToken === null) {
            return [
                'sent' => false,
                'reason' => 'missing_credentials',
            ];
        }

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->timeout(10)
                ->post($endpoint, array_merge($requestBody, [
                    'access_token' => $accessToken,
                ]));
        } catch (\Throwable $exception) {
            return [
                'sent' => false,
                'reason' => 'provider_request_failed',
            ];
        }

        $responseBody = $response->json();

        if (!is_array($responseBody)) {
            return [
                'sent' => false,
                'reason' => 'invalid_response',
            ];
        }

        if (!$response->successful()) {
            return [
                'sent' => false,
                'reason' => 'provider_http_error',
                'provider_response' => $this->providerSummary($response->status(), $responseBody),
            ];
        }

        return [
            'sent' => true,
            'event_id' => $eventId,
            'provider_response' => $this->providerSummary($response->status(), $responseBody),
        ];
    }

    private function isEligible(array $payload): bool
    {
        if (($payload['eligible'] ?? false) !== true) {
            return false;
        }

        if (($payload['event_name'] ?? null) !== 'Purchase') {
            return false;
        }

        foreach (['event_id', 'transaction_id', 'currency'] as $field) {
            if (!isset($payload[$field]) || !is_scalar($payload[$field]) || (string) $payload[$field] === '') {
                return false;
            }
        }

        return array_key_exists('value', $payload) && is_numeric($payload['value']);
    }

    private function normalizePhone(?string $phone): ?string
    {
        if ($phone === null || $phone === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if (!is_string($digits)) {
            return null;
        }

        if (preg_match('/^01[3-9][0-9]{8}$/', $digits) === 1) {
            return '88' . $digits;
        }

        if (preg_match('/^8801[3-9][0-9]{8}$/', $digits) === 1) {
            return $digits;
        }

        return null;
    }

    private function requestBody(
        array $payload,
        string $normalizedPhone,
        ?string $testEventCode,
        ?string $clientIpAddress,
        ?string $clientUserAgent,
        ?string $fbp,
        ?string $fbc,
        ?string $hashedEmail
    ): array
    {
        $body = [
            'data' => [[
                'event_name' => 'Purchase',
                'event_time' => time(),
                'event_id' => (string) $payload['event_id'],
                'action_source' => 'website',
                'user_data' => [
                    'ph' => [hash('sha256', $normalizedPhone)],
                ],
                'custom_data' => [
                    'currency' => (string) $payload['currency'],
                    'value' => (float) $payload['value'],
                    'order_id' => (string) $payload['transaction_id'],
                    'contents' => $this->normalizeContents($payload['items'] ?? []),
                ],
            ]],
        ];

        if ($clientIpAddress !== null) {
            $body['data'][0]['user_data']['client_ip_address'] = $clientIpAddress;
        }

        if ($clientUserAgent !== null) {
            $body['data'][0]['user_data']['client_user_agent'] = $clientUserAgent;
        }

        if ($fbp !== null) {
            $body['data'][0]['user_data']['fbp'] = $fbp;
        }

        if ($fbc !== null) {
            $body['data'][0]['user_data']['fbc'] = $fbc;
        }

        if ($hashedEmail !== null) {
            $body['data'][0]['user_data']['em'] = [$hashedEmail];
        }

        if ($testEventCode !== null) {
            $body['test_event_code'] = $testEventCode;
        }

        return $body;
    }

    private function normalizeContents($items): array
    {
        if (!is_array($items)) {
            return [];
        }

        $contents = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $productId = $item['product_id'] ?? null;

            if (!is_scalar($productId) || (string) $productId === '') {
                continue;
            }

            $content = ['id' => (string) $productId];

            if (isset($item['quantity']) && is_numeric($item['quantity'])) {
                $content['quantity'] = max(0, (int) $item['quantity']);
            }

            if (isset($item['price']) && is_numeric($item['price'])) {
                $content['item_price'] = (float) $item['price'];
            }

            $contents[] = $content;
        }

        return $contents;
    }

    private function endpoint(?string $pixelId): string
    {
        $pathPixelId = $pixelId !== null && preg_match('/^[0-9]+$/', $pixelId) === 1
            ? $pixelId
            : '{PIXEL_ID}';

        return 'https://graph.facebook.com/' . self::API_VERSION . '/' . $pathPixelId . '/events';
    }

    private function stringOption(array $options, string $key): ?string
    {
        $value = $options[$key] ?? null;

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    private function ipOption(array $options, string $key): ?string
    {
        $value = $this->stringOption($options, $key);

        return $value !== null && filter_var($value, FILTER_VALIDATE_IP) !== false ? $value : null;
    }

    private function hashedEmailOption(array $options, string $key): ?string
    {
        $value = $this->stringOption($options, $key);

        if ($value === null) {
            return null;
        }

        $normalized = strtolower($value);

        return filter_var($normalized, FILTER_VALIDATE_EMAIL) !== false
            ? hash('sha256', $normalized)
            : null;
    }

    private function providerSummary(int $httpStatus, array $response): array
    {
        $summary = ['http_status' => $httpStatus];

        if (isset($response['events_received']) && is_numeric($response['events_received'])) {
            $summary['events_received'] = (int) $response['events_received'];
        }

        if (isset($response['error']) && is_array($response['error'])) {
            if (isset($response['error']['code']) && is_numeric($response['error']['code'])) {
                $summary['error_code'] = (int) $response['error']['code'];
            }

            if (isset($response['error']['type']) && is_string($response['error']['type'])) {
                $summary['error_type'] = $response['error']['type'];
            }
        }

        return $summary;
    }
}
