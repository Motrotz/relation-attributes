<?php

use RelationAttributes\Attributes\BelongsTo;
use Tests\Fixtures\Team;
use Tests\Fixtures\User;

it('configures default relationship', function () {
    $user = new
        #[BelongsTo(Team::class)]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(BelongsTo::class);

    expect($attributes)
        ->toHaveCount(1);

    [$team] = $attributes;
    $relation = $team->newInstance();

    expect($relation)
        ->related->toBe(Team::class)
        ->and($relation->foreignKey)->toBeNull()
        ->and($relation->ownerKey)->toBeNull()
        ->and($relation->relation)->toBe('team');
});

it('configures custom relationship', function () {
    $user = new
        #[BelongsTo(Team::class, 'team_uuid', 'uuid', 'organization')]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(BelongsTo::class);

    expect($attributes)
        ->toHaveCount(1);

    [$team] = $attributes;
    $relation = $team->newInstance();

    expect($relation)
        ->related->toBe(Team::class)
        ->and($relation->foreignKey)->toBe('team_uuid')
        ->and($relation->ownerKey)->toBe('uuid')
        ->and($relation->relation)->toBe('organization');
});

it('allows multiple belongs to relationships', function () {
    $user = new
        #[BelongsTo(Team::class)]
        #[BelongsTo(Team::class, 'personal_team_id', 'id', 'personalTeam')]
        class extends User {};

    $attributes = (new ReflectionClass($user))
        ->getAttributes(BelongsTo::class);

    expect($attributes)->toHaveCount(2);

    [$team, $personal] = array_map(
        fn (ReflectionAttribute $attribute): BelongsTo => $attribute->newInstance(),
        $attributes
    );

    expect($team)
        ->related->toBe(Team::class)
        ->and($team->foreignKey)->toBeNull()
        ->and($team->ownerKey)->toBeNull()
        ->and($team->relation)->toBe('team');

    expect($personal)
        ->related->toBe(Team::class)
        ->and($personal->foreignKey)->toBe('personal_team_id')
        ->and($personal->ownerKey)->toBe('id')
        ->and($personal->relation)->toBe('personalTeam');
});
