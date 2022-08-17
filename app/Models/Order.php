<?php

namespace App\Models;

use App\Traits\FilterableModel;
use App\Traits\HasUUIDField;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, HasUUIDField, FilterableModel, SoftDeletes;

    protected $fillable = [
        'order_status_uuid',
        'payment_uuid',
        'products',
        'address'
    ];

    protected $casts = [
        'products' => 'array',
        'address' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid','uuid');
    }

    public function order_status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_uuid','uuid');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_uuid','uuid');
    }

    public static function getAll($filter_params, $per_pg): LengthAwarePaginator
    {
        return self::getRecords($filter_params, $per_pg, ['user_uuid']);
    }
}
