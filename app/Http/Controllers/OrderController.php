<?php

namespace App\Http\Controllers;

use App\Strategies\Implementations\WooCommerceStrategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\OrderService;
use App\Factories\OrderStrategyFactory;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService,
    private readonly OrderStrategyFactory $orderStrategyFactory){}

    public function store(Request $request)
    {
        try {
            $strategy = $this->orderStrategyFactory->make($request);
            $order = $this->orderService->process($request->all(), $strategy);


            Log::info('PEDIDO RECEBIDO!', $request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Pedido integrado e aguardando emissão fiscal.',
                'internal_id' => $order->id
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
