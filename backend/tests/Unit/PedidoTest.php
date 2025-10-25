<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PedidoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Gera um token JWT e define o header Authorization.
     */
    protected function actingAsJwt(User $user): self
    {
        $token = JWTAuth::fromUser($user);
        return $this->withHeader('Authorization', "Bearer {$token}");
    }

    public function test_user_can_create_pedido()
    {
        $user = User::factory()->create();

        $data = [
            'destino' => 'Onfly Jr',
            'data_ida' => '2025-12-01',
            'data_volta' => '2025-12-05',
        ];

        $response = $this->actingAsJwt($user)
            ->postJson('/api/pedidos', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['destino' => 'Onfly Jr']);

        $this->assertDatabaseHas('pedidos', [
            'destino' => 'Onfly Jr',
            'user_id' => $user->id
        ]);
    }

    public function test_user_can_view_their_pedidos()
    {
        $user = User::factory()->create();
        $pedido = Pedido::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAsJwt($user)
            ->getJson('/api/pedidos');

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $pedido->id]);
    }

    public function test_admin_can_update_status()
    {
        $admin = User::factory()->create(['is_admin' => 1]);
        $pedido = Pedido::factory()->create(['status' => 'solicitado']);

        $response = $this->actingAsJwt($admin)
            ->patchJson("/api/pedidos/{$pedido->id}/status", [
                'status' => 'aprovado'
            ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => 'aprovado']);
    }

    public function test_non_admin_cannot_update_status()
    {
        $user = User::factory()->create(['is_admin' => 0]);
        $pedido = Pedido::factory()->create(['status' => 'solicitado']);

        $response = $this->actingAsJwt($user)
            ->patchJson("/api/pedidos/{$pedido->id}/status", [
                'status' => 'aprovado'
            ]);

        $response->assertStatus(400)
                 ->assertJsonFragment([
                     'error' => 'É necessário ser um administrador para alterar status de um pedido.'
                 ]);

        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'status' => 'solicitado'
        ]);
    }

    public function test_cannot_cancel_approved_pedido()
    {
        $admin = User::factory()->create(['is_admin' => 1]);
        $pedido = Pedido::factory()->create(['status' => 'aprovado']);

        $response = $this->actingAsJwt($admin)
            ->patchJson("/api/pedidos/{$pedido->id}/status", [
                'status' => 'cancelado'
            ]);

        $response->assertStatus(400)
                 ->assertJsonFragment([
                     'error' => 'Não é possível cancelar um pedido já aprovado.'
                 ]);

        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'status' => 'aprovado'
        ]);
    }
}
