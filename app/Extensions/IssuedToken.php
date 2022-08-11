<?php

namespace App\Extensions;

class IssuedToken
{
    public function __construct(
        public string $token,
        public string $unique_id,
        public \DateTimeInterface $expires_at
    ) {
        //
    }
}
