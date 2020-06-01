<?php

namespace Denngarr\Seat\Fitting\Models\Sde;

use Illuminate\Database\Eloquent\Model;

class DgmTypeAttributes extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $table = 'dgmTypeAttributes';

    protected $primaryKey = 'typeID, attributeID';
}
