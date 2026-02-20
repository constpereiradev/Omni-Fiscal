<?php

namespace App\Strategies\Interfaces;


interface OrderIntegrationStrategy
{
    public function transform(array $data): array;
}