<?php

namespace ApiV2\Controllers;

use ApiV2\Modules\AbstractServiceFactory;
use ApiV2\Modules\FCL\FCLSellerPostFactory;
use ApiV2\Services\ISellerPostService;

/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 16:13
 *
 */
class FCLSellerPostController extends AbstractSellerPostController implements ISellerPostController
{

    /**
     * FCLSellerPostController constructor.
     * @param AbstractServiceFactory $serviceFactory
     * @param ISellerPostService $service
     */
    public function __construct()
    {
        $this->serviceFactory = new FCLSellerPostFactory();
    }


}