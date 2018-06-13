<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 4/10/2017
 * Time: 12:21 PM
 */

namespace ApiV2\Controllers;

//use ApiV2\Controllers\AbstractSellerQuoteController;
use ApiV2\Modules\LCL\LCLSelleQuoteFactory;

class LCLSellerQuoteControllers extends AbstractSellerQuoteController implements ISellerQuoteControllers
{
    public function __construct()
    {
        $this->serviceFactory = new LCLSelleQuoteFactory();
    }

}