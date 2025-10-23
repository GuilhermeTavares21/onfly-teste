<?php
namespace App\Repositories;

use App\Models\Pedido;

class PedidoRepository
{
    // Coleta todos os pedidos por usuário único com filtros
    public function allByUser($userId, $filters = [])
    {
        $query = Pedido::where('user_id', $userId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['destino'])) {
            $query->where('destino', 'like', "%{$filters['destino']}%");
        }

        if (!empty($filters['inicio']) && !empty($filters['fim'])) {
            $query->whereBetween('data_ida', [$filters['inicio'], $filters['fim']]);
        }
        
        return $query->get();
    }

    // Coleta todos os pedidos de todos os usuários com filtros
    public function all($filters = [])
    {
        $query = Pedido::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['destino'])) {
            $query->where('destino', 'like', "%{$filters['destino']}%");
        }

        if (!empty($filters['inicio']) && !empty($filters['fim'])) {
            $query->whereBetween('data_ida', [$filters['inicio'], $filters['fim']]);
        }

        if (!empty($filters['usuario'])) {
            $query->whereHas('user', function($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['usuario']}%");
        });
    }

        return $query->get();
    }
    public function findById($id, $userId)
    {
        return Pedido::where('user_id', $userId)->findOrFail($id);
    }

    public function create(array $data)
    {
        return Pedido::create($data);
    }

    public function updateStatus($pedido, $status)
    {
        $pedido->update(['status' => $status]);
        return $pedido;
    }
}
