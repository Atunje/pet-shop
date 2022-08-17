<?php

namespace App\Models;

use App\DTOs\FilterParams;
use App\Traits\HasUUIDField;
use App\Traits\FilterableModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Order extends Model
{
    use HasFactory, HasUUIDField, FilterableModel, SoftDeletes;

    protected $fillable = [
        'order_status_uuid',
        'payment_uuid',
        'products',
        'address',
    ];

    protected $casts = [
        'products' => 'array',
        'address' => 'array',
    ];

    /**
     * Get the associated user
     *
     * @return BelongsTo<User, Order>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    /**
     * Get the associated user
     *
     * @return BelongsTo<OrderStatus, Order>
     */
    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_uuid', 'uuid');
    }

    /**
     * Get the associated user
     *
     * @return BelongsTo<Payment, Order>
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_uuid', 'uuid');
    }

    /**
     * Get all the orders
     *
     * @param FilterParams $filter_params
     * @return LengthAwarePaginator
     * @throws \Exception
     */
    public static function getAll($filter_params): LengthAwarePaginator
    {
        return self::getRecords($filter_params, ['user_uuid']);
    }
}
