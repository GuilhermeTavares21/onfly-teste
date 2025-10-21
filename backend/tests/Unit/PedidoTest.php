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

        $this->assertDatabaseHas('pedidos', ['destino' => 'Onfly Jr', 'user_id' => $user->id]);
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

    public function test_user_can_update_status()
    {
        $user = User::factory()->create();
        $pedido = Pedido::factory()->create(['user_id' => $user->id, 'status' => 'solicitado']);

        Sanctum::actingAs($user);

        $response = $this->patchJson("/api/pedidos/{$pedido->id}/status", ['status' => 'aprovado']);

        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => 'aprovado']);
    }
}
