<?php

namespace Tests\Feature;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_users_can_view_brand_listing(): void
    {
        //seed the database
        $count = 5;
        Brand::factory()->count($count)->create();

        $response = $this->get(route('brands'));
        $response->assertStatus(Response::HTTP_OK)
            //confirm the records returned
            ->assertJson( fn (AssertableJson $json) =>
                $json->where('success', 1)
                    ->has('data', fn ($json) =>
                        //confirm returned data content
                        $json->has('data')
                            ->where('total', $count)
                            ->hasAll(['data.0.uuid', 'data.0.slug', 'data.0.title'])
                            //confirm data is paginated
                            ->hasAll(['current_page', 'from', 'to', 'per_page'])
                            ->etc()
                    )
                    ->etc()
                );

    }

    public function test_admin_can_create_brand()
    {
        $title = fake()->sentence(rand(1, 4));
        $response = $this->post(route('brand.create'), [
            'title' => $title,
        ], $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson( fn (AssertableJson $json) =>
                $json->where('success', 1)
                    ->has('data', fn ($json) =>
                        $json->hasAll(['uuid', 'slug', 'title'])
                            ->etc()
                        )
                    ->etc()
            );

        $brand = Brand::where('title', $title)->first();
        $this->assertNotNull($brand);
    }

    public function test_user_cannot_create_brand()
    {
        $title = fake()->sentence(rand(1, 4));
        $response = $this->post(route('brand.create'), [
            'title' => $title,
        ], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson( fn (AssertableJson $json) =>
                $json->where('success', 0)
                    ->etc()
            );

        $brand = Brand::where('title', $title)->first();
        $this->assertNull($brand);
    }

    public function test_admin_can_update_brand()
    {
        //create a brand
        $brand = Brand::factory()->create();
        $new_title = fake()->sentence(rand(1, 4));

        $response = $this->put(route('brand.update', ['brand' => $brand->uuid]), [
            'title' => $new_title,
        ], $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson( fn (AssertableJson $json) =>
                $json->where('success', 1)
                    ->where('data.uuid', $brand->uuid)
                    ->where('data.title', $new_title)
                    ->where('data.slug', Str::slug($new_title))
                    ->etc()
                );

        $brand = Brand::find($brand->id);
        $this->assertEquals($brand->title, $new_title);
    }

    public function test_user_cannot_update_brand()
    {
        //create a brand
        $brand = Brand::factory()->create();
        $new_title = fake()->sentence(rand(1, 4));

        $response = $this->put(route('brand.update', ['brand' => $brand->uuid]), [
            'title' => $new_title,
        ], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson( fn (AssertableJson $json) =>
            $json->where('success', 0)
                ->etc()
            );

        $brand = Brand::find($brand->id);
        $this->assertNotEquals($brand->title, $new_title);
    }

    public function test_users_can_view_brand()
    {
        $brand = Brand::factory()->create();
        $response = $this->get(route('brand.show', ['brand' => $brand->uuid]));
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson( fn (AssertableJson $json) =>
            $json->where('success', 1)
                ->where('data.uuid', $brand->uuid)
                ->where('data.title', $brand->title)
                ->where('data.slug', $brand->slug)
                ->etc()
            );
    }

    public function test_admin_can_delete_brand()
    {
        $brand = Brand::factory()->create();
        $response = $this->delete(route('brand.delete', ['brand' => $brand->uuid]), [], $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)->assertJsonPath('success', 1);

        $brand = Brand::find($brand->id);
        $this->assertNull($brand);
    }

    public function test_user_cannot_delete_brand()
    {
        $brand = Brand::factory()->create();
        $response = $this->delete(route('brand.delete', ['brand' => $brand->uuid]), [], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)->assertJsonPath('success', 0);

        $brand = Brand::find($brand->id);
        $this->assertNotNull($brand);
    }
}
