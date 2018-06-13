<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 16:52
 */

namespace Api\Modules;


/**
 * Interface IServiceFactory is responsible for manfacturing factories of factories.
 * @package Api\Modules
 */
interface IServiceFactory
{
    /**
     * Manufacture a factory for specified service and use case.
     * @param $service The service example FCL, LCL etc..
     * @param $usecase The use case example BuyerPost, SellerPost etc..
     * @return mixed a manufactured factory
     */
    public static function getFactory($service, $usecase);

}