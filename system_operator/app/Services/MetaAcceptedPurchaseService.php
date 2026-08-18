<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;

class MetaAcceptedPurchaseService
{
    public function buildPayload(Order $order): array
    {
        if (!$order instanceof Model) {
            return $this->ineligible('invalid_order');
        }

        $status = (int) $order->status;

        if ($status !== 2) {
            return $this->ineligible('order_not_accepted');
        }

        $orderId = $order->id;
        $totalAmount = $this->numeric($order->total_amount);

        if ($totalAmount === null) {
            return $this->ineligible('invalid_order');
        }

        $shippingCost = $this->numeric($order->shipping_cost ?? 0) ?? 0.0;
        $tax = $this->numeric($order->vat ?? 0) ?? 0.0;
        $coupon = $this->string($order->coupon_code ?? null);

        $items = $this->normalizeItems($order);

        return [
            'eligible' => true,
            'event_name' => 'Purchase',
            'event_id' => 'purchase_' . $orderId,
            'transaction_id' => (string) $orderId,
            'currency' => 'BDT',
            'value' => (float) $totalAmount,
            'shipping' => (float) $shippingCost,
            'tax' => (float) $tax,
            'coupon' => $coupon,
            'items' => $items,
        ];
    }

    private function normalizeItems(Order $order): array
    {
        $items = [];

        foreach ($order->order_details ?? [] as $detail) {
            $product = $detail->product ?? null;

            $items[] = [
                'product_id' => (string) $detail->product_id,
                'name' => $this->productName($product),
                'quantity' => (int) $detail->product_qty,
                'price' => (float) $detail->price,
                'brand' => $this->productField($product, ['brand_title', 'brand']),
                'category' => $this->productField($product, ['category_title', 'category']),
                'variant' => $this->variant($detail->product_options),
            ];
        }

        return $items;
    }

    private function productName($product): ?string
    {
        if (!$product) {
            return null;
        }

        $value = $product->title ?? $product->name ?? null;

        return is_scalar($value) && (string) $value !== '' ? (string) $value : null;
    }

    private function productField($product, array $keys): ?string
    {
        if (!$product) {
            return null;
        }

        foreach ($keys as $key) {
            $value = $product->$key ?? null;

            if (is_scalar($value) && (string) $value !== '') {
                return (string) $value;
            }
        }

        return null;
    }

    private function variant($options): ?string
    {
        if (is_string($options)) {
            $decoded = json_decode($options, true);

            if (is_array($decoded)) {
                $options = $decoded;
            }
        }

        if (is_object($options)) {
            $options = get_object_vars($options);
        }

        if (!is_array($options)) {
            return null;
        }

        $variant = $options['Weight'] ?? $options['weight'] ?? $options['variant'] ?? null;

        return is_scalar($variant) && (string) $variant !== '' ? (string) $variant : null;
    }

    private function numeric($value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private function string($value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    private function ineligible(string $reason): array
    {
        return [
            'eligible' => false,
            'reason' => $reason,
        ];
    }
}
