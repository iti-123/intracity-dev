<?php
/**
 * Created by PhpStorm.
 * User: sainath
 * Date: 2/20/17
 * Time: 3:20 PM
 */

namespace ApiV2\Controllers;

use ApiV2\Modules\FCL\FCLOrderFactory;

class FCLOrderController extends AbstractOrderController
{

    public function __construct()
    {
        $this->serviceFactory = new FCLOrderFactory();
        $this->serviceUiName = 'fcl';
    }


}