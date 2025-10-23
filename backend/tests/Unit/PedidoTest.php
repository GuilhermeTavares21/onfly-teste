<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pedido;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PedidoTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_pedido()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $data = [
            'destino' => 'Onfly Jr',
            'data_ida' => '2025-12-01',
            'data_volta' => '2025-12-05',
        ];

        $response = $this->postJson('/api/pedidos', $data);

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

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/pedidos');

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $pedido->id]);
    }

    public function test_admin_can_update_status()
    {
        $admin = User::factory()->create(['is_admin' => 1]);
        $pedido = Pedido::factory()->create(['status' => 'solicitado']);

        Sanctum::actingAs($admin);

        $response = $this->patchJson("/api/pedidos/{$pedido->id}/status", [
            'status' => 'aprovado'
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => 'aprovado']);
    }

    public function test_non_admin_cannot_update_status()
    {
        $user = User::factory()->create(['is_admin' => 0]);
        $pedido = Pedido::factory()->create(['status' => 'solicitado']);

        Sanctum::actingAs($user);

        $response = $this->patchJson("/api/pedidos/{$pedido->id}/status", [
            'status' => 'aprovado'
        ]);

        $response->assertStatus(400)
                 ->assertJsonFragment([
                     'error' => 'É necessário ser um administrador para alterar status de um pedido.'
                 ]);

        $this->assertDatabaseHas('pedidos', ['id' => $pedido->id, 'status' => 'solicitado']);
    }

    public function test_cannot_cancel_approved_pedido()
    {
        $admin = User::factory()->create(['is_admin' => 1]);
        $pedido = Pedido::factory()->create(['status' => 'aprovado']);

        Sanctum::actingAs($admin);

        $response = $this->patchJson("/api/pedidos/{$pedido->id}/status", [
            'status' => 'cancelado'
        ]);

        $response->assertStatus(400)
                 ->assertJsonFragment([
                     'error' => 'Não é possível cancelar um pedido já aprovado.'
                 ]);

        $this->assertDatabaseHas('pedidos', ['id' => $pedido->id, 'status' => 'aprovado']);
    }
}
