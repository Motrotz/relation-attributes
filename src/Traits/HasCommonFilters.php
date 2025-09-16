<?php

namespace RelationAttributes\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;

trait HasCommonFilters
{
    // Filtering
    public array $where = [];
    public array $whereIn = [];
    public array $whereNull = [];
    public array $whereNotNull = [];

    // Sorting
    public array $orderBy = [];

    // Limiting
    public ?int $limit = null;
    public ?int $offset = null;

    // Selection
    public ?array $select = null;
    public bool $distinct = false;

    // Eager loading
    public array $with = [];

    // Advanced filtering
    public array $whereHas = [];
    public array $whereDate = [];
    public array $whereBetween = [];

    /**
     * Apply common filters to a relationship
     */
    public function applyCommonFilters(Relation $relationship): Relation
    {
        // Apply where conditions
        foreach ($this->where as $column => $value) {
            if (is_array($value) && count($value) === 2) {
                // Handle operator: ['column' => ['>', 100]]
                [$operator, $val] = $value;
                $relationship->where($column, $operator, $val);
            } else {
                $relationship->where($column, $value);
            }
        }

        // Apply whereIn conditions
        foreach ($this->whereIn as $column => $values) {
            $relationship->whereIn($column, $values);
        }

        // Apply whereNull conditions
        foreach ($this->whereNull as $column) {
            $relationship->whereNull($column);
        }

        // Apply whereNotNull conditions
        foreach ($this->whereNotNull as $column) {
            $relationship->whereNotNull($column);
        }

        // Apply ordering
        foreach ($this->orderBy as $column => $direction) {
            $relationship->orderBy($column, $direction ?? 'asc');
        }

        // Apply limit and offset
        if ($this->limit !== null) {
            $relationship->limit($this->limit);

            if ($this->offset !== null) {
                $relationship->offset($this->offset);
            }
        }

        // Apply select
        if ($this->select !== null) {
            $relationship->select($this->select);
        }

        // Apply distinct
        if ($this->distinct) {
            $relationship->distinct();
        }

        // Apply eager loading
        if (!empty($this->with)) {
            $relationship->with($this->with);
        }

        // Apply whereHas conditions
        foreach ($this->whereHas as $relation => $callback) {
            if (is_callable($callback)) {
                $relationship->whereHas($relation, $callback);
            } else {
                $relationship->whereHas($relation);
            }
        }

        // Apply date-based filtering
        foreach ($this->whereDate as $column => $date) {
            $relationship->whereDate($column, $date);
        }

        // Apply between conditions
        foreach ($this->whereBetween as $column => $values) {
            $relationship->whereBetween($column, $values);
        }

        return $relationship;
    }
}