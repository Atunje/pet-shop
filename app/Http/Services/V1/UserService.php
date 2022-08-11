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
     * Register new admin
     *
     * @param array $data
     * @return UserResource
     * @throws Exception
     */
    public function registerAdmin($data)
    {
        $user = $this->create($data, true);
        return new UserResource($user);
    }


    /**
     * Create a user record
     *
     * @param array $data
     * @param bool $is_admin
     * @throws Exception
     * @return User
     */
    private function create($data, $is_admin = false): User
    {
        $user = new User($data);
        $user->is_admin = $is_admin;
        $user->is_marketing = !empty($data['is_marketing']);
        $user->password = Hash::make($data['password']);

        if ($user->save()) {
            return $user;
        }

        //throw new UserCouldNotBeCreatedException($user);
        throw new Exception('User could not be created');
    }


    /**
     * Validates admin credentials and returns access token
     *
     * @param array $credentials
     * @return string|null
     */
    public function adminLogin($credentials)
    {
        $credentials['is_admin'] = true;
        return Auth::attempt($credentials);
    }
}
