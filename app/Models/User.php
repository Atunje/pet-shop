<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasJwtTokens;
use App\Traits\HasUUIDField;
use App\Traits\FilterableModel;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        'is_admin',
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
    ];

    /**
     * Get users
     *
     * @param array $filter_params
     * @param int $per_pg
     * @return LengthAwarePaginator
     */
    public static function getUsers($filter_params, $per_pg): LengthAwarePaginator
    {
        return self::getRecords($filter_params, $per_pg, self::$filterable, ['is_admin' => false]);
    }
}
