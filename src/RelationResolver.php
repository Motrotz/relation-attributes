<?php

namespace RelationAttributes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use LogicException;
use RelationAttributes\Attributes\BelongsTo;
use RelationAttributes\Attributes\BelongsToMany;
use RelationAttributes\Attributes\HasMany;
use RelationAttributes\Attributes\HasOne;
use RelationAttributes\Contracts\RelationAttribute;

class RelationResolver
{
    public function resolve(RelationAttribute $relation): Closure
    {
        return match ($relation::class) {
            BelongsTo::class => $this->belongsTo($relation),
            HasOne::class => $this->hasOne($relation),
            HasMany::class => $this->hasMany($relation),
            BelongsToMany::class => $this->belongsToMany($relation),
            default => throw new LogicException(
                'Unhandled Relation Attribute: '.$relation::class
            ),
        };
    }

    public function belongsTo(BelongsTo $relation): Closure
    {
        return fn (Model $model) => function () use ($model, $relation): Relations\BelongsTo {
            $relationship = $model->belongsTo(
                $relation->related,
                $relation->foreignKey,
                $relation->ownerKey,
                $relation->relation
            );

            // Apply common filters if available
            if (method_exists($relation, 'applyCommonFilters')) {
                $relationship = $relation->applyCommonFilters($relationship);
            }

            return $relationship;
        };
    }

    public function hasOne(HasOne $relation): Closure
    {
        return fn (Model $model) => function () use ($model, $relation): Relations\HasOne {
            $relationship = $model->hasOne(
                $relation->related,
                $relation->foreignKey,
                $relation->localKey
            );

            // Apply common filters if available
            if (method_exists($relation, 'applyCommonFilters')) {
                $relationship = $relation->applyCommonFilters($relationship);
            }

            return $relationship;
        };
    }

    public function hasMany(HasMany $relation): Closure
    {
        return fn (Model $model) => function () use ($model, $relation): Relations\HasMany {
            $relationship = $model->hasMany(
                $relation->related,
                $relation->foreignKey,
                $relation->localKey
            );

            // Apply common filters if available
            if (method_exists($relation, 'applyCommonFilters')) {
                $relationship = $relation->applyCommonFilters($relationship);
            }

            return $relationship;
        };
    }

    public function belongsToMany(BelongsToMany $relation): Closure
    {
        return fn (Model $model) => function () use ($model, $relation): Relations\BelongsToMany {
            $relationship = $model->belongsToMany(
                $relation->related,
                $relation->table,
                $relation->foreignPivotKey,
                $relation->relatedPivotKey,
                $relation->parentKey,
                $relation->relatedKey,
                $relation->relation
            );

            // Set pivot accessor name first (before other pivot operations)
            if ($relation->pivotAs && $relation->pivotAs !== 'pivot') {
                $relationship->as($relation->pivotAs);
            }

            // Apply pivot filters if available
            if (method_exists($relation, 'applyPivotFilters')) {
                $relationship = $relation->applyPivotFilters($relationship);
            }

            // Apply common filters if available
            if (method_exists($relation, 'applyCommonFilters')) {
                $relationship = $relation->applyCommonFilters($relationship);
            }

            return $relationship;
        };
    }
}
