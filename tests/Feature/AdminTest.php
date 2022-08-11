<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/api/v1');
        $response->assertStatus(200);
    }


    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_authenticated_route_without_access_token()
    {
        $response = $this->json('GET', '/api/v1/user');
        $response->assertStatus(401);
    }
}
