<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminTest extends TestCase
{
    //use RefreshDatabase;

    /**
     * Test admin can register
     *
     * @return void
     */
    public function test_admin_registration(): void
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
        $response = $this->get(self::ADMIN_ENDPOINT . 'logout', $this->getAdminAuthHeaders());
        $response->assertStatus(200);
    }


    public function test_admin_account_cannot_be_edited()
    {
        $user = User::factory()->admin()->create();
        $user_arr = $user->toArray();

        $updated = User::factory()->admin_marketing()->make()->toArray();
        $updated['is_marketing'] = 'is_marketing';
        $updated['password'] = 'password';
        $updated['password_confirmation'] = 'password';
        $updated = array_merge($user_arr, $updated);

        $response = $this->put(self::USER_ENDPOINT . "edit", $updated, $this->getAdminAuthHeaders($user));
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
        $response = $this->delete(self::USER_ENDPOINT, [], $this->getAdminAuthHeaders());
        $response->assertStatus(401);
    }

    public function test_admin_can_view_user_listing()
    {
        $response = $this->post(self::ADMIN_ENDPOINT . "user-listing", [], $this->getAdminAuthHeaders());
        $response->assertStatus(200)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }


    public function test_admin_can_edit_user()
    {
        $user = User::factory()->create();
        $user_arr = $user->toArray();

        $updated = User::factory()->marketing()->make()->toArray();
        $updated['is_marketing'] = 'is_marketing';
        $updated['password'] = 'password';
        $updated['password_confirmation'] = 'password';
        $updated = array_merge($user_arr, $updated);

        $response = $this->put(self::ADMIN_ENDPOINT . "user-edit/" . $user->uuid, $updated, $this->getAdminAuthHeaders());
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


    public function test_admin_can_delete_user_account()
    {
        $this->withoutExceptionHandling();

        //create user
        $user = User::factory()->create();

        //delete the user with admin account
        $response = $this->delete(self::ADMIN_ENDPOINT . "user-delete/" . $user->uuid, [], $this->getAdminAuthHeaders());
        $response->assertStatus(200);

        //fetch the user afresh from the db
        $user = User::find($user->id);
        $this->assertNull($user);
    }
}
