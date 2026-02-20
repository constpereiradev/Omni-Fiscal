<?php

namespace App\DTOs;

class OrderDto
{
    public function __construct(
        public readonly string $externalId,
        public readonly string $origin,
        public readonly ?string $customerId,  // Pode ser nulo
        public readonly ?string $orderKey,     // Pode ser nulo
        public readonly ?string $currency,     // Pode ser nulo
        public readonly ?string $billingName,  // Pode ser nulo
        public readonly ?string $billingAddress, // Pode ser nulo
        public readonly ?string $shippingName,  // Pode ser nulo
        public readonly ?string $shippingAddress, // Pode ser nulo
        public readonly array $items,           // Lista de itens
        public readonly ?float $total,          // Pode ser nulo
        public readonly ?float $discountTotal,  // Pode ser nulo
        public readonly ?float $shippingTotal,  // Pode ser nulo
        public readonly ?string $status,        // Pode ser nulo
        public readonly ?string $lastError,     // Pode ser nulo
        public readonly array $rawPayload       // Dados brutos
    ) {}

    /**
     * Um método estático para facilitar a criação a partir de arrays limpos
     */
    public static function fromArray(array $data): self
    {
        return new self(
            externalId: $data['external_id'] ?? '',
            origin: $data['origin'] ?? '',
            customerId: $data['customer_id'] ?? null,
            orderKey: $data['order_key'] ?? null,
            currency: $data['currency'] ?? null,
            billingName: $data['billing_name'] ?? null,
            billingAddress: $data['billing_address'] ?? null,
            shippingName: $data['shipping_name'] ?? null,
            shippingAddress: $data['shipping_address'] ?? null,
            items: $data['items'] ?? [],
            total: isset($data['total']) ? (float) $data['total'] : null,
            discountTotal: isset($data['discount_total']) ? (float) $data['discount_total'] : null,
            shippingTotal: isset($data['shipping_total']) ? (float) $data['shipping_total'] : null,
            status: $data['status'] ?? null,
            lastError: $data['last_error'] ?? null,
            rawPayload: $data['raw_data'] ?? [],
        );
    }
}