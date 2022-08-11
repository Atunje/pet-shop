<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class AdminTest extends TestCase
{
    const ADMIN_ENDPOINT = '/v1/admin/';


    /**
     * Test the root endpoint
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get(self::ROOT_ENDPOINT);
        $response->assertStatus(200);
    }


    /**
     * Test admin can register
     *
     * @return void
     */
    public function test_admin_registration()
    {
        $user = User::factory()->make()->toArray();
        $user['password'] = 'password1';
        $user['password_confirmation'] = 'password1';
        $user['is_marketing'] = 'is_marketing';

        $response = $this->post(self::ADMIN_ENDPOINT . 'create', $user);
        $response->assertStatus(200);
    }
}
