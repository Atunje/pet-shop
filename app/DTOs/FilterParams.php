<?php

namespace App\DTOs;

class FilterParams
{
    public int $limit;

    public string $sort_by;

    public bool $desc;

    public array $date_range;

    public function __construct(array $filter_params)
    {
        foreach ($filter_params as $field => $value) {
            $this->__set($field, $value);
        }
    }

    /**
     * Set  a new object attribute.
     * @param string $field
     * @param mixed $value
     * @return void
     */
    public function __set(string $field, mixed $value): void
    {
        $this->$field = $value;
    }

    /**
     * Convert object to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return (array) $this;
    }
}
