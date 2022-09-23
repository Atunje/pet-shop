<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user can register.
     *
     * @return void
     */
    public function test_user_registration(): void
    {
        $user = User::factory()->make()->toArray();
        $user['password'] = 'password1';
        $user['password_confirmation'] = 'password1';
        $user['is_marketing'] = 'is_marketing';

        $response = $this->post(route('user.create'), $user);

        $response->assertStatus(Response::HTTP_OK);

        //check if access token was created
        $content = json_decode($response->content(), true);
        $data = $content['data'];
        $this->assertArrayHasKey('token', $data);
    }

    public function test_user_can_login()
    {
        //create admin user with default password - password
        $user = User::factory()->create();

        $response = $this->post(route('user.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_OK);

        //check if access token was created
        $content = json_decode($response->content(), true);
        $data = $content['data'];
        $this->assertArrayHasKey('token', $data);
    }

    public function test_admin_cannot_login_on_user_route()
    {
        //create user with default password - password
        $user = User::factory()->admin()->create();

        $response = $this->post(route('user.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        //create user with default password - password
        $user = User::factory()->admin()->create();

        $response = $this->post(route('user.login'), [
            'email' => $user->email,
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_user_can_logout()
    {
        $response = $this->get(route('user.logout'), $this->getUserAuthHeaders());
        $response->assertStatus(200);
    }

    public function test_user_can_view_own_profile()
    {
        $user = User::factory()->create();

        $response = $this->get(route('user.profile'), $this->getUserAuthHeaders($user));
        $response->assertStatus(Response::HTTP_OK);

        //check if the email matches
        $content = json_decode($response->content(), true);
        $email = $content['data']['email'];
        $this->assertEquals($user->email, $email);
    }

    public function test_user_can_edit_own_profile()
    {
        $user = User::factory()->create();
        $user_arr = $user->toArray();

        $updated = User::factory()->marketing()->make()->toArray();
        $updated['is_marketing'] = 'is_marketing';
        $updated['password'] = 'password';
        $updated['password_confirmation'] = 'password';
        $updated = array_merge($user_arr, $updated);

        $response = $this->put(route('user.update'), $updated, $this->getUserAuthHeaders($user));
        $response->assertStatus(Response::HTTP_OK);

        $user->refresh();
        $this->assertEquals($user->first_name, $updated['first_name']);
        $this->assertEquals($user->last_name, $updated['last_name']);
        $this->assertEquals($user->email, $updated['email']);
        $this->assertEquals($user->phone_number, $updated['phone_number']);
        $this->assertEquals($user->avatar, $updated['avatar']);
        $this->assertEquals($user->address, $updated['address']);
        $this->assertEquals($user->is_marketing, 1);
    }

    public function test_user_can_delete_own_account()
    {
        $headers = $this->getUserAuthHeaders();
        $response = $this->delete(route('user.delete'), [], $headers);
        $response->assertStatus(Response::HTTP_OK);

        $this->refreshApplication();

        //test if account is still assessible
        $response = $this->get(route('user.profile'), $headers);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
