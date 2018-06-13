<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 23/2/17
 * Time: 11:39 PM
 */

namespace ApiV2\Controllers;

use ApiV2\Modules\RoRo\RoRoBuyerPostFactory;

class RoRoBuyerPostController extends AbstractBuyerPostController implements IBuyerPostController
{


    public function __construct()
    {
        $this->serviceFactory = new RoRoBuyerPostFactory();

    }

}