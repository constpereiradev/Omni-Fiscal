<?php

namespace App\Services;

use App\Strategies\Interfaces\OrderIntegrationStrategy;
use App\Models\Order;
use App\Jobs\ProcessInvoiceJob;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Processa um pedido, o armazenando e adicionando  na filha de processamento a emissão da NF.
     * @param array $data
     * @param OrderIntegrationStrategy $strategy
     */
    public function process(array $data, OrderIntegrationStrategy $strategy)
    {
        $cleanData = $strategy->transform($data);

        $orderData = (array) $cleanData;
        Log::info(message: "Iniciando o cadastro de pedido");

        try {
            //Salva os dados do pedido no banco
            $order = Order::updateOrCreate(
                [
                    'external_id' => $orderData['external_id'],
                    'origin' => $orderData['origin'],
                ],
                $orderData
            );

            //Coloca na fila de processamento a emissão da nota
            ProcessInvoiceJob::dispatch($order);

            return $order;
        } catch (\Exception $e) {
            Log::error(message: $e->getMessage());
        }
    }
}   