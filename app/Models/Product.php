<?php

namespace App\Models;

use DB;
use Exception;
use App\DTOs\FilterParams;
use App\Traits\HasUUIDField;
use App\Traits\FilterableModel;
use Illuminate\Database\Eloquent\Model;
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
     * @param FilterParams $filter_params
     * @return LengthAwarePaginator
     * @throws Exception
     */
    public static function getAll($filter_params)
    {
        return self::getRecords($filter_params, ['title', 'price']);
    }

    /**
     * Extend the query by adding queries to filter by brand and category
     *
     * @return void
     */
    protected function additionalQueryFromModel(): void
    {
        //add special queries based on category
        if (isset($this->filter_params->category)) {
            $this->query->join('categories', 'categories.uuid', '=', "products.category_uuid")
                ->where('categories.title', $this->filter_params->category);
        }

        //add special queries based on category
        if (isset($this->filter_params->brand)) {
            $this->query->join('brands', 'products.metadata', 'like', DB::raw("CONCAT('%', brands.uuid, '%')"))
                ->where('brands.title', $this->filter_params->brand);
        }
    }
}
