<?php

namespace RelationAttributes\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPivotFilters
{
    // Pivot configuration
    public array $pivotColumns = [];
    public bool $timestamps = false;
    public ?string $pivotClass = null;

    // Pivot filtering
    public array $wherePivot = [];
    public array $wherePivotIn = [];
    public array $wherePivotNull = [];
    public array $wherePivotNotNull = [];

    // Pivot defaults
    public array $withPivotValues = [];

    // Pivot ordering
    public array $orderByPivot = [];

    /**
     * Apply pivot-specific configuration to a BelongsToMany relationship
     */
    public function applyPivotFilters(BelongsToMany $relationship): BelongsToMany
    {
        // Add pivot columns
        if (!empty($this->pivotColumns)) {
            $relationship->withPivot($this->pivotColumns);
        }

        // Add timestamps
        if ($this->timestamps) {
            $relationship->withTimestamps();
        }

        // Use custom pivot model
        if ($this->pivotClass) {
            $relationship->using($this->pivotClass);
        }

        // Apply where conditions on pivot
        foreach ($this->wherePivot as $column => $value) {
            if (is_array($value) && count($value) === 2) {
                // Handle operator: ['column' => ['>', 100]]
                [$operator, $val] = $value;
                $relationship->wherePivot($column, $operator, $val);
            } else {
                $relationship->wherePivot($column, $value);
            }
        }

        // Apply whereIn conditions on pivot
        foreach ($this->wherePivotIn as $column => $values) {
            $relationship->wherePivotIn($column, $values);
        }

        // Apply whereNull conditions on pivot
        foreach ($this->wherePivotNull as $column) {
            $relationship->wherePivotNull($column);
        }

        // Apply whereNotNull conditions on pivot
        foreach ($this->wherePivotNotNull as $column) {
            $relationship->wherePivotNotNull($column);
        }

        // Apply default pivot values
        foreach ($this->withPivotValues as $column => $value) {
            $relationship->withPivotValue($column, $value);
        }

        // Apply ordering on pivot
        foreach ($this->orderByPivot as $column => $direction) {
            $relationship->orderByPivot($column, $direction ?? 'asc');
        }

        return $relationship;
    }
}