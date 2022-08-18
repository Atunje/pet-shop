<?php

namespace App\Models;

use App\Traits\HasUUIDField;
use App\Traits\FilterableModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\OrderStatus
 *
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Database\Factories\OrderStatusFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrderStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus whereUuid($value)
 * @method static \Illuminate\Database\Query\Builder|OrderStatus withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderStatus withoutTrashed()
 * @mixin \Eloquent
 */
class OrderStatus extends Model
{
    use HasFactory, HasUUIDField, FilterableModel, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
    ];

    /**
     * Status of shipped order
     *
     * @const string
     */
    private const ORDER_SHIPPED_STATUS = "shipped";

    /**
     * Get order status by uuid
     *
     * @param string $order_status_uuid
     * @return ?self
     */
    public static function getStatus($order_status_uuid)
    {
        return self::where('uuid', $order_status_uuid)->first();
    }

    /**
     * Check if status is shipped
     *
     * @param string $uuid
     * @return bool
     */
    public static function isShippedStatus($uuid): bool
    {
        $status = self::getStatus($uuid);
        return $status !== null && strtolower($status->title) === self::ORDER_SHIPPED_STATUS;
    }
}
