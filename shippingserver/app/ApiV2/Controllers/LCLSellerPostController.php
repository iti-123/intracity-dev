<?php

namespace ApiV2\Controllers;

use ApiV2\Modules\AbstractServiceFactory;
use ApiV2\Modules\LCL\LCLSellerPostFactory;
use ApiV2\Services\ISellerPostService;

/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 16:13
 *
 */
class LCLSellerPostController extends AbstractSellerPostController implements ISellerPostController
{

    /**
     * LCLSellerPostController constructor.
     * @param AbstractServiceFactory $serviceFactory
     * @param ISellerPostService $service
     */
    public function __construct()
    {
        $this->serviceFactory = new LCLSellerPostFactory();
    }

}