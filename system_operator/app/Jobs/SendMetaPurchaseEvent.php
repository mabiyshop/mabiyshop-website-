<?php

namespace App\Jobs;

use App\Models\MetaConversionEvent;
use App\Models\Order;
use App\Services\MetaAcceptedPurchaseService;
use App\Services\MetaConversionSenderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendMetaPurchaseEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $orderId;
    private $eventId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
        $this->eventId = 'purchase_' . $orderId;
    }

    public function handle(MetaAcceptedPurchaseService $acceptedPurchaseService, MetaConversionSenderService $senderService)
    {
        $existing = MetaConversionEvent::where('event_id', $this->eventId)->first();

        if ($existing && $existing->status === 'SENT') {
            return;
        }

        $order = Order::find($this->orderId);

        if (!$order || (int) $order->status !== 2) {
            return;
        }

        $payload = $acceptedPurchaseService->buildPayload($order);

        if (($payload['eligible'] ?? false) !== true) {
            $this->record($order->id, $payload['event_name'] ?? 'Purchase', 'FAILED', $payload['reason'] ?? 'ineligible_payload');

            return;
        }

        $phone = null;

        if (!empty($order->user_id)) {
            $phone = \App\Models\User::where('id', $order->user_id)->value('phone');
        }

        $result = $senderService->send($payload, $phone, [
            'pixel_id' => config('services.meta_capi.pixel_id'),
            'access_token' => config('services.meta_capi.access_token'),
            'test_event_code' => config('services.meta_capi.test_event_code'),
            'dry_run' => filter_var(config('services.meta_capi.dry_run', true), FILTER_VALIDATE_BOOLEAN),
            'client_ip_address' => $order->ip_address,
            'client_user_agent' => $order->client_user_agent,
            'fbp' => $order->fbp,
            'fbc' => $order->fbc,
            'email' => $order->email,
        ]);

        $status = ($result['sent'] ?? false) === true ? 'SENT' : 'FAILED';
        $lastError = $result['reason'] ?? ($result['provider_response']['error_type'] ?? 'unknown');

        $this->record($order->id, $payload['event_name'], $status, $lastError);
    }

    public function failed(Throwable $exception)
    {
        $this->record($this->orderId, 'Purchase', 'FAILED', $exception->getMessage());
    }

    private function record(int $orderId, string $eventName, string $status, ?string $lastError = null): void
    {
        $existing = MetaConversionEvent::where('event_id', $this->eventId)->first();

        if ($existing) {
            $existing->update([
                'status' => $status,
                'attempts' => ($existing->attempts ?? 0) + 1,
                'last_error' => $lastError,
                'sent_at' => $status === 'SENT' ? now() : $existing->sent_at,
            ]);

            return;
        }

        MetaConversionEvent::create([
            'order_id' => $orderId,
            'event_name' => $eventName,
            'event_id' => $this->eventId,
            'status' => $status,
            'attempts' => 1,
            'last_error' => $lastError,
            'sent_at' => $status === 'SENT' ? now() : null,
        ]);
    }
}
