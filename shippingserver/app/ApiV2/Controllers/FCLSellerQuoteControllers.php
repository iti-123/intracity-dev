<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 9:49 AM
 */

namespace ApiV2\Controllers;

//use ApiV2\Controllers\AbstractSellerQuoteController;
use ApiV2\Modules\FCL\FCLSelleQuoteFactory;

class FCLSellerQuoteControllers extends AbstractSellerQuoteController implements ISellerQuoteControllers
{

    public function __construct()
    {
        $this->serviceFactory = new FCLSelleQuoteFactory();
    }

}