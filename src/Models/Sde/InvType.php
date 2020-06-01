<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 29/12/2017
 * Time: 15:13
 */

namespace Denngarr\Seat\Fitting\Models\Sde;

use Illuminate\Database\Eloquent\Model;

class InvType extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $table = 'invTypes';

    protected $primaryKey = 'typeID';
}
