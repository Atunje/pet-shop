<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasJwtTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasJwtTokens, SoftDeletes, HasFactory, Notifiable;


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
        'is_marketing'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'is_admin'
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
     * @var array<string, string|boolean>
     */
    protected $attributes = [
        'uuid' => '',
        'is_admin' => false,
        'is_marketing' => false
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
}
