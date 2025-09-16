<?php

use RelationAttributes\Attributes\BelongsToMany;
use Tests\Fixtures\User;

it('configures pivot columns correctly', function () {
    $relation = new BelongsToMany(
        User::class,
        pivotColumns: ['role', 'permissions', 'joined_at', 'is_active']
    );

    expect($relation->pivotColumns)->toBe(['role', 'permissions', 'joined_at', 'is_active']);
});

it('configures timestamps flag correctly', function () {
    $relation = new BelongsToMany(
        User::class,
        timestamps: true
    );

    expect($relation->timestamps)->toBeTrue();
});

it('configures custom pivot class correctly', function () {
    $relation = new BelongsToMany(
        User::class,
        pivotClass: 'App\Models\CustomPivot'
    );

    expect($relation->pivotClass)->toBe('App\Models\CustomPivot');
});

it('configures wherePivot filters correctly', function () {
    $relation = new BelongsToMany(
        User::class,
        wherePivot: [
            'active' => true,
            'approved' => 1,
            'role' => 'admin'
        ]
    );

    expect($relation->wherePivot)->toBe([
        'active' => true,
        'approved' => 1,
        'role' => 'admin'
    ]);
});

it('configures wherePivotIn filters correctly', function () {
    $relation = new BelongsToMany(
        User::class,
        wherePivotIn: [
            'role' => ['admin', 'moderator', 'editor'],
            'status' => ['active', 'pending']
        ]
    );

    expect($relation->wherePivotIn)->toBe([
        'role' => ['admin', 'moderator', 'editor'],
        'status' => ['active', 'pending']
    ]);
});

it('configures wherePivotNull filters correctly', function () {
    $relation = new BelongsToMany(
        User::class,
        wherePivotNull: ['deleted_at', 'banned_at', 'revoked_at']
    );

    expect($relation->wherePivotNull)->toBe(['deleted_at', 'banned_at', 'revoked_at']);
});

it('configures wherePivotNotNull filters correctly', function () {
    $relation = new BelongsToMany(
        User::class,
        wherePivotNotNull: ['approved_at', 'verified_at']
    );

    expect($relation->wherePivotNotNull)->toBe(['approved_at', 'verified_at']);
});

it('configures withPivotValues defaults correctly', function () {
    $relation = new BelongsToMany(
        User::class,
        withPivotValues: [
            'status' => 'active',
            'assigned_by' => 'system',
            'is_primary' => false
        ]
    );

    expect($relation->withPivotValues)->toBe([
        'status' => 'active',
        'assigned_by' => 'system',
        'is_primary' => false
    ]);
});

it('configures orderByPivot correctly', function () {
    $relation = new BelongsToMany(
        User::class,
        orderByPivot: [
            'created_at' => 'desc',
            'role' => 'asc',
            'priority' => 'desc'
        ]
    );

    expect($relation->orderByPivot)->toBe([
        'created_at' => 'desc',
        'role' => 'asc',
        'priority' => 'desc'
    ]);
});

it('configures pivot table name correctly', function () {
    $relation = new BelongsToMany(
        User::class,
        'members',
        'membership',
        'team_user'
    );

    expect($relation->table)->toBe('team_user');
});

it('configures pivot keys correctly', function () {
    $relation = new BelongsToMany(
        User::class,
        'members',
        'membership',
        'team_user',
        'team_uuid',
        'user_uuid',
        'uuid',
        'uuid'
    );

    expect($relation)
        ->table->toBe('team_user')
        ->and($relation->foreignPivotKey)->toBe('team_uuid')
        ->and($relation->relatedPivotKey)->toBe('user_uuid')
        ->and($relation->parentKey)->toBe('uuid')
        ->and($relation->relatedKey)->toBe('uuid');
});

it('configures pivot accessor name correctly', function () {
    $relation = new BelongsToMany(
        User::class,
        'members',
        'membership'
    );

    expect($relation->pivotAs)->toBe('membership');
});

it('defaults pivot accessor to "pivot"', function () {
    $relation = new BelongsToMany(User::class);

    expect($relation->pivotAs)->toBe('pivot');
});

it('combines all pivot features correctly', function () {
    $relation = new BelongsToMany(
        User::class,
        'activeMembers',
        'membership',
        'organization_user',
        'org_id',
        'member_id',
        'id',
        'id',
        pivotColumns: ['role', 'department', 'joined_at', 'permissions'],
        timestamps: true,
        pivotClass: 'App\Models\Membership',
        wherePivot: ['active' => true, 'verified' => true],
        wherePivotIn: ['role' => ['admin', 'manager', 'member']],
        wherePivotNull: ['terminated_at'],
        wherePivotNotNull: ['approved_at'],
        withPivotValues: ['added_by' => 'system', 'status' => 'active'],
        orderByPivot: ['joined_at' => 'desc', 'role' => 'asc']
    );

    expect($relation)
        ->relation->toBe('activeMembers')
        ->and($relation->pivotAs)->toBe('membership')
        ->and($relation->table)->toBe('organization_user')
        ->and($relation->foreignPivotKey)->toBe('org_id')
        ->and($relation->relatedPivotKey)->toBe('member_id')
        ->and($relation->parentKey)->toBe('id')
        ->and($relation->relatedKey)->toBe('id')
        ->and($relation->pivotColumns)->toBe(['role', 'department', 'joined_at', 'permissions'])
        ->and($relation->timestamps)->toBeTrue()
        ->and($relation->pivotClass)->toBe('App\Models\Membership')
        ->and($relation->wherePivot)->toBe(['active' => true, 'verified' => true])
        ->and($relation->wherePivotIn)->toBe(['role' => ['admin', 'manager', 'member']])
        ->and($relation->wherePivotNull)->toBe(['terminated_at'])
        ->and($relation->wherePivotNotNull)->toBe(['approved_at'])
        ->and($relation->withPivotValues)->toBe(['added_by' => 'system', 'status' => 'active'])
        ->and($relation->orderByPivot)->toBe(['joined_at' => 'desc', 'role' => 'asc']);
});

it('has pivot filter trait method', function () {
    $relation = new BelongsToMany(User::class);

    expect(method_exists($relation, 'applyPivotFilters'))->toBeTrue();
});