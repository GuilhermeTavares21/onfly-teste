<?php
namespace App\Services;

use App\Mail\PedidoStatusAlteradoMail;
use App\Models\Pedido;
use App\Repositories\PedidoRepository;
use Illuminate\Support\Facades\Auth;
use Mail;

class PedidoService
{
    protected $repository;

    public function __construct(PedidoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list($filters = [])
    {
        if(Auth::user()->is_admin){
            return $this->repository->all($filters);
        } else {
            return $this->repository->allByUser(Auth::id(), $filters);
        }
    }

    public function create($data)
    {
        $data['user_id'] = Auth::id();
        $data['nome_solicitante'] = Auth::user()->name;
        $data['status'] = 'solicitado';

        return $this->repository->create($data);
    }

    public function show($id)
    {
        return $this->repository->findById($id, Auth::id());
    }

    public function updateStatus($id, $status)
    {
        if (!Auth::user()->is_admin) {
            throw new \Exception('É necessário ser um administrador para alterar status de um pedido.');
        }

        $pedido = Pedido::find($id);

        if (!$pedido) {
            throw new \Exception('Não foi possível localizar o pedido com esse ID.');
        }

        if ($pedido->status === 'aprovado' && $status === 'cancelado') {
            throw new \Exception('Não é possível cancelar um pedido já aprovado.');
        }

        $pedidoAtualizado = $this->repository->updateStatus($pedido, $status);

        $this->enviarEmail($pedidoAtualizado);

        return $pedidoAtualizado;
    }

    protected function enviarEmail(Pedido $pedido)
    {
        try {
            $user = $pedido->user;

            if ($user && $user->email) {
                Mail::to($user->email)->send(new PedidoStatusAlteradoMail($pedido, $pedido->status));
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar e-mail do pedido: ' . $e->getMessage());
        }
    }
}
