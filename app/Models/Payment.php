<?php

namespace App\Models;

use App\Traits\HasUUIDField;
use App\Traits\FilterableModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, HasUUIDField, FilterableModel, SoftDeletes;

    protected $casts = [
        'details' => 'array',
    ];

    protected $fillable = [
        'type',
        'details',
    ];
}
