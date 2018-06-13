<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 4/10/2017
 * Time: 12:21 PM
 */

namespace Api\Controllers;

//use Api\Controllers\AbstractSellerQuoteController;
use Api\Modules\LCL\LCLSelleQuoteFactory;

class LCLSellerQuoteControllers extends AbstractSellerQuoteController implements ISellerQuoteControllers
{
    public function __construct()
    {
        $this->serviceFactory = new LCLSelleQuoteFactory();
    }

}