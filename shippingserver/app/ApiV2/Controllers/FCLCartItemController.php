<?php

namespace ApiV2\Controllers;

use ApiV2\Modules\FCL\FCLCartItemFactory;

class FCLCartItemController extends AbstractCartItemController
{


    public function __construct()
    {
        $this->serviceFactory = new FCLCartItemFactory();

    }

}