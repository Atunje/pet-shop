<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Log an admin user in and return the jwt.
     *
     * @param bool $is_admin
     * @param User|null $user
     * @return mixed
     */
    protected function getAuthToken(bool $is_admin = false, ?User $user = null): mixed
    {
        //create admin/user with default password - password
        if ($user === null) {
            $user = $is_admin ? User::factory()->admin()->create() : User::factory()->create();
        }

        return $user->createToken();
    }

    protected function getAdminAuthHeaders(?User $user = null): array
    {
        return ['Authorization' => 'Bearer '.$this->getAuthToken(is_admin:true, user:$user)];
    }

    protected function getUserAuthHeaders(?User $user = null): array
    {
        return ['Authorization' => 'Bearer '.$this->getAuthToken(user:$user)];
    }
}
