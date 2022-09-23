<?php

namespace App\Traits;

use App\DTOs\FilterParams;
use DateTime;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * The query to be built and executed.
     *
     * @var Builder<self>
     */
    private Builder $query;

    /**
     * Array of key value pairs to add to query's where statement.
     */
    private FilterParams $filter_params;

    /**
     * Array of model fields that can make where statements.
     *
     * @var array<int, string>
     */
    private array $filterables;

    /**
     * Records per page.
     */
    private int $per_page;

    /**
     * Field used to sort the records.
     */
    private ?string $sort_by;

    /**
     * Sort the records in descending order.
     */
    private bool $sort_descending;

    /**
     * Field to use when querying by date range.
     */
    protected string $date_field = 'created_at';

    /**
     * Get all database records by building the necessary query and returning paginated records.
     *
     * @param FilterParams $filter_params
     * @param array $filterables
     * @return LengthAwarePaginator
     * @throws Exception
     */
    private static function getRecords(FilterParams $filter_params, array $filterables = []): LengthAwarePaginator
    {
        $instance = new self();

        //set the instance variables
        $instance->setInstanceParams($instance, $filter_params, $filterables);

        $instance->queryByFilterParams();

        $instance->queryByDateRange();

        $instance->sortQuery();

        $instance->additionalQueryFromModel();

        return $instance->query->paginate($instance->per_page);
    }

    /**
     * Set the instance parameters.
     *
     * @param self $instance
     * @param FilterParams $filter_params
     * @param array $filterables
     * @return void
     */
    private function setInstanceParams(self $instance, FilterParams $filter_params, array $filterables): void
    {
        $this->filter_params = $filter_params;
        $this->filterables = $filterables;

        $sort_by = $filter_params->sort_by ?? null;
        $this->sort_by = in_array($sort_by, $this->filterables) ? $sort_by : null;

        $this->per_page = $filter_params->limit;

        $this->sort_descending = $filter_params->desc;

        //initialize the query builder
        $instance->query = $instance->query();
    }

    protected function queryByFilterParams(): void
    {
        foreach ($this->filter_params->toArray() as $col => $val) {
            if (in_array($col, $this->filterables)) {
                //add the filterable fields
                $this->query->where($col, $val);
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function queryByDateRange(): void
    {
        if (isset($this->filter_params->date_range)) {
            $date_range = $this->filter_params->date_range;
            $from = strval($date_range['from']);
            $to = strval($date_range['from']);

            $this->query
                ->whereDate($this->date_field, '>=', new DateTime($from))
                ->whereDate($this->date_field, '<=', new DateTime($to));
        }
    }

    /**
     * Add sorting to the query builder.
     *
     * @return void
     */
    protected function sortQuery(): void
    {
        if (! is_null($this->sort_by)) {
            $order = $this->sort_descending ? 'asc' : 'desc';
            $this->query->orderBy($this->sort_by, $order);
        }
    }

    /**
     * Allow models to add additional query with the implementation of this method.
     *
     * @return void
     */
    protected function additionalQueryFromModel(): void
    {
        //
    }

    /**
     * Get all db records.
     *
     * @param FilterParams $filter_params
     * @return LengthAwarePaginator
     * @throws Exception
     */
    public static function getAll(FilterParams $filter_params): LengthAwarePaginator
    {
        return self::getRecords($filter_params);
    }
}
