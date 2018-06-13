<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 9:49 AM
 */

namespace Api\Controllers;

//use Api\Controllers\AbstractSellerQuoteController;
use Api\Modules\FCL\FCLSelleQuoteFactory;

class FCLSellerQuoteControllers extends AbstractSellerQuoteController implements ISellerQuoteControllers
{

    public function __construct()
    {
        $this->serviceFactory = new FCLSelleQuoteFactory();
    }

}