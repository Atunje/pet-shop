<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
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
        $response->assertStatus(200)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }



    public function test_creation_of_category()
    {
        $title = fake()->sentence(rand(1,4));
        $response = $this->post(self::CATEGORY_ENDPOINT . "create", [
            'title' => $title
        ], $this->getUserAuthHeaders());
        $response->assertStatus(200);

        $category = Category::where('title', $title);
        $this->assertNotNull($category);
    }


    public function test_updating_of_category()
    {
        //create a category
        $category = Category::factory()->create();
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
        $category = Category::factory()->create();
        $response = $this->get(self::CATEGORY_ENDPOINT . $category->uuid, $this->getUserAuthHeaders());
        $response->assertStatus(200)->assertJsonPath('data.uuid', $category->uuid);;
    }


    public function test_deleting_a_category()
    {
        $category = Category::factory()->create();
        $response = $this->delete(self::CATEGORY_ENDPOINT . $category->uuid, [], $this->getUserAuthHeaders());
        $response->assertStatus(200);

        $category = Category::find($category->id);
        $this->assertNull($category);
    }
}
