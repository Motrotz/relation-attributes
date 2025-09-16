<?php

use RelationAttributes\Attributes\HasMany;
use Tests\Fixtures\User;

it('configures where filters correctly', function () {
    $relation = new HasMany(
        User::class,
        where: [
            'active' => true,
            'verified' => 1,
            'role' => 'admin'
        ]
    );

    expect($relation->where)->toBe([
        'active' => true,
        'verified' => 1,
        'role' => 'admin'
    ]);
});

it('configures whereIn filters correctly', function () {
    $relation = new HasMany(
        User::class,
        whereIn: [
            'status' => ['active', 'pending', 'verified'],
            'role' => ['admin', 'moderator']
        ]
    );

    expect($relation->whereIn)->toBe([
        'status' => ['active', 'pending', 'verified'],
        'role' => ['admin', 'moderator']
    ]);
});

it('configures whereNull filters correctly', function () {
    $relation = new HasMany(
        User::class,
        whereNull: ['deleted_at', 'banned_at', 'suspended_at']
    );

    expect($relation->whereNull)->toBe(['deleted_at', 'banned_at', 'suspended_at']);
});

it('configures whereNotNull filters correctly', function () {
    $relation = new HasMany(
        User::class,
        whereNotNull: ['email_verified_at', 'activated_at']
    );

    expect($relation->whereNotNull)->toBe(['email_verified_at', 'activated_at']);
});

it('configures orderBy correctly', function () {
    $relation = new HasMany(
        User::class,
        orderBy: [
            'created_at' => 'desc',
            'name' => 'asc',
            'priority' => 'desc'
        ]
    );

    expect($relation->orderBy)->toBe([
        'created_at' => 'desc',
        'name' => 'asc',
        'priority' => 'desc'
    ]);
});

it('configures limit and offset correctly', function () {
    $relation = new HasMany(
        User::class,
        limit: 10,
        offset: 20
    );

    expect($relation->limit)->toBe(10)
        ->and($relation->offset)->toBe(20);
});

it('configures select columns correctly', function () {
    $relation = new HasMany(
        User::class,
        select: ['id', 'name', 'email']
    );

    expect($relation->select)->toBe(['id', 'name', 'email']);
});

it('configures distinct flag correctly', function () {
    $relation = new HasMany(
        User::class,
        distinct: true
    );

    expect($relation->distinct)->toBeTrue();
});

it('configures eager loading with correctly', function () {
    $relation = new HasMany(
        User::class,
        with: ['posts', 'comments', 'profile']
    );

    expect($relation->with)->toBe(['posts', 'comments', 'profile']);
});

it('configures whereHas correctly', function () {
    $relation = new HasMany(
        User::class,
        whereHas: [
            'posts' => function ($query) {
                $query->where('published', true);
            },
            'comments' => null  // Just check existence
        ]
    );

    expect($relation->whereHas)
        ->toHaveKey('posts')
        ->toHaveKey('comments')
        ->and($relation->whereHas['posts'])->toBeCallable()
        ->and($relation->whereHas['comments'])->toBeNull();
});

it('configures whereDate correctly', function () {
    $relation = new HasMany(
        User::class,
        whereDate: [
            'created_at' => '2024-01-01',
            'published_at' => '2024-12-25'
        ]
    );

    expect($relation->whereDate)->toBe([
        'created_at' => '2024-01-01',
        'published_at' => '2024-12-25'
    ]);
});

it('configures whereBetween correctly', function () {
    $relation = new HasMany(
        User::class,
        whereBetween: [
            'created_at' => ['2024-01-01', '2024-12-31'],
            'age' => [18, 65],
            'price' => [10.50, 99.99]
        ]
    );

    expect($relation->whereBetween)->toBe([
        'created_at' => ['2024-01-01', '2024-12-31'],
        'age' => [18, 65],
        'price' => [10.50, 99.99]
    ]);
});

it('combines multiple filters correctly', function () {
    $relation = new HasMany(
        User::class,
        'complexRelation',
        where: ['active' => true],
        whereIn: ['role' => ['admin', 'editor']],
        whereNull: ['deleted_at'],
        whereNotNull: ['verified_at'],
        whereDate: ['created_at' => '2024-01-01'],
        whereBetween: ['age' => [18, 65]],
        orderBy: ['created_at' => 'desc'],
        limit: 10,
        offset: 5,
        select: ['id', 'name'],
        distinct: true,
        with: ['profile']
    );

    expect($relation)
        ->relation->toBe('complexRelation')
        ->and($relation->where)->toBe(['active' => true])
        ->and($relation->whereIn)->toBe(['role' => ['admin', 'editor']])
        ->and($relation->whereNull)->toBe(['deleted_at'])
        ->and($relation->whereNotNull)->toBe(['verified_at'])
        ->and($relation->whereDate)->toBe(['created_at' => '2024-01-01'])
        ->and($relation->whereBetween)->toBe(['age' => [18, 65]])
        ->and($relation->orderBy)->toBe(['created_at' => 'desc'])
        ->and($relation->limit)->toBe(10)
        ->and($relation->offset)->toBe(5)
        ->and($relation->select)->toBe(['id', 'name'])
        ->and($relation->distinct)->toBeTrue()
        ->and($relation->with)->toBe(['profile']);
});

it('has common filter trait method', function () {
    $relation = new HasMany(User::class);

    expect(method_exists($relation, 'applyCommonFilters'))->toBeTrue();
});

it('works with BelongsTo relationship', function () {
    $relation = new \RelationAttributes\Attributes\BelongsTo(
        User::class,
        where: ['active' => true],
        orderBy: ['name' => 'asc'],
        limit: 1
    );

    expect($relation->where)->toBe(['active' => true])
        ->and($relation->orderBy)->toBe(['name' => 'asc'])
        ->and($relation->limit)->toBe(1);
});

it('works with HasOne relationship', function () {
    $relation = new \RelationAttributes\Attributes\HasOne(
        User::class,
        where: ['primary' => true],
        whereNull: ['deleted_at']
    );

    expect($relation->where)->toBe(['primary' => true])
        ->and($relation->whereNull)->toBe(['deleted_at']);
});

it('works with BelongsToMany relationship', function () {
    $relation = new \RelationAttributes\Attributes\BelongsToMany(
        User::class,
        where: ['active' => true],
        orderBy: ['name' => 'asc'],
        limit: 10
    );

    expect($relation->where)->toBe(['active' => true])
        ->and($relation->orderBy)->toBe(['name' => 'asc'])
        ->and($relation->limit)->toBe(10);
});