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
        //TODO: Add docs
        try {
            Log::info(message: "Webmania: Iniciando emissão fiscal do pedido {$order->external_id}");

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

    private function prepareWebManiaPayload(Order $order): array
    {
        return [
            'ambiente' => '{{ambiente}}',
            'rps' => [
                [
                    'servico' => [
                        'valor_servicos' => $order->total,
                        'discriminacao' => 'DESCRIÇÃO DO SERVIÇO PRESTADO',
                        'impostos' => [
                            'ir' => 1.5,
                            'iss' => 5
                        ],
                        'codigo_servico' => '0000',
                        'iss_retido' => 2,
                        'codigo_cnae' => '000000',
                        'informacoes_complementares' => 'REF. MES ANO'
                    ],
                    'tomador' => [
                        'razao_social' => 'RAZAO TOMADOR',
                        'cnpj' => '00000000000000',
                        'cep' => '00000000',
                        'endereco' => 'Rodovia',
                        'numero' => 'NR',
                        'bairro' => 'BAIRRO',
                        'cidade' => 'CIDADE',
                        'uf' => 'UF'
                    ]
                ]
            ]
        ];
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
