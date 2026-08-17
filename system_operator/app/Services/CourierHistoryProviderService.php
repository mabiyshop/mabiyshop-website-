<?php

namespace App\Services;

class CourierHistoryProviderService
{
    private $bdCourierHistoryService;
    private $courierHistoryService;

    public function __construct(
        BdCourierHistoryService $bdCourierHistoryService,
        CourierHistoryService $courierHistoryService
    ) {
        $this->bdCourierHistoryService = $bdCourierHistoryService;
        $this->courierHistoryService = $courierHistoryService;
    }

    public function getHistory(string $phone, array $options = []): array
    {
        $bdResult = $this->bdCourierHistoryService->getHistory($phone, $options);

        if (($bdResult['status'] ?? '') === 'ok') {
            return $bdResult;
        }

        $fraudBdResult = $this->courierHistoryService->getHistory($phone, $options);

        if (($fraudBdResult['status'] ?? '') === 'ok') {
            return $fraudBdResult;
        }

        return $this->emptyResult();
    }

    private function emptyResult(): array
    {
        return [
            'phone' => '',
            'status' => 'unavailable',
            'error' => 'all_providers_unavailable',
            'couriers' => [],
            'combined' => [
                'total' => 0,
                'successful' => 0,
                'failed' => 0,
                'success_rate' => 0.0,
            ],
        ];
    }
}
