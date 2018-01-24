<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 29/12/2017
 * Time: 15:13
 */

namespace Denngarr\Seat\Fitting\Models;

use Illuminate\Database\Eloquent\Model;

class Fitting extends Model
{
    public $timestamps = true;

    protected $table = 'seat-fitting';

    protected $fillable = ['id', 'shiptype', 'fitname', 'eftfitting'];
}
