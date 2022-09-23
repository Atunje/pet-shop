<?php

namespace App\Models;

use App\DTOs\FilterParams;
use App\Traits\Filterable;
use App\Traits\HasUUIDField;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Order.
 *
 * @property int $id
 * @property string $uuid
 * @property string $user_uuid
 * @property string $order_status_uuid
 * @property string|null $payment_uuid
 * @property array $products
 * @property array $address
 * @property float $delivery_fee
 * @property float $amount
 * @property string|null $shipped_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\OrderStatus $orderStatus
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\OrderFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Query\Builder|Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeliveryFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderStatusUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUuid($value)
 * @method static \Illuminate\Database\Query\Builder|Order withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Order withoutTrashed()
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory, HasUUIDField, Filterable, SoftDeletes;

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
     * Get the associated user.
     *
     * @return BelongsTo<User, Order>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    /**
     * Get the associated user.
     *
     * @return BelongsTo<OrderStatus, Order>
     */
    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_uuid', 'uuid');
    }

    /**
     * Get the associated user.
     *
     * @return BelongsTo<Payment, Order>
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_uuid', 'uuid');
    }

    /**
     * Get all the orders.
     *
     * @param FilterParams $filter_params
     * @return LengthAwarePaginator
     * @throws \Exception
     */
    public static function getAll(FilterParams $filter_params): LengthAwarePaginator
    {
        return self::getRecords($filter_params, ['user_uuid']);
    }
}
