<?php

namespace RelationAttributes\Concerns;

use ReflectionAttribute;
use ReflectionClass;
use RelationAttributes\Contracts\RelationAttribute;
use RelationAttributes\RelationResolver;

trait HasRelationAttributes
{
    protected function initializeRelationAttributes(): void
    {
        $attributes = (new ReflectionClass(static::class))
            ->getAttributes(RelationAttribute::class, ReflectionAttribute::IS_INSTANCEOF);

        if (! $attributes) {
            return;
        }

        $resolver = new RelationResolver;

        foreach ($attributes as $attribute) {
            $relation = $attribute->newInstance();

            if (! method_exists(static::class, $relation->relation)) {
                $handle = $resolver->resolve($relation);

                static::resolveRelationUsing($relation->relation, $handle($this));
            }
        }
    }
}
