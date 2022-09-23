<?php

namespace App\Extensions;

use App\DTOs\IssuedToken;
use App\Models\JwtToken;

abstract class JWTLibraryClient
{
    /**
     * JWT issued by.
     *
     * @var string
     */
    protected string $issuer;

    /**
     * JWT Expiry in seconds.
     *
     * @var int
     */
    protected int $expires_in;

    /**
     * JWT private key.
     *
     * @var string
     */
    protected string $private_key;

    /**
     * JWT public key.
     *
     * @var string
     */
    protected string $public_key;

    public function __construct()
    {
        $this->private_key = strval(config('jwt.secret_key'));
        $this->public_key = strval(config('jwt.public_key'));
        $this->issuer = strval(config('jwt.app_url'));
        $this->expires_in = intval(config('jwt.expires_in'));

        $this->configure();
    }

    /**
     * Get the stored jwtToken if token is valid.
     *
     * @param string $token
     * @return JwtToken|null
     */
    abstract public function getJwtToken(string $token): ?JwtToken;

    /**
     * Issue the jwt and return the string.
     *
     * @param string $user_identifier
     * @return IssuedToken
     */
    abstract public function issueToken(string $user_identifier): IssuedToken;

    /**
     * Initial configuration.
     *
     * @return void
     */
    abstract protected function configure(): void;

    /**
     * Get stored jwt token.
     *
     * @param mixed $unique_id
     * @return JwtToken|null
     */
    protected function getStoredJwtToken(mixed $unique_id): ?JwtToken
    {
        return JwtToken::where('unique_id', $unique_id)->first();
    }
}
