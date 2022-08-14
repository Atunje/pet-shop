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
    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }
}
