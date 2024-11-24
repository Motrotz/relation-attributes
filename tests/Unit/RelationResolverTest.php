<?php

use Illuminate\Database\Eloquent\Model;
use RelationAttributes\Attributes\BelongsTo;
use RelationAttributes\Contracts\RelationAttribute;
use RelationAttributes\RelationResolver;

it('resolves belongsTo relation', function () {
    $relation = new BelongsTo(Model::class);
    $closure = (new RelationResolver)
        ->resolve($relation);

    expect($closure)
        ->toBeInstanceOf(Closure::class);
});

it('throws exception for unhandled relation', function () {
    $attribute = new class implements RelationAttribute {};

    expect(fn () => (new RelationResolver)->resolve($attribute))
        ->toThrow(LogicException::class);
});
