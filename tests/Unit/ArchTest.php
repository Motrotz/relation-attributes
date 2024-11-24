<?php

use RelationAttributes\Contracts\RelationAttribute;

arch()->preset()->php();
arch()->preset()->security();

arch()
    ->expect('RelationAttributes\Attributes')
    ->toImplement(RelationAttribute::class)
    ->toHaveAttribute(Attribute::class);

arch()
    ->expect('RelationAttributes\Concerns')
    ->toBeTrait();

arch()
    ->expect('RelationAttributes\Contracts')
    ->toBeInterface();
