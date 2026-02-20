<?php

namespace App\Strategies\Implementations;

use App\Strategies\Interfaces\OrderIntegrationStrategy;


class WooCommerceStrategy implements OrderIntegrationStrategy
{
    public function transform(array $data): array
    {
        $billing = $data['billing'] ?? [];
        $shipping = $data['shipping'] ?? [];

        return [
            'external_id' => $data['id'] ?? null,
            'origin' => 'woocomerce',
            'customer_id' => $data['customer_id'] ?? null,
            'raw_data' => json_encode($data) ?? null,
            'order_key' => $data['order_key'] ?? null,
            'currency' => $data['currency'] ?? null,
            'billing_name'     => trim(($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? '')),
            'billing_address'  => trim(($billing['address_1'] ?? '') . ' ' . ($billing['address_2'] ?? '')),
            
            'shipping_name'    => trim(($shipping['first_name'] ?? '') . ' ' . ($shipping['last_name'] ?? '')),
            'shipping_address' => trim(($shipping['address_1'] ?? '') . ' ' . ($shipping['address_2'] ?? '')),

            'items' => $data['line_items'] ?? null,
            'total' => $data['total'] ?? null,
            'discount_total' => $data['discount_total'] ?? null,
            'shipping_total' => $data['shipping_total'] ?? null,
            'status' => $data['status'] ?? null,
            'last_error' => $data['last_error'] ?? null,
        ];
    }
}
