<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\JwtToken.
 *
 * @property int $id
 * @property string $unique_id
 * @property int $user_id
 * @property string $token_title
 * @property bool $is_valid
 * @property string|null $restrictions
 * @property string|null $permissions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $refreshed_at
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereIsValid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereRefreshedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereRestrictions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereTokenTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereUniqueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereUserId($value)
 * @mixin \Eloquent
 */
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
     * Makes token invalid when user logs out.
     *
     * @return bool
     */
    public function invalidate(): bool
    {
        $this->is_valid = false;

        return $this->save();
    }

    /**
     * Checks the validity status of token.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->is_valid;
    }

    /**
     * Update last time used.
     *
     * @return bool
     */
    public function saveLastUsedTime(): bool
    {
        $this->last_used_at = now();

        return $this->save();
    }
}
