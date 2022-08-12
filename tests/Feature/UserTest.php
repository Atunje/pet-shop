<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Faker\Factory;
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


    public function test_user_can_logout()
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


    public function test_user_can_view_own_profile()
    {
        //create user with default password - password
        $user = User::factory()->create();

        //login
        $response = $this->post(self::USER_ENDPOINT . 'login', [
            "email" => $user->email,
            "password" => "password"
        ]);

        //get the auth token
        $content = json_decode($response->content(), true);
        $token = $content['data']['token'];

        $this->refreshApplication();

        //profile endpoint
        $headers = ["Authorization" => "Bearer $token"];
        $response = $this->get(self::USER_ENDPOINT, $headers);
        $response->assertStatus(200);

        //check if the email matches
        $content = json_decode($response->content(), true);
        $email = $content['data']['email'];
        $this->assertEquals($user->email, $email);
    }


    public function test_user_can_edit_own_profile()
    {
        //create user with default password - password
        $user = User::factory()->create();

        //login
        $response = $this->post(self::USER_ENDPOINT . 'login', [
            "email" => $user->email,
            "password" => "password"
        ]);

        //get the auth token
        $content = json_decode($response->content(), true);
        $token = $content['data']['token'];

        $this->refreshApplication();

        $faker = Factory::create();

        $updated = $user;
        $updated->phone_number = $faker->phoneNumber();
        $updated->address = $faker->address();
        $updated->is_marketing = "is_marketing";
        $updated->first_name = $faker->firstName();
        $updated->last_name = $faker->lastName();
        $updated->email = $faker->safeEmail();
        $updated->avatar = $faker->uuid();

        //profile endpoint
        $headers = ["Authorization" => "Bearer $token"];
        $response = $this->put(self::USER_ENDPOINT . "edit", $updated->toArray(), $headers);
        $response->assertStatus(200);

        $user->fresh();
        $this->assertEquals($user->first_name, $updated->first_name);
        $this->assertEquals($user->last_name, $updated->last_name);
        $this->assertEquals($user->email, $updated->email);
        $this->assertEquals($user->phone_number, $updated->phone_number);
        $this->assertEquals($user->avatar, $updated->avatar);
        $this->assertEquals($user->address, $updated->address);
        $this->assertTrue($user->is_marketing);
    }
}
