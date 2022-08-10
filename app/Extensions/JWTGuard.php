<?php

namespace App\Extensions;

use Illuminate\Http\Request;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class JWTGuard implements Guard
{
    use GuardHelpers;

    /**
     * The currently authenticated user.
     *
     * @var Authenticatable|null
     */
    protected $user;


    /**
     * Request
     *
     * @var Request
     */
    private $request;


    /**
     * Jwt implementation class of the selected jwt library
     *
     * @var JWTLibraryClient
     */
    private $jwtLibraryClient;


    public function __construct(UserProvider $provider, Request $request, JWTLibraryClient $jwtLibraryClient)
    {
        $this->provider = $provider;
        $this->request = $request;
        $this->jwtLibraryClient = $jwtLibraryClient;
    }


    public function user(): Authenticatable|null
    {
        if ($this->user === null) {
            $user = null;

            $token = $this->request->bearerToken();

            if (! empty($token)) {
                $user = $this->getTokenUser($token);
            }

            return $this->user = $user;
        }

        return $this->user;
    }


    public function getTokenUser(string $token): ?Authenticatable
    {
        //get the jwt token
        $jwt_token = $this->jwtLibraryClient->getJwtToken($token);

        if($jwt_token !== null) {
            return $this->provider->retrieveById($jwt_token->user_id);
        }

        return null;
    }


    public function validate(array $credentials = []): bool
    {
        $user = $this->provider->retrieveByCredentials($credentials);
        return $user !== null;
    }
}
