<?php

namespace Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use RelationAttributes\Concerns\HasRelationAttributes;

class Team extends Model
{
    use HasRelationAttributes;
}
