<?php

use RelationAttributes\Attributes\HasOne;
use Tests\Fixtures\User;

it('configures default hasOne relationship', function () {
    $user = new
        #[HasOne(User::class)]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(HasOne::class);

    expect($attributes)
        ->toHaveCount(1);

    [$hasOne] = $attributes;
    $relation = $hasOne->newInstance();

    expect($relation)
        ->related->toBe(User::class)
        ->and($relation->foreignKey)->toBeNull()
        ->and($relation->localKey)->toBeNull()
        ->and($relation->relation)->toBe('user');
});

it('configures custom hasOne relationship', function () {
    $user = new
        #[HasOne(User::class, 'profile', 'owner_id', 'uuid')]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(HasOne::class);

    expect($attributes)
        ->toHaveCount(1);

    [$hasOne] = $attributes;
    $relation = $hasOne->newInstance();

    expect($relation)
        ->related->toBe(User::class)
        ->and($relation->foreignKey)->toBe('owner_id')
        ->and($relation->localKey)->toBe('uuid')
        ->and($relation->relation)->toBe('profile');
});

it('allows multiple hasOne relationships', function () {
    $user = new
        #[HasOne(User::class)]
        #[HasOne(User::class, 'primaryProfile', 'primary_user_id', 'id')]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(HasOne::class);

    expect($attributes)->toHaveCount(2);

    [$first, $second] = array_map(
        fn (ReflectionAttribute $attribute): HasOne => $attribute->newInstance(),
        $attributes
    );

    expect($first)
        ->related->toBe(User::class)
        ->and($first->foreignKey)->toBeNull()
        ->and($first->localKey)->toBeNull()
        ->and($first->relation)->toBe('user');

    expect($second)
        ->related->toBe(User::class)
        ->and($second->foreignKey)->toBe('primary_user_id')
        ->and($second->localKey)->toBe('id')
        ->and($second->relation)->toBe('primaryProfile');
});

it('configures hasOne with common filters', function () {
    $user = new
        #[HasOne(
            User::class,
            'activeProfile',
            where: ['active' => true, 'verified' => true],
            whereNull: ['deleted_at'],
            orderBy: ['created_at' => 'desc']
        )]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(HasOne::class);

    [$hasOne] = $attributes;
    $relation = $hasOne->newInstance();

    expect($relation)
        ->relation->toBe('activeProfile')
        ->and($relation->where)->toBe(['active' => true, 'verified' => true])
        ->and($relation->whereNull)->toBe(['deleted_at'])
        ->and($relation->orderBy)->toBe(['created_at' => 'desc']);
});

it('has common filter trait methods', function () {
    $user = new
        #[HasOne(User::class)]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(HasOne::class);

    [$hasOne] = $attributes;
    $relation = $hasOne->newInstance();

    expect(method_exists($relation, 'applyCommonFilters'))->toBeTrue();
});