<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/23/2017
 * Time: 4:01 PM
 */

namespace ApiV2\Controllers;

use ApiV2\Modules\AirFreight\AirFreightSellerPostFactory;

class AirFreightSellerPostController extends AbstractSellerPostController implements ISellerPostController
{


    public function __construct()
    {
        $this->serviceFactory = new AirFreightSellerPostFactory();

    }

}