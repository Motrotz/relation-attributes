<?php

namespace Killmails\RelationAttributes\Concerns;

use Killmails\RelationAttributes\Contracts\RelationAttribute;
use Killmails\RelationAttributes\RelationResolver;
use ReflectionAttribute;
use ReflectionClass;

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
