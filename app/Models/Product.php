<?php

namespace App\Models;

use DB;
use Exception;
use App\DTOs\FilterParams;
use App\Traits\HasUUIDField;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property float $price
 * @property string $description
 * @property array $metadata
 * @property string $category_uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Category|null $category
 * @method static \Database\Factories\ProductFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Query\Builder|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCategoryUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUuid($value)
 * @method static \Illuminate\Database\Query\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Product withoutTrashed()
 * @mixin \Eloquent
 */
class Product extends Model
{
    use HasFactory, HasUUIDField, Filterable, SoftDeletes;

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
     * Get the associated brand to this product
     *
     * @return File|null
     */
    public function getImageFile()
    {
        return File::where('uuid', $this->metadata['image'])->first();
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
