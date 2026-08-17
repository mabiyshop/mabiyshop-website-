<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class MetaConversionService
{
    public function buildPurchasePayload($order): array
    {
        if (!is_object($order) && !is_array($order)) {
            return $this->invalid('invalid_order');
        }

        $status = $this->value($order, 'status');

        if (!is_numeric($status)) {
            return $this->invalid('invalid_order');
        }

        if ((int) $status === 5) {
            return $this->invalid('order_cancelled');
        }

        if ((int) $status !== 6) {
            return $this->invalid('order_not_completed');
        }

        $orderId = $this->value($order, 'id');
        $totalAmount = $this->value($order, 'total_amount');

        if ((!is_int($orderId) && !is_string($orderId)) || $orderId === '' || !is_numeric($totalAmount)) {
            return $this->invalid('invalid_order');
        }

        $shippingCost = $this->value($order, 'shipping_cost', 0);
        $tax = $this->value($order, 'vat', 0);
        $coupon = $this->value($order, 'coupon_code');

        return [
            'eligible' => true,
            'event_name' => 'Purchase',
            'event_id' => 'purchase_' . $orderId,
            'transaction_id' => (string) $orderId,
            'currency' => 'BDT',
            'value' => (float) $totalAmount,
            'shipping' => is_numeric($shippingCost) ? (float) $shippingCost : 0.0,
            'tax' => is_numeric($tax) ? (float) $tax : 0.0,
            'coupon' => is_string($coupon) && $coupon !== '' ? $coupon : null,
            'items' => $this->normalizeItems($this->loadedOrderDetails($order)),
        ];
    }

    private function loadedOrderDetails($order): iterable
    {
        if ($order instanceof Model) {
            return $order->relationLoaded('order_details')
                ? ($order->getRelation('order_details') ?? [])
                : [];
        }

        $details = $this->value($order, 'order_details', []);

        return is_array($details) || $details instanceof \Traversable ? $details : [];
    }

    private function normalizeItems(iterable $details): array
    {
        $items = [];

        foreach ($details as $detail) {
            if (!is_object($detail) && !is_array($detail)) {
                continue;
            }

            $product = $this->loadedProduct($detail);

            $items[] = [
                'product_id' => $this->value($detail, 'product_id'),
                'name' => $this->productValue($product, ['title', 'name']),
                'quantity' => $this->integerValue($this->value($detail, 'product_qty')),
                'price' => $this->floatValue($this->value($detail, 'price')),
                'brand' => $this->productValue($product, ['brand_title', 'brand']),
                'category' => $this->productValue($product, ['category_title', 'category']),
                'variant' => $this->normalizeVariant($this->value($detail, 'product_options')),
            ];
        }

        return $items;
    }

    private function loadedProduct($detail)
    {
        if ($detail instanceof Model) {
            return $detail->relationLoaded('product') ? $detail->getRelation('product') : null;
        }

        return $this->value($detail, 'product');
    }

    private function productValue($product, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = $this->value($product, $key);

            if (is_scalar($value) && (string) $value !== '') {
                return (string) $value;
            }
        }

        return null;
    }

    private function normalizeVariant($options): ?string
    {
        if (is_string($options)) {
            $decoded = json_decode($options, true);

            if (!is_array($decoded)) {
                return $options !== '' ? $options : null;
            }

            $options = $decoded;
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

    private function value($source, string $key, $default = null)
    {
        if (is_array($source)) {
            return array_key_exists($key, $source) ? $source[$key] : $default;
        }

        if ($source instanceof Model) {
            if (array_key_exists($key, $source->getAttributes())) {
                return $source->getAttribute($key);
            }

            if ($source->relationLoaded($key)) {
                return $source->getRelation($key);
            }

            return $default;
        }

        if (is_object($source) && property_exists($source, $key)) {
            return $source->{$key};
        }

        return $default;
    }

    private function integerValue($value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }

    private function floatValue($value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }

    private function invalid(string $reason): array
    {
        return [
            'eligible' => false,
            'reason' => $reason,
        ];
    }
}
