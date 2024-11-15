<?php

namespace Killmails\RelationAttributes\Attributes;

use Attribute;
use Illuminate\Support\Str;
use Killmails\RelationAttributes\Contracts\RelationAttribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class BelongsTo implements RelationAttribute
{
    public function __construct(
        public string $related,
        public ?string $foreignKey = null,
        public ?string $ownerKey = null,
        public ?string $relation = null,
    ) {
        $this->relation ??= Str::of($this->related)->classBasename()->camel();
    }
}
