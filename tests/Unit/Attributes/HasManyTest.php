<?php

use RelationAttributes\Attributes\HasMany;
use Tests\Fixtures\User;

it('configures default hasMany relationship', function () {
    $user = new
        #[HasMany(User::class)]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(HasMany::class);

    expect($attributes)
        ->toHaveCount(1);

    [$hasMany] = $attributes;
    $relation = $hasMany->newInstance();

    expect($relation)
        ->related->toBe(User::class)
        ->and($relation->foreignKey)->toBeNull()
        ->and($relation->localKey)->toBeNull()
        ->and($relation->relation)->toBe('users');
});

it('configures custom hasMany relationship', function () {
    $user = new
        #[HasMany(User::class, 'children', 'parent_uuid', 'uuid')]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(HasMany::class);

    expect($attributes)
        ->toHaveCount(1);

    [$hasMany] = $attributes;
    $relation = $hasMany->newInstance();

    expect($relation)
        ->related->toBe(User::class)
        ->and($relation->foreignKey)->toBe('parent_uuid')
        ->and($relation->localKey)->toBe('uuid')
        ->and($relation->relation)->toBe('children');
});

it('allows multiple hasMany relationships', function () {
    $user = new
        #[HasMany(User::class)]
        #[HasMany(User::class, 'subordinates', 'manager_id', 'id')]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(HasMany::class);

    expect($attributes)->toHaveCount(2);

    [$first, $second] = array_map(
        fn (ReflectionAttribute $attribute): HasMany => $attribute->newInstance(),
        $attributes
    );

    expect($first)
        ->related->toBe(User::class)
        ->and($first->foreignKey)->toBeNull()
        ->and($first->localKey)->toBeNull()
        ->and($first->relation)->toBe('users');

    expect($second)
        ->related->toBe(User::class)
        ->and($second->foreignKey)->toBe('manager_id')
        ->and($second->localKey)->toBe('id')
        ->and($second->relation)->toBe('subordinates');
});

it('pluralizes relation name by default', function () {
    $team = new
        #[HasMany(User::class)]
        class extends User {};

    $attributes = (new ReflectionClass($team))
        ->getAttributes(HasMany::class);

    [$hasMany] = $attributes;
    $relation = $hasMany->newInstance();

    expect($relation->relation)->toBe('users');
});

it('configures hasMany with common filters and limit', function () {
    $user = new
        #[HasMany(
            User::class,
            'recentPosts',
            where: ['published' => true],
            whereIn: ['status' => ['approved', 'featured']],
            whereNull: ['deleted_at'],
            orderBy: ['published_at' => 'desc', 'views' => 'desc'],
            limit: 5
        )]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(HasMany::class);

    [$hasMany] = $attributes;
    $relation = $hasMany->newInstance();

    expect($relation)
        ->relation->toBe('recentPosts')
        ->and($relation->where)->toBe(['published' => true])
        ->and($relation->whereIn)->toBe(['status' => ['approved', 'featured']])
        ->and($relation->whereNull)->toBe(['deleted_at'])
        ->and($relation->orderBy)->toBe(['published_at' => 'desc', 'views' => 'desc'])
        ->and($relation->limit)->toBe(5);
});

it('has common filter trait methods', function () {
    $user = new
        #[HasMany(User::class)]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(HasMany::class);

    [$hasMany] = $attributes;
    $relation = $hasMany->newInstance();

    expect(method_exists($relation, 'applyCommonFilters'))->toBeTrue();
});