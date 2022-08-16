<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\OrderStatus;
use Tests\TestCase;

class OrderStatusTest extends TestCase
{
    //use RefreshDatabase;


    /**
     * @return void
     */
    public function test_order_status_listing(): void
    {
        $response = $this->get(self::ORDER_STATUS_ENDPOINT, $this->getUserAuthHeaders());
        $response->assertStatus(200)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }



    public function test_creation_of_order_status()
    {
        $title = fake()->sentence(rand(1,4));
        $response = $this->post(self::ORDER_STATUS_ENDPOINT . "create", [
            'title' => $title
        ], $this->getUserAuthHeaders());
        $response->assertStatus(200);

        $order_status = OrderStatus::where('title', $title);
        $this->assertNotNull($order_status);
    }


    public function test_updating_of_order_status()
    {
        //create a order_status
        $order_status = OrderStatus::factory()->create();
        $new_title = fake()->sentence(rand(1,4));

        $response = $this->put(self::ORDER_STATUS_ENDPOINT . $order_status->uuid, [
            'title' => $new_title
        ], $this->getUserAuthHeaders());
        $response->assertStatus(200);

        $order_status->refresh();
        $this->assertEquals($order_status->title, $new_title);
    }


    public function test_viewing_of_a_order_status()
    {
        $order_status = OrderStatus::factory()->create();
        $response = $this->get(self::ORDER_STATUS_ENDPOINT . $order_status->uuid, $this->getUserAuthHeaders());
        $response->assertStatus(200)->assertJsonPath('data.uuid', $order_status->uuid);
    }


    public function test_deleting_a_order_status()
    {
        $this->withoutExceptionHandling();

        $order_status = OrderStatus::factory()->create();
        $response = $this->delete(self::ORDER_STATUS_ENDPOINT . $order_status->uuid, [], $this->getUserAuthHeaders());
        $response->assertStatus(200);

        $order_status = OrderStatus::find($order_status->id);
        $this->assertNull($order_status);
    }
}
