<?php

namespace Api\Controllers;

use Api\Modules\AbstractServiceFactory;
use Api\Modules\LCL\LCLSellerPostFactory;
use Api\Services\ISellerPostService;

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