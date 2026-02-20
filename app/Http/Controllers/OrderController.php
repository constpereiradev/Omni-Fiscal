<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\OrderService;
use App\Factories\OrderStrategyFactory;
use App\Models\Order;
use App\Services\FiscalService;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly OrderStrategyFactory $orderStrategyFactory,
        private readonly FiscalService $fiscalService
    ) {}

    public function index()
    {
        $orders = Order::orderBy("id", "desc")->get();

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        try {
            $strategy = $this->orderStrategyFactory->make($request);
            $order = $this->orderService->process($request->all(), $strategy);

            Log::info('PEDIDO RECEBIDO!');

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
