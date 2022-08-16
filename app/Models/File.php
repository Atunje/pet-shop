<?php

namespace App\Models;

use App\Traits\HasUUIDField;
use App\Traits\FilterableModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory, HasUUIDField, FilterableModel;

    protected $fillable = [
        'name', 'size', 'type', 'path'
    ];
}
