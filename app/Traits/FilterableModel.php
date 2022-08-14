<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait FilterableModel
{
    /**
     * Get all database records based on the supplied filter params
     *
     * @param array<string, string> $filter_params
     * @param int $per_pg
     * @param array<string, string|bool|int> $extra_where
     * @param array<int, string> $filterable
     * @return LengthAwarePaginator
     */
    private static function getRecords($filter_params, $per_pg, $filterable = [], $extra_where = [])
    {
        $instance = new self();

        $query = $instance->query();

        //add the extra where statement to the query
        if(!empty($extra_where)) {
            $query->where($extra_where);
        }

        if(count($filter_params) > 0) {
            foreach ($filter_params as $col => $val) {
                if(in_array($col, $filterable) && $val !== null) {
                    //add the filterable fields
                    $query->where($col, $val);
                }
            }

            if (isset($filter_params['sortBy'])) {
                $order = $filter_params['desc'] === "0" ? "asc" : "desc";
                $query = $query->orderBy($filter_params['sort_by'], $order);
            }
        }

        return $query->paginate($per_pg);
    }


    /**
     * Get all db records
     *
     * @param $params
     * @param $per_pg
     * @return LengthAwarePaginator
     */
    public static function getUsers($params, $per_pg): LengthAwarePaginator
    {
        return self::getRecords($params, $per_pg);
    }
}
