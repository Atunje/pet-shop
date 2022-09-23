<?php

namespace App\Http\Services\V1;

use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Register new admin.
     *
     * @param array $data
     * @return UserResource
     * @throws Exception
     */
    public function registerAdmin(array $data): UserResource
    {
        $user = $this->create($data, true);

        return new UserResource($user);
    }

    /**
     * Register new user.
     *
     * @param array $data
     * @return UserResource
     * @throws Exception
     */
    public function registerUser(array $data): UserResource
    {
        $user = $this->create($data);

        return new UserResource($user);
    }

    /**
     * Create a user record.
     *
     * @param array $data
     * @param bool $is_admin
     * @return User
     *@throws Exception
     */
    private function create(array $data, bool $is_admin = false): User
    {
        $user = new User($data);
        $user->is_admin = $is_admin;
        $user->is_marketing = ! empty($data['is_marketing']);
        $user->password = Hash::make($data['password']);

        if ($user->save()) {
            return $user;
        }

        //throw new UserCouldNotBeCreatedException($user);
        throw new Exception('User could not be created');
    }

    /**
     * Validates admin credentials and returns access token.
     *
     * @param array $credentials
     * @return string|null
     */
    public function adminLogin(array $credentials): ?string
    {
        $credentials['is_admin'] = true;

        return Auth::attempt($credentials);
    }

    /**
     * Validates user credentials and returns access token.
     *
     * @param array $credentials
     * @return string|null
     */
    public function userLogin(array $credentials): ?string
    {
        $credentials['is_admin'] = false;

        return Auth::attempt($credentials);
    }

    /**
     * Updates user record.
     *
     * @param User $user
     * @param array $data
     * @return bool
     */
    public function update(User $user, array $data): bool
    {
        $data['is_marketing'] = ! empty($data['is_marketing']);

        return $user->update($data);
    }

    /**
     * Deletes user record.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }
}
