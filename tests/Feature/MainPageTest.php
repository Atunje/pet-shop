<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MainPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_promotion_listing(): void
    {
        Promotion::factory(10)->create();
        $response = $this->get(route('promotions'));
        $response->assertStatus(Response::HTTP_OK)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }

    /**
     * @return void
     */
    public function test_blog_listing(): void
    {
        Post::factory(10)->create();
        $response = $this->get(route('blog'));
        $response->assertStatus(Response::HTTP_OK)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }

    public function test_viewing_a_blog()
    {
        $post = Post::factory()->create();
        $response = $this->get(route('blog.show', ['post' => $post->uuid]));
        $response->assertStatus(Response::HTTP_OK)->assertJsonPath('data.uuid', $post->uuid);
    }
}
