<?php

namespace Denngarr\Seat\Fitting\Http\Controllers;

use Seat\Api\Http\Controllers\Api\v2\ApiController;
use Denngarr\Seat\Fitting\Http\Controllers\FittingController;

/**
 * Class ApiFittingController.
 * @package Denngarr\Seat\Fitting\Http\Controllers
 */
class ApiFittingController extends ApiController
{

    public function getFittingList()
    {
        return (new FittingController())->getFittingList();
    }

    public function getFittingById($id)
    {
        return (new FittingController())->getFittingById($id);
    }

    public function getDoctrineList()
    {
        return (new FittingController())->getDoctrineList();
    }

    public function getDoctrineById($id)
    {
        return (new FittingController())->getDoctrineById($id);
    }
}
