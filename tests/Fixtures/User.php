<?php

namespace Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use RelationAttributes\Concerns\HasRelationAttributes;

class User extends Model
{
    use HasRelationAttributes;
}
