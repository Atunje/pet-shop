<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_user_can_view_product_listing(): void
    {
        $response = $this->get(route('products'), $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }

    public function test_admin_can_view_product_listing(): void
    {
        $response = $this->get(route('products'), $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }

    public function test_admin_can_create_product()
    {
        $product = Product::factory()->make();
        $product->metadata = json_encode($product->metadata);

        $response = $this->post(route('product.create'), $product->toArray(), $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK);

        $product = Product::where('title', $product->title)->first();
        $this->assertNotNull($product);
    }

    public function test_user_cannot_create_product()
    {
        $product = Product::factory()->make();
        $product->metadata = json_encode($product->metadata);

        $response = $this->post(route('product.create'), $product->toArray(), $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $product = Product::where('title', $product->title)->first();
        $this->assertNull($product);
    }

    public function test_admin_can_update_product()
    {
        //create a product
        $product = Product::factory()->create();
        $update = Product::factory()->make();
        $update->metadata = json_encode($update->metadata);

        $response = $this->put(route('product.update', ['product' => $product->uuid]), $update->toArray(), $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK);

        $product = Product::find($product->id);
        $this->assertEquals($product->title, $update->title);
        $this->assertEquals($product->price, $update->price);
        $this->assertEquals($product->description, $update->description);
        $this->assertEquals($product->category_uuid, $update->category_uuid);
        $this->assertEquals(json_encode($product->metadata), $update->metadata);
    }

    public function test_user_cannot_update_product()
    {
        //create a product
        $product = Product::factory()->create();
        $update = Product::factory()->make();
        $update->metadata = json_encode($update->metadata);

        $response = $this->put(route('product.update', ['product' => $product->uuid]), $update->toArray(), $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $product = Product::find($product->id);
        $this->assertNotEquals($product->title, $update->title);
    }

    public function test_admin_can_view_product()
    {
        $product = Product::factory()->create();
        $response = $this->get(route('product.show', ['product' => $product->uuid]), $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)->assertJsonPath('data.uuid', $product->uuid);
    }

    public function test_user_can_view_product()
    {
        $product = Product::factory()->create();
        $response = $this->get(route('product.show', ['product' => $product->uuid]), $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)->assertJsonPath('data.uuid', $product->uuid);
    }

    public function test_admin_can_delete_product()
    {
        $product = Product::factory()->create();
        $response = $this->delete(route('product.delete', ['product' => $product->uuid]), [], $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK);

        $product = Product::find($product->id);
        $this->assertNull($product);
    }

    public function test_user_cannot_delete_product()
    {
        $product = Product::factory()->create();
        $response = $this->delete(route('product.delete', ['product' => $product->uuid]), [], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $product = Product::find($product->id);
        $this->assertNotNull($product);
    }
}
