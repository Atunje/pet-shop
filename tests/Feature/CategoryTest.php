<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_user_can_view_category_listing(): void
    {
        $response = $this->get(route('categories'), $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }

    public function test_admin_can_view_category_listing(): void
    {
        $response = $this->get(route('categories'), $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }

    public function test_admin_can_create_category()
    {
        $title = fake()->sentence(rand(1, 4));
        $response = $this->post(route('category.create'), [
            'title' => $title,
        ], $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK);

        $category = Category::where('title', $title)->first();
        $this->assertNotNull($category);
    }

    public function test_user_cannot_create_category()
    {
        $title = fake()->sentence(rand(1, 4));
        $response = $this->post(route('category.create'), [
            'title' => $title,
        ], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $category = Category::where('title', $title)->first();
        $this->assertNull($category);
    }

    public function test_admin_can_update_category()
    {
        //create a category
        $category = Category::factory()->create();
        $new_title = fake()->sentence(rand(1, 4));

        $response = $this->put(route('category.update', ['category' =>$category->uuid]), [
            'title' => $new_title,
        ], $this->getAdminAuthHeaders());
        $response->assertStatus(200);

        $category = Category::find($category->id);
        $this->assertEquals($category->title, $new_title);
    }

    public function test_user_cannot_update_category()
    {
        //create a category
        $category = Category::factory()->create();
        $new_title = fake()->sentence(rand(1, 4));

        $response = $this->put(route('category.update', ['category' =>$category->uuid]), [
            'title' => $new_title,
        ], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $category = Category::find($category->id);
        $this->assertNotEquals($category->title, $new_title);
    }

    public function test_admin_can_view_category()
    {
        $category = Category::factory()->create();
        $response = $this->get(route('category.show', ['category' =>$category->uuid]), $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)->assertJsonPath('data.uuid', $category->uuid);
    }

    public function test_user_can_view_category()
    {
        $category = Category::factory()->create();
        $response = $this->get(route('category.show', ['category' =>$category->uuid]), $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)->assertJsonPath('data.uuid', $category->uuid);
    }

    public function test_admin_can_delete_category()
    {
        $category = Category::factory()->create();
        $response = $this->delete(route('category.delete', ['category' =>$category->uuid]), [], $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK);

        $category = Category::find($category->id);
        $this->assertNull($category);
    }

    public function test_user_cannot_delete_category()
    {
        $category = Category::factory()->create();
        $response = $this->delete(route('category.delete', ['category' =>$category->uuid]), [], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $category = Category::find($category->id);
        $this->assertNotNull($category);
    }
}
