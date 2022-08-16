<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use Tests\TestCase;

class ProductTest extends TestCase
{
    //use RefreshDatabase;


    /**
     * @return void
     */
    public function test_product_listing(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->get(self::PRODUCT_ENDPOINT, $this->getUserAuthHeaders());
        $response->assertStatus(200)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }

    public function test_creation_of_product()
    {
        $new = Product::factory()->make();
        $new->metadata = json_encode($new->metadata);

        $response = $this->post(self::PRODUCT_ENDPOINT . "create", $new->toArray(), $this->getUserAuthHeaders());
        $response->assertStatus(200);

        $product = Product::where('title', $new->title);
        $this->assertNotNull($product);
    }


    public function test_updating_a_product()
    {
        //create a product
        $product = Product::factory()->create();
        $updated = Product::factory()->make();
        $updated->metadata = json_encode($updated->metadata);

        $response = $this->put(self::PRODUCT_ENDPOINT . $product->uuid, $updated->toArray(), $this->getUserAuthHeaders());
        $response->assertStatus(200);

        $product->refresh();
        $this->assertEquals($product->title, $updated->title);
        $this->assertEquals($product->price, $updated->price);
        $this->assertEquals($product->description, $updated->description);
        $this->assertEquals($product->metadata, $updated->metadata);
        $this->assertEquals($product->category_uuid, $updated->category_uuid);
    }


    public function test_viewing_of_a_product()
    {
        $product = Product::factory()->create();
        $response = $this->get(self::PRODUCT_ENDPOINT . $product->uuid, $this->getUserAuthHeaders());
        $response->assertStatus(200)->assertJsonPath('data.uuid', $product->uuid);;
    }


    public function test_deleting_a_product()
    {
        $product = Product::factory()->create();
        $response = $this->delete(self::PRODUCT_ENDPOINT . $product->uuid, [], $this->getUserAuthHeaders());
        $response->assertStatus(200);

        $product = Product::find($product->id);
        $this->assertNull($product);
    }
}
