<?php

use RelationAttributes\Attributes\BelongsToMany;
use Tests\Fixtures\User;

it('configures default belongsToMany relationship', function () {
    $user = new
        #[BelongsToMany(User::class)]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(BelongsToMany::class);

    expect($attributes)
        ->toHaveCount(1);

    [$belongsToMany] = $attributes;
    $relation = $belongsToMany->newInstance();

    expect($relation)
        ->related->toBe(User::class)
        ->and($relation->relation)->toBe('users')
        ->and($relation->pivotAs)->toBe('pivot')
        ->and($relation->table)->toBeNull()  // Laravel will auto-generate
        ->and($relation->foreignPivotKey)->toBeNull()
        ->and($relation->relatedPivotKey)->toBeNull()
        ->and($relation->timestamps)->toBeFalse();
});

it('configures custom belongsToMany relationship', function () {
    $user = new
        #[BelongsToMany(
            User::class,
            'coAuthors',
            'membership',
            'post_user',
            'post_id',
            'user_id',
            'id',
            'id'
        )]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(BelongsToMany::class);

    expect($attributes)
        ->toHaveCount(1);

    [$belongsToMany] = $attributes;
    $relation = $belongsToMany->newInstance();

    expect($relation)
        ->related->toBe(User::class)
        ->and($relation->relation)->toBe('coAuthors')
        ->and($relation->pivotAs)->toBe('membership')
        ->and($relation->table)->toBe('post_user')
        ->and($relation->foreignPivotKey)->toBe('post_id')
        ->and($relation->relatedPivotKey)->toBe('user_id')
        ->and($relation->parentKey)->toBe('id')
        ->and($relation->relatedKey)->toBe('id');
});

it('configures belongsToMany with pivot columns and timestamps', function () {
    $user = new
        #[BelongsToMany(
            User::class,
            'members',
            'membership',
            pivotColumns: ['role', 'joined_at'],
            timestamps: true
        )]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(BelongsToMany::class);

    [$belongsToMany] = $attributes;
    $relation = $belongsToMany->newInstance();

    expect($relation)
        ->relation->toBe('members')
        ->and($relation->pivotAs)->toBe('membership')
        ->and($relation->pivotColumns)->toBe(['role', 'joined_at'])
        ->and($relation->timestamps)->toBeTrue();
});

it('configures belongsToMany with filtering and ordering', function () {
    $user = new
        #[BelongsToMany(
            User::class,
            'activeMembers',
            wherePivot: ['active' => true],
            wherePivotIn: ['role' => ['admin', 'moderator']],
            wherePivotNull: ['deleted_at'],
            orderByPivot: ['joined_at' => 'desc']
        )]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(BelongsToMany::class);

    [$belongsToMany] = $attributes;
    $relation = $belongsToMany->newInstance();

    expect($relation)
        ->relation->toBe('activeMembers')
        ->and($relation->wherePivot)->toBe(['active' => true])
        ->and($relation->wherePivotIn)->toBe(['role' => ['admin', 'moderator']])
        ->and($relation->wherePivotNull)->toBe(['deleted_at'])
        ->and($relation->orderByPivot)->toBe(['joined_at' => 'desc']);
});

it('allows multiple belongsToMany relationships', function () {
    $user = new
        #[BelongsToMany(User::class, 'friends')]
        #[BelongsToMany(User::class, 'followers', 'follower', 'user_followers')]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(BelongsToMany::class);

    expect($attributes)->toHaveCount(2);

    [$first, $second] = array_map(
        fn (ReflectionAttribute $attribute): BelongsToMany => $attribute->newInstance(),
        $attributes
    );

    expect($first)
        ->relation->toBe('friends')
        ->and($first->pivotAs)->toBe('pivot');

    expect($second)
        ->relation->toBe('followers')
        ->and($second->pivotAs)->toBe('follower')
        ->and($second->table)->toBe('user_followers');
});

it('configures belongsToMany with common filters and limit', function () {
    $user = new
        #[BelongsToMany(
            User::class,
            'topContributors',
            where: ['active' => true],
            orderBy: ['name' => 'asc'],
            limit: 10
        )]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(BelongsToMany::class);

    [$belongsToMany] = $attributes;
    $relation = $belongsToMany->newInstance();

    expect($relation)
        ->relation->toBe('topContributors')
        ->and($relation->where)->toBe(['active' => true])
        ->and($relation->orderBy)->toBe(['name' => 'asc'])
        ->and($relation->limit)->toBe(10);
});

it('configures belongsToMany with pivot filters and timestamps', function () {
    $user = new
        #[BelongsToMany(
            User::class,
            'activeMembers',
            'membership',
            pivotColumns: ['role', 'joined_at', 'permissions'],
            timestamps: true,
            wherePivot: ['active' => true],
            wherePivotIn: ['role' => ['admin', 'moderator']],
            wherePivotNull: ['banned_at'],
            orderByPivot: ['joined_at' => 'desc'],
            withPivotValues: ['status' => 'active']
        )]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(BelongsToMany::class);

    [$belongsToMany] = $attributes;
    $relation = $belongsToMany->newInstance();

    expect($relation)
        ->relation->toBe('activeMembers')
        ->and($relation->pivotAs)->toBe('membership')
        ->and($relation->pivotColumns)->toBe(['role', 'joined_at', 'permissions'])
        ->and($relation->timestamps)->toBeTrue()
        ->and($relation->wherePivot)->toBe(['active' => true])
        ->and($relation->wherePivotIn)->toBe(['role' => ['admin', 'moderator']])
        ->and($relation->wherePivotNull)->toBe(['banned_at'])
        ->and($relation->orderByPivot)->toBe(['joined_at' => 'desc'])
        ->and($relation->withPivotValues)->toBe(['status' => 'active']);
});

it('has both common and pivot filter trait methods', function () {
    $user = new
        #[BelongsToMany(User::class)]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(BelongsToMany::class);

    [$belongsToMany] = $attributes;
    $relation = $belongsToMany->newInstance();

    expect(method_exists($relation, 'applyCommonFilters'))->toBeTrue()
        ->and(method_exists($relation, 'applyPivotFilters'))->toBeTrue();
});