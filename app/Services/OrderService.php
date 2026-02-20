<?php

namespace App\Services;
use App\Strategies\Interfaces\OrderIntegrationStrategy;
use App\Models\Order;

class OrderService
{
    public function process(array $data, OrderIntegrationStrategy $strategy)
    {
        $cleanData = $strategy->transform($data);

        $orderData = (array) $cleanData;  
        return Order::updateOrCreate(
            [
                'external_id' => $orderData['external_id'], 
                'origin' => $orderData['origin'],
            ],
            $orderData 
        );
    }
}