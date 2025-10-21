<?php
namespace App\Http\Controllers;

use App\Http\Requests\PedidoRequest;
use App\Services\PedidoService;
use Illuminate\Support\Facades\Log;

class PedidoController extends Controller
{
    protected $service;

    public function __construct(PedidoService $service)
    {
        $this->service = $service;
    }

    public function index(\Illuminate\Http\Request $request)
    {
        try {
            $pedidos = $this->service->list($request->all());
            return response()->json($pedidos);
        } catch (\Exception $e) {
            Log::error('Erro ao listar pedidos', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro ao listar pedidos'], 500);
        }
    }

    public function store(PedidoRequest $request)
    {
        try {
            $pedido = $this->service->create($request->validated());
            return response()->json($pedido, 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar pedido', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro ao criar pedido'], 500);
        }
    }

    public function show($id)
    {
        try {
            $pedido = $this->service->show($id);
            return response()->json($pedido);
        } catch (\Exception $e) {
            Log::error('Erro ao mostrar pedido', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Pedido não encontrado'], 404);
        }
    }

    public function updateStatus(\Illuminate\Http\Request $request, $id)
    {
        try {
            $pedido = $this->service->updateStatus($id, $request->status);
            return response()->json($pedido);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar status do pedido', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
