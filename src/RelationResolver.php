<?php

namespace RelationAttributes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use LogicException;
use RelationAttributes\Attributes\BelongsTo;
use RelationAttributes\Contracts\RelationAttribute;

class RelationResolver
{
    public function resolve(RelationAttribute $relation): Closure
    {
        return match ($relation::class) {
            BelongsTo::class => $this->belongsTo($relation),
            default => throw new LogicException(
                'Unhandled Relation Attribute: '.$relation::class
            ),
        };
    }

    public function belongsTo(BelongsTo $relation): Closure
    {
        return fn (Model $model) => fn (): Relations\BelongsTo => $model->belongsTo(
            $relation->related,
            $relation->foreignKey,
            $relation->ownerKey,
            $relation->relation
        );
    }
}
