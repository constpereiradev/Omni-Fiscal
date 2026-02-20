<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class FiscalService
{
    /**
     * Summary of emit
     * @param Order $order
     * @return void
     */
    public function emit(Order $order)
    {
        try {
            Log::info(message: "Iniciando emissão fiscal do pedido: {$order->external_id}");

            $payload = is_array($order->raw_data) ? (object) $order->raw_data : json_decode($order->raw_data);

            if (!isset($payload->line_items) || empty($payload->line_items)) {
                throw new \Exception("Pedido sem itens para emissão.");
            }

            $items = $payload->line_items;

            foreach ($items as $item) {
                $ncm = "6109.10.00"; // Esse valor deve ser buscado no banco pelo SKU
                Log::info("Processando Item: {$item->name} | NCM: {$ncm}");
            }

            //Chamada para api externa (Ex: FocusNFe) e validação de retorno.
            $this->handleSuccess($order);
        } catch (\Exception $e) {
            $this->handleError($order, $e->getMessage());
        }

    }

    private function handleSuccess(Order $order)
    {
        $order->update([
            'fiscal_status' => 'emitted',
            'invoice_key' => '352602' . str_pad($order->external_id, 38, '0', STR_PAD_LEFT), // Chave simulada
            'emitted_at' => now(),
        ]);

        Log::info("Nota Fiscal emitida com sucesso para o pedido {$order->external_id}");
    }

    private function handleError(Order $order, string $message)
    {
        $order->update([
            'fiscal_status' => 'error',
            'last_error' => $message
        ]);

        Log::error("Falha na emissão fiscal {$order->external_id}: {$message}");
    }
}
