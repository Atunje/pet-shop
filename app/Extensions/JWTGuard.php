<?php

namespace App\Extensions;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

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
     */
    private Request $request;

    /**
     * Jwt implementation class of the selected jwt library.
     */
    private JWTLibraryClient $jwtLibraryClient;

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

        if ($jwt_token !== null) {
            $user = $this->provider->retrieveById($jwt_token->user_id);

            $user?->setCurrentJwtToken($jwt_token);

            return $user;
        }

        return null;
    }

    public function validate(array $credentials = []): bool
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        return $user !== null;
    }

    /**
     * Validates user's credentials and returns access token.
     *
     * @param array $credentials
     * @return string|null
     */
    public function attempt(array $credentials = []): ?string
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if ($user !== null && $this->provider->validateCredentials($user, $credentials)) {
            $token = $user->createToken();
            $user->loggedIn();

            return $token;
        }

        return null;
    }

    /**
     * Logs user out.
     *
     * @return bool
     */
    public function logout(): bool
    {
        $user = $this->request->user();

        if ($user !== null) {
            return $user->invalidateToken();
        }

        return true;
    }
}
