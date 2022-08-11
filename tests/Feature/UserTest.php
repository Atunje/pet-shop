<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends TestCase
{
    const USER_ENDPOINT = '/v1/user/';


    /**
     * Test user can register
     *
     * @return void
     */
    public function test_user_registration()
    {
        $user = User::factory()->make()->toArray();
        $user['password'] = 'password1';
        $user['password_confirmation'] = 'password1';
        $user['is_marketing'] = 'is_marketing';

        $response = $this->post(self::USER_ENDPOINT . 'create', $user);

        $response->assertStatus(200);

        //check if access token was created
        $content = json_decode($response->content(), true);
        $data = $content['data'];
        $this->assertArrayHasKey('token', $data);
    }


    public function test_user_can_login()
    {
        //create admin user with default password - password
        $user = User::factory()->create();

        $response = $this->post(self::USER_ENDPOINT . 'login', [
            "email" => $user->email,
            "password" => "password"
        ]);

        $response->assertStatus(200);

        //check if access token was created
        $content = json_decode($response->content(), true);
        $data = $content['data'];
        $this->assertArrayHasKey('token', $data);
    }

    public function test_admin_cannot_login_on_user_route()
    {
        //create user with default password - password
        $user = User::factory()->admin()->create();

        $response = $this->post(self::USER_ENDPOINT . 'login', [
            "email" => $user->email,
            "password" => "password"
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }


    public function test_user_cannot_login_with_invalid_credentials()
    {
        //create user with default password - password
        $user = User::factory()->admin()->create();

        $response = $this->post(self::USER_ENDPOINT . 'login', [
            "email" => $user->email,
            "password" => "wrong_password"
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }


    public function test_admin_can_logout()
    {
        //create user with default password - password
        $user = User::factory()->create();

        $response = $this->post(self::USER_ENDPOINT . 'login', [
            "email" => $user->email,
            "password" => "password"
        ]);

        //get the auth token
        $content = json_decode($response->content(), true);
        $token = $content['data']['token'];

        $this->refreshApplication();

        //try logging out again
        $headers = ["Authorization" => "Bearer $token", "Accept" => "application/json"];
        $response = $this->get(self::USER_ENDPOINT . 'logout', $headers);
        $response->assertStatus(200);
    }
}
