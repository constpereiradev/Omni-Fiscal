<?php

namespace App\Strategies\Implementations;

use App\Strategies\Interfaces\OrderIntegrationStrategy;


class WooCommerceStrategy implements OrderIntegrationStrategy
{
    public function transform(array $data): array
    {
        return [
            'external_id' => $data['id'] ?? null,
            'origin' => 'woocomerce',
            'customer_id' => $data['customer_id'] ?? null,
            'raw_data' => json_encode($data) ?? null,
            'order_key' => $data['order_key'] ?? null,
            'currency' => $data['currency'] ?? null,
            'billing_name' => $data['billing']['first_name'] .  '' . $data['billing']['last_name'] ?? null,
            'billing_address' => $data['billing']['address_1'] .  '' . $data['billing']['address_2'] ?? null,
            'shipping_name' => $data['shipping']['first_name'] .  '' . $data['shipping']['last_name'] ?? null,
            'shipping_address' => $data['shipping']['address_1'] .  '' . $data['shipping']['address_2'] ?? null,
            'items' => $data['line_items'] ?? null,
            'total' => $data['total'] ?? null,
            'discount_total' => $data['discount_total'] ?? null,
            'shipping_total' => $data['shipping_total'] ?? null,
            'status' => $data['status'] ?? null,
            'last_error' => $data['last_error'] ?? null,
        ];
    }
}
