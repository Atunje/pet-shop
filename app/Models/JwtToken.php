<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JwtToken extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'token_title',
        'unique_id',
        'expires_at',
        'is_valid',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'is_valid',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'refreshed_at' => 'datetime',
    ];

    /**
     * Makes token invalid when user logs out
     *
     * @return bool
     */
    public function invalidate(): bool
    {
        $this->is_valid = false;
        return $this->save();
    }

    /**
     * Checks the validity status of token
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->is_valid;
    }

    /**
     * Update last time used
     *
     * @return bool
     */
    public function saveLastUsedTime()
    {
        $this->last_used_at = now();
        return $this->save();
    }
}
