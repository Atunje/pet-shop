<?php

namespace App\Models;

use App\Traits\FilterableModel;
use App\Traits\HasUUIDField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, HasUUIDField, FilterableModel, SoftDeletes;

    protected $casts = [
        'details' => 'array',
    ];

    protected $fillable = [
        'type',
        'details'
    ];
}
