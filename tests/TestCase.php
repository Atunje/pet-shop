<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    const ROOT_ENDPOINT = '/v1';
    const ADMIN_ENDPOINT = '/v1/admin/';
    const USER_ENDPOINT = '/v1/user/';
    const CATEGORY_ENDPOINT = '/v1/category/';
    const BRAND_ENDPOINT = '/v1/brand/';
    const PRODUCT_ENDPOINT = '/v1/product/';
    const ORDER_STATUS_ENDPOINT = '/v1/order-status/';

    /**
     * Log an admin user in and return the jwt
     *
     * @param bool $is_admin
     * @param User|null $user
     * @return mixed
     */
    protected function getAuthToken(bool $is_admin = false, ?User $user = null)
    {
        //create admin user with default password - password
        if($user === null) {
            $user = $is_admin ? User::factory()->admin()->create() : User::factory()->create();
        }

        $endpoint = $is_admin ? self::ADMIN_ENDPOINT : self::USER_ENDPOINT;
        $response = $this->post($endpoint . 'login', [
            "email" => $user->email,
            "password" => "password"
        ]);

        $content = json_decode($response->content(), true);

        $this->refreshApplication();

        return $content['data']['token'];
    }


    protected function getAdminAuthHeaders(?User $user = null): array
    {
        return ["Authorization" => "Bearer " . $this->getAuthToken(is_admin:true, user:$user)];
    }


    protected function getUserAuthHeaders(?User $user = null): array
    {
        return ["Authorization" => "Bearer " . $this->getAuthToken(user:$user)];
    }
}
