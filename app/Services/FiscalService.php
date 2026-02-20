<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use App\Utils\Utils;

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

            $payload = $this->prepareWebManiaPayload($order);

            Log::info("Payload gerado com sucesso para o cliente: " . $payload['rps'][0]['tomador']['razao_social']);

            $this->handleSuccess($order);

        } catch (\Exception $e) {
            $this->handleError($order, $e->getMessage());
        }
    }

    private function prepareWebManiaPayload(Order $order): array
    {
        $config = config('fiscal');

        return [
            'ambiente' => 2, // 1 = Produção, 2 = Homologação
            'rps' => [
                [
                    'servico' => [
                        'valor_servicos' => (float) $order->total,
                        'discriminacao'  => "Prestação de serviço ref. ao pedido #{$order->external_id}",
                        'impostos' => [
                            'ir'  => $config['ir'],
                            'iss' => $config['iss']
                        ],
                        'codigo_servico' => $config['codigo_servico'],
                        'iss_retido'     => $config['iss_retido'],
                        'codigo_cnae'    => $config['cnae'],
                        'informacoes_complementares' => 'Pagamento via Gateway Online'
                    ],
                    'tomador' => [
                        'razao_social' => $order->billing_name,
                        'cnpj'         => Utils::onlyNumbers($order->document ?? '00000000000'),
                        'cep'          => Utils::onlyNumbers($order->raw_data['billing']['postcode'] ?? '00000000'),
                        'endereco'     => $order->billing_address,
                        'numero'       => $order->address_number ?? 'S/N',
                        'bairro'       => $order->address_neighbor ?? 'Centro',
                        'cidade'       => $order->raw_data['billing']['city'] ?? 'Não informada',
                        'uf'           => $order->raw_data['billing']['state'] ?? 'BA'
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
