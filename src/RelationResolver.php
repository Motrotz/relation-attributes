<?php

namespace Killmails\RelationAttributes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Killmails\RelationAttributes\Attributes\BelongsTo;
use Killmails\RelationAttributes\Contracts\RelationAttribute;
use LogicException;

class RelationResolver
{
    public function resolve(RelationAttribute $relation): Closure
    {
        return match ($relation::class) {
            BelongsTo::class => $this->belongsTo($relation),
            default => throw new LogicException('Unhandled Relation Attribute: '.$relation::class),
        };
    }

    public function belongsTo(BelongsTo $relation): Closure
    {
        return fn (Model $model) => fn () => $model->belongsTo(
            $relation->related,
            $relation->foreignKey,
            $relation->ownerKey,
            $relation->relation
        );
    }
}
