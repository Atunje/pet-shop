<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Brand;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BrandTest extends TestCase
{
    //use RefreshDatabase;


    /**
     * @return void
     */
    public function test_brand_listing(): void
    {
        $response = $this->get(self::BRAND_ENDPOINT, $this->getUserAuthHeaders());
        $response->assertStatus(200);
    }



    public function test_creation_of_brand()
    {
        $title = fake()->sentence(rand(1,4));
        $response = $this->post(self::BRAND_ENDPOINT . "create", [
            'title' => $title
        ], $this->getUserAuthHeaders());
        $response->assertStatus(200);

        $brand = Brand::where('title', $title);
        $this->assertNotNull($brand);
    }


    public function test_updating_of_brand()
    {
        //create a brand
        $brand = Brand::factory()->create();
        $new_title = fake()->sentence(rand(1,4));

        $response = $this->put(self::BRAND_ENDPOINT . $brand->uuid, [
            'title' => $new_title
        ], $this->getUserAuthHeaders());
        $response->assertStatus(200);

        $brand->refresh();
        $this->assertEquals($brand->title, $new_title);
    }


    public function test_viewing_of_a_brand()
    {
        $brand = Brand::factory()->create();
        $response = $this->get(self::BRAND_ENDPOINT . $brand->uuid, $this->getUserAuthHeaders());
        $response->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
            $json->has('data')->has('uuid', $brand->uuid)
            );
    }


    public function test_deleting_a_brand()
    {
        $brand = Brand::factory()->create();
        $response = $this->delete(self::BRAND_ENDPOINT . $brand->uuid, [], $this->getUserAuthHeaders());
        $response->assertStatus(200);

        $brand = Brand::find($brand->id);
        $this->assertNull($brand);
    }
}
