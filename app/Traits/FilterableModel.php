<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait FilterableModel
{
    /**
     * Get all database records by building the necessary query and returning paginated records
     *
     * @param array<string, mixed> $filter_params
     * @param int $per_pg
     * @param array<int, string> $filterable
     * @param array<string, mixed> $array_of_wheres
     * @return LengthAwarePaginator
     */
    private static function getRecords($filter_params, $per_pg, $filterable = [], $array_of_wheres = [])
    {
        $instance = new self();

        $query = $instance->query();

        $instance->applyWheres($query, $array_of_wheres);

        $instance->applyFilterParamsOnQuery($query, $filter_params, $filterable);

        $instance->sortQuery(
            $query,
            $filter_params['sort_by'] ?? null,
            isset($filter_params['desc']) && $filter_params['desc'] === 1
        );

        return $query->paginate($per_pg);
    }

    /**
     * Apply the supplied array of wheres to query builder
     *
     * @param Builder<Model> $query
     * @param array $wheres
     * @return void
     */
    protected function applyWheres($query, $wheres)
    {
        if (! empty($wheres)) {
            $query->where($wheres);
        }
    }

    /**
     * Add extra where statements to the query based on the filter params supplied
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
                $query->where($col, $val);
            }
        }
    }

    /**
     * Add sorting to the query builder
     *
     * @param Builder<Model> $query
     * @param string|mixed|null $sort_by
     * @param bool $desc
     * @return void
     */
    protected function sortQuery($query, $sort_by, $desc)
    {
        if (! is_null($sort_by)) {
            $order = $desc ? "asc" : "desc";
            $query->orderBy(strval($sort_by), $order);
        }
    }

    /**
     * Get all db records
     *
     * @param array<string, mixed> $filter_params
     * @param int $per_pg
     * @return LengthAwarePaginator
     */
    public static function getAll($filter_params, $per_pg): LengthAwarePaginator
    {
        return self::getRecords($filter_params, $per_pg);
    }
}
