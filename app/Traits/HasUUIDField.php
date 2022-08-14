<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUUIDField
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            $user->uuid = Str::uuid()->toString();
        });
    }
}
