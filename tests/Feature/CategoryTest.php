<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Brand;
use Exception;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    //use RefreshDatabase;


    /**
     * @return void
     */
    public function test_category_listing(): void
    {
        $response = $this->get(self::CATEGORY_ENDPOINT, $this->getUserAuthHeaders());
        $response->assertStatus(200);
    }



    public function test_creation_of_category()
    {
        $title = fake()->sentence(rand(1,4));
        $response = $this->post(self::CATEGORY_ENDPOINT . "create", [
            'title' => $title
        ], $this->getUserAuthHeaders());
        $response->assertStatus(200);

        $category = Brand::where('title', $title);
        $this->assertNotNull($category);
    }


    public function test_updating_of_category()
    {
        //create a category
        $category = Brand::factory()->create();
        $new_title = fake()->sentence(rand(1,4));

        $response = $this->put(self::CATEGORY_ENDPOINT . $category->uuid, [
            'title' => $new_title
        ], $this->getUserAuthHeaders());
        $response->assertStatus(200);

        $category->refresh();
        $this->assertEquals($category->title, $new_title);
    }


    public function test_viewing_of_a_category()
    {
        $category = Brand::factory()->create();
        $response = $this->get(self::CATEGORY_ENDPOINT . $category->uuid, $this->getUserAuthHeaders());
        $response->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')->has('uuid', $category->uuid)
            );
    }


    public function test_deleting_a_category()
    {
        $category = Brand::factory()->create();
        $response = $this->delete(self::CATEGORY_ENDPOINT . $category->uuid, [], $this->getUserAuthHeaders());
        $response->assertStatus(200);

        $category = Brand::find($category->id);
        $this->assertNull($category);
    }
}
