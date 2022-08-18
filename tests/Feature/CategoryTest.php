<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    //use RefreshDatabase;


    /**
     * @return void
     */
    public function test_user_can_view_category_listing(): void
    {
        $response = $this->get(self::CATEGORIES_ENDPOINT, $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }


    public function test_admin_can_view_category_listing(): void
    {
        $response = $this->get(self::CATEGORIES_ENDPOINT, $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }



    public function test_admin_can_create_category()
    {
        $title = fake()->sentence(rand(1,4));
        $response = $this->post(self::CATEGORY_ENDPOINT . "create", [
            'title' => $title
        ], $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK);

        $category = Category::where('title', $title)->first();
        $this->assertNotNull($category);
    }


    public function test_user_cannot_create_category()
    {
        $title = fake()->sentence(rand(1,4));
        $response = $this->post(self::CATEGORY_ENDPOINT . "create", [
            'title' => $title
        ], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $category = Category::where('title', $title)->first();
        $this->assertNull($category);
    }


    public function test_admin_can_update_category()
    {
        //create a category
        $category = Category::factory()->create();
        $new_title = fake()->sentence(rand(1,4));

        $response = $this->put(self::CATEGORY_ENDPOINT . $category->uuid, [
            'title' => $new_title
        ], $this->getAdminAuthHeaders());
        $response->assertStatus(200);

        $category = Category::find($category->id);
        $this->assertEquals($category->title, $new_title);
    }


    public function test_user_cannot_update_category()
    {
        //create a category
        $category = Category::factory()->create();
        $new_title = fake()->sentence(rand(1,4));

        $response = $this->put(self::CATEGORY_ENDPOINT . $category->uuid, [
            'title' => $new_title
        ], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $category = Category::find($category->id);
        $this->assertNotEquals($category->title, $new_title);
    }


    public function test_admin_can_view_category()
    {
        $category = Category::factory()->create();
        $response = $this->get(self::CATEGORY_ENDPOINT . $category->uuid, $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)->assertJsonPath('data.uuid', $category->uuid);
    }


    public function test_user_can_view_category()
    {
        $category = Category::factory()->create();
        $response = $this->get(self::CATEGORY_ENDPOINT . $category->uuid, $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)->assertJsonPath('data.uuid', $category->uuid);
    }

    public function test_admin_can_delete_category()
    {
        $category = Category::factory()->create();
        $response = $this->delete(self::CATEGORY_ENDPOINT . $category->uuid, [], $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK);

        $category = Category::find($category->id);
        $this->assertNull($category);
    }


    public function test_user_cannot_delete_category()
    {
        $category = Category::factory()->create();
        $response = $this->delete(self::CATEGORY_ENDPOINT . $category->uuid, [], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $category = Category::find($category->id);
        $this->assertNotNull($category);
    }
}
