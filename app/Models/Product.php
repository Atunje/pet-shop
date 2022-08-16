<?php

namespace App\Models;

use DB;
use App\Traits\HasUUIDField;
use App\Traits\FilterableModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Product extends Model
{
    use HasFactory, HasUUIDField, FilterableModel, SoftDeletes;

    protected $table = "products";

    protected $fillable = [
        'title',
        'price',
        'description',
        'metadata',
        'category_uuid',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the associated category
     *
     * @return BelongsTo<Category, Product>
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_uuid', 'uuid');
    }

    /**
     * Get the associated brand to this product
     *
     * @return Brand|null
     */
    public function getBrand()
    {
        return Brand::where('uuid', $this->metadata['brand'])->first();
    }

    /**
     * Get all db records
     *
     * @param array<string, mixed> $filter_params
     * @param int $per_pg
     * @return LengthAwarePaginator
     */
    public static function getAll($filter_params, $per_pg)
    {
        return self::getRecords($filter_params, $per_pg, ['title', 'price']);
    }

    /**
     * Extend the original functionality by adding queries using category and brand
     *
     * @param Builder<Model> $query
     * @param array $filter_params
     * @param array $filterable
     * @return void
     */
    protected function applyFilterParamsOnQuery($query, $filter_params, $filterable): void
    {
        foreach ($filter_params as $col => $val) {
            if ($val !== null && in_array($col, $filterable)) {
                //add the filterable fields
                $query->where($this->table . "." . $col, 'like', '%' . $val . '%');
            }
        }

        if (isset($filter_params['category'])) {
            $query->join('categories', 'categories.uuid', '=', "products.category_uuid")
                ->where('categories.title', $filter_params['category']);
        }

        if (isset($filter_params['brand'])) {
            $query->join('brands', 'products.metadata', 'like', DB::raw("CONCAT('%', brands.uuid, '%')"))
                ->where('brands.title', $filter_params['brand']);
        }
    }
}
