<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Brand;
use App\Models\Post;
use App\Models\Promotion;
use Tests\TestCase;

class MainPageTest extends TestCase
{
    //use RefreshDatabase;

    /**
     * @return void
     */
    public function test_promotion_listing(): void
    {
        Promotion::factory(10)->create();
        $response = $this->get(self::MAIN_PAGE_ENDPOINT . 'promotions');
        $response->assertStatus(200)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }

    /**
     * @return void
     */
    public function test_blog_listing(): void
    {
        Post::factory(10)->create();
        $response = $this->get(self::MAIN_PAGE_ENDPOINT . 'blog');
        $response->assertStatus(200)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }

    public function test_viewing_a_blog()
    {
        $post = Post::factory()->create();
        $response = $this->get(self::MAIN_PAGE_ENDPOINT . 'blog/' . $post->uuid);
        $response->assertStatus(200)->assertJsonPath('data.uuid', $post->uuid);
    }
}
