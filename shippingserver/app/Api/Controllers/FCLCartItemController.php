<?php

namespace Api\Controllers;

use Api\Modules\FCL\FCLCartItemFactory;

class FCLCartItemController extends AbstractCartItemController
{


    public function __construct()
    {
        $this->serviceFactory = new FCLCartItemFactory();

    }

}