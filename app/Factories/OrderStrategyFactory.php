<?php

namespace App\Factories;

use App\Strategies\Interfaces\OrderIntegrationStrategy;
use App\Strategies\Implementations\WooCommerceStrategy;
use Illuminate\Http\Request;

class OrderStrategyFactory
{
    public static function make(Request $request): OrderIntegrationStrategy
    {
        $userAgent = $request->userAgent();

        if (str_contains($userAgent, 'WooCommerce')) {
            return new WooCommerceStrategy();
        }

        throw new \Exception("Origem não suportada.");
    }
}
