<?php

namespace RelationAttributes\Attributes;

use Attribute;
use Illuminate\Support\Str;
use RelationAttributes\Contracts\RelationAttribute;
use RelationAttributes\Traits\HasCommonFilters;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class HasMany implements RelationAttribute
{
    use HasCommonFilters;
    public function __construct(
        public string $related,
        public ?string $relation = null,
        public ?string $foreignKey = null,
        public ?string $localKey = null,
        // Common filters
        array $where = [],
        array $whereIn = [],
        array $whereNull = [],
        array $whereNotNull = [],
        array $orderBy = [],
        ?int $limit = null,
        ?int $offset = null,
        ?array $select = null,
        bool $distinct = false,
        array $with = [],
        array $whereHas = [],
        array $whereDate = [],
        array $whereBetween = [],
    ) {
        $this->relation ??= Str::of($this->related)
            ->classBasename()
            ->plural()
            ->camel();

        // Initialize filter properties
        $this->where = $where;
        $this->whereIn = $whereIn;
        $this->whereNull = $whereNull;
        $this->whereNotNull = $whereNotNull;
        $this->orderBy = $orderBy;
        $this->limit = $limit;
        $this->offset = $offset;
        $this->select = $select;
        $this->distinct = $distinct;
        $this->with = $with;
        $this->whereHas = $whereHas;
        $this->whereDate = $whereDate;
        $this->whereBetween = $whereBetween;
    }
}