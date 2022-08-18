<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Exception;
use App\DTOs\FilterParams;
use App\Traits\HasJwtTokens;
use App\Traits\HasUUIDField;
use App\Traits\FilterableModel;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class User extends Authenticatable
{
    use HasJwtTokens, HasUUIDField, FilterableModel, SoftDeletes, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'avatar',
        'is_marketing',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, string|bool>
     */
    protected $attributes = [
        'uuid' => '',
        'is_admin' => false,
        'is_marketing' => false,
    ];

    /**
     * Called when user logs in
     *
     * @return void
     */
    public function loggedIn()
    {
        $this->last_login_at = now();
        $this->save();
    }

    /**
     * Checks if user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Run query filters with these columns
     *
     * @var array<int, string>
     */
    private static array $filterable = [
        'first_name',
        'email',
        'phone_number',
        'address',
        'created_at',
        'is_marketing',
        'is_admin',
    ];

    /**
     * @return HasMany<Order>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_uuid', 'uuid');
    }

    /**
     * Get users
     *
     * @param FilterParams $filter_params
     * @return LengthAwarePaginator
     * @throws Exception
     */
    public static function getUsers($filter_params): LengthAwarePaginator
    {
        $filter_params->__set("is_admin", false);
        return self::getRecords($filter_params, self::$filterable);
    }
}
