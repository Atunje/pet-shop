<?php

namespace Tests\Feature;

use App\Models\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class OrderStatusTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_user_can_view_order_status_listing(): void
    {
        $response = $this->get(route('order-statuses'), $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }

    public function test_admin_can_view_order_status_listing(): void
    {
        $response = $this->get(route('order-statuses'), $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }

    public function test_admin_can_create_order_status()
    {
        $title = fake()->sentence(rand(1, 4));
        $response = $this->post(route('order-status.create'), [
            'title' => $title,
        ], $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK);

        $order_status = OrderStatus::where('title', $title)->first();
        $this->assertNotNull($order_status);
    }

    public function test_user_cannot_create_order_status()
    {
        $title = fake()->sentence(rand(1, 4));
        $response = $this->post(route('order-status.create'), [
            'title' => $title,
        ], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $order_status = OrderStatus::where('title', $title)->first();
        $this->assertNull($order_status);
    }

    public function test_admin_can_update_order_status()
    {
        //create a order_status
        $order_status = OrderStatus::factory()->create();
        $new_title = fake()->sentence(rand(1, 4));

        $response = $this->put(route('order-status.update', ['order_status' => $order_status->uuid]), [
            'title' => $new_title,
        ], $this->getAdminAuthHeaders());
        $response->assertStatus(200);

        $order_status = OrderStatus::find($order_status->id);
        $this->assertEquals($order_status->title, $new_title);
    }

    public function test_user_cannot_update_order_status()
    {
        //create a order_status
        $order_status = OrderStatus::factory()->create();
        $new_title = fake()->sentence(rand(1, 4));

        $response = $this->put(route('order-status.update', ['order_status' => $order_status->uuid]), [
            'title' => $new_title,
        ], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $order_status = OrderStatus::find($order_status->id);
        $this->assertNotEquals($order_status->title, $new_title);
    }

    public function test_admin_can_view_order_status()
    {
        $order_status = OrderStatus::factory()->create();
        $response = $this->get(route('order-status.show', ['order_status' => $order_status->uuid]), $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)->assertJsonPath('data.uuid', $order_status->uuid);
    }

    public function test_user_can_view_order_status()
    {
        $order_status = OrderStatus::factory()->create();
        $response = $this->get(route('order-status.show', ['order_status' => $order_status->uuid]), $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)->assertJsonPath('data.uuid', $order_status->uuid);
    }

    public function test_admin_can_delete_order_status()
    {
        $order_status = OrderStatus::factory()->create();
        $response = $this->delete(route('order-status.delete', ['order_status' => $order_status->uuid]), [], $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK);

        $order_status = OrderStatus::find($order_status->id);
        $this->assertNull($order_status);
    }

    public function test_user_cannot_delete_order_status()
    {
        $order_status = OrderStatus::factory()->create();
        $response = $this->delete(route('order-status.delete', ['order_status' => $order_status->uuid]), [], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $order_status = OrderStatus::find($order_status->id);
        $this->assertNotNull($order_status);
    }
}
