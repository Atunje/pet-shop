<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Faker\Factory;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

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

        //check if access token was created
        $content = json_decode($response->content(), true);
        $data = $content['data'];
        $this->assertArrayHasKey('token', $data);
    }


    public function test_admin_can_login()
    {
        //create admin user with default password - password
        $user = User::factory()->admin()->create();

        $response = $this->post(self::ADMIN_ENDPOINT . 'login', [
            "email" => $user->email,
            "password" => "password"
        ]);

        $response->assertStatus(200);

        //check if access token was created
        $content = json_decode($response->content(), true);
        $data = $content['data'];
        $this->assertArrayHasKey('token', $data);
    }

    public function test_user_cannot_login_on_admin_route()
    {
        //create user with default password - password
        $user = User::factory()->create();

        $response = $this->post(self::ADMIN_ENDPOINT . 'login', [
            "email" => $user->email,
            "password" => "password"
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }


    public function test_admin_cannot_login_with_invalid_credentials()
    {
        //create user with default password - password
        $user = User::factory()->admin()->create();

        $response = $this->post(self::ADMIN_ENDPOINT . 'login', [
            "email" => $user->email,
            "password" => "wrong_password"
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }


    public function test_admin_can_logout()
    {
        //create user with default password - password
        $user = User::factory()->admin()->create();

        $response = $this->post(self::ADMIN_ENDPOINT . 'login', [
            "email" => $user->email,
            "password" => "password"
        ]);

        //get the auth token
        $content = json_decode($response->content(), true);
        $token = $content['data']['token'];

        $this->refreshApplication();

        //try logging out again
        $headers = ["Authorization" => "Bearer $token", "Accept" => "application/json"];
        $response = $this->get(self::ADMIN_ENDPOINT . 'logout', $headers);
        $response->assertStatus(200);
    }


    public function test_admin_account_cannot_be_edited()
    {
        //create admin user with default password - password
        $user = User::factory()->admin()->create();

        //login
        $response = $this->post(self::ADMIN_ENDPOINT . 'login', [
            "email" => $user->email,
            "password" => "password"
        ]);

        //get the auth token
        $content = json_decode($response->content(), true);
        $token = $content['data']['token'];

        $this->refreshApplication();

        $faker = Factory::create();

        $updated = $user->toArray();
        $updated['phone_number'] = $faker->phoneNumber();
        $updated['address'] = $faker->address();
        $updated['is_marketing'] = "is_marketing";
        $updated['first_name'] = $faker->firstName();
        $updated['last_name'] = $faker->lastName();
        $updated['email'] = $faker->safeEmail();
        $updated['avatar'] = $faker->uuid();
        $updated['password'] = "password";
        $updated['password_confirmation'] = "password";

        //profile endpoint
        $headers = ["Authorization" => "Bearer $token"];
        $response = $this->put(UserTest::USER_ENDPOINT . "edit", $updated, $headers);
        $response->assertStatus(401);

        $user->refresh();
        $this->assertNotEquals($user->first_name, $updated['first_name']);
        $this->assertNotEquals($user->last_name, $updated['last_name']);
        $this->assertNotEquals($user->email, $updated['email']);
        $this->assertNotEquals($user->phone_number, $updated['phone_number']);
        $this->assertNotEquals($user->avatar, $updated['avatar']);
        $this->assertNotEquals($user->address, $updated['address']);
        $this->assertNotEquals($user->is_marketing, 1);
    }

    public function test_admin_account_cannot_be_deleted()
    {
        //create admin user with default password - password
        $user = User::factory()->admin()->create();

        //login
        $response = $this->post(self::ADMIN_ENDPOINT . 'login', [
            "email" => $user->email,
            "password" => "password"
        ]);

        //get the auth token
        $content = json_decode($response->content(), true);
        $token = $content['data']['token'];

        $this->refreshApplication();

        //delete
        $headers = ["Authorization" => "Bearer $token"];
        $response = $this->delete(UserTest::USER_ENDPOINT, $headers);
        $response->assertStatus(401);
    }
}
