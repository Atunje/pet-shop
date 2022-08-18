<?php

namespace App\Models;

use App\Traits\HasUUIDField;
use App\Traits\FilterableModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
