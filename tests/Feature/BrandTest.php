<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Brand;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BrandTest extends TestCase
{
    //use RefreshDatabase;


    /**
     * @return void
     */
    public function test_user_can_view_brand_listing(): void
    {
        $response = $this->get(self::BRAND_ENDPOINT, $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }


    public function test_admin_can_view_brand_listing(): void
    {
        $response = $this->get(self::BRAND_ENDPOINT, $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }



    public function test_admin_can_create_brand()
    {
        $title = fake()->sentence(rand(1,4));
        $response = $this->post(self::BRAND_ENDPOINT . "create", [
            'title' => $title
        ], $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK);

        $brand = Brand::where('title', $title)->first();
        $this->assertNotNull($brand);
    }


    public function test_user_cannot_create_brand()
    {
        $title = fake()->sentence(rand(1,4));
        $response = $this->post(self::BRAND_ENDPOINT . "create", [
            'title' => $title
        ], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $brand = Brand::where('title', $title)->first();
        $this->assertNull($brand);
    }


    public function test_admin_can_update_brand()
    {
        //create a brand
        $brand = Brand::factory()->create();
        $new_title = fake()->sentence(rand(1,4));

        $response = $this->put(self::BRAND_ENDPOINT . $brand->uuid, [
            'title' => $new_title
        ], $this->getAdminAuthHeaders());
        $response->assertStatus(200);

        $brand = Brand::find($brand->id);
        $this->assertEquals($brand->title, $new_title);
    }


    public function test_user_cannot_update_brand()
    {
        //create a brand
        $brand = Brand::factory()->create();
        $new_title = fake()->sentence(rand(1,4));

        $response = $this->put(self::BRAND_ENDPOINT . $brand->uuid, [
            'title' => $new_title
        ], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $brand = Brand::find($brand->id);
        $this->assertNotEquals($brand->title, $new_title);
    }


    public function test_admin_can_view_brand()
    {
        $brand = Brand::factory()->create();
        $response = $this->get(self::BRAND_ENDPOINT . $brand->uuid, $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK);//->assertJsonPath('data.uuid', $brand->uuid);
    }


    public function test_user_can_view_brand()
    {
        $brand = Brand::factory()->create();
        $response = $this->get(self::BRAND_ENDPOINT . $brand->uuid, $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)->assertJsonPath('data.uuid', $brand->uuid);
    }

    public function test_admin_can_delete_brand()
    {
        $brand = Brand::factory()->create();
        $response = $this->delete(self::BRAND_ENDPOINT . $brand->uuid, [], $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK);

        $brand = Brand::find($brand->id);
        $this->assertNull($brand);
    }


    public function test_user_cannot_delete_brand()
    {
        $brand = Brand::factory()->create();
        $response = $this->delete(self::BRAND_ENDPOINT . $brand->uuid, [], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $brand = Brand::find($brand->id);
        $this->assertNotNull($brand);
    }
}
