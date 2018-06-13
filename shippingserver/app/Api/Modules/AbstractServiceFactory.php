<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 16:22
 */

namespace Api\Modules;


use Api\Modules\AirFreight\AirFreightBuyerPostFactory;
use Api\Modules\AirFreight\AirFreightOrderFactory;
use Api\Modules\AirFreight\AirFreightSelleQuoteFactory;
use Api\Modules\AirFreight\AirFreightSellerPostFactory;
use Api\Modules\FCL\FCLBuyerPostFactory;
use Api\Modules\FCL\FCLOrderFactory;
use Api\Modules\FCL\FCLSelleQuoteFactory;
use Api\Modules\FCL\FCLSellerPostFactory;
use Api\Modules\LCL\LCLBuyerPostFactory;
use Api\Modules\LCL\LCLOrderFactory;
use Api\Modules\LCL\LCLSelleQuoteFactory;
use Api\Modules\LCL\LCLSellerPostFactory;
use App\Exceptions\ApplicationException;


class AbstractServiceFactory implements IServiceFactory
{

    public function __construct()
    {
    }

    public static function getFactory($service, $usecase)
    {

        $factory = null;

        switch ($usecase) {

            case USECASE_BUYERPOST:
                $factory = self::getBuyerPostFactory($service);
                break;
            case USECASE_SELLERPOST:
                $factory = self::getSellerPostFactory($service);
                break;
            case USECASE_SELLERQUOTE:
                $factory = self::getSellerQuoteFactory($service);
                break;
            case USECASE_ORDERS:
                $factory = self::getOrderFactory($service);
                break;
        }
        if ($factory == null) {
            throw new ApplicationException([], ["9999" => "System Configuration error, No factory found for " . $service . "," . $usecase]);
        }
        return $factory;
    }

    public static function getBuyerPostFactory($service)
    {

        $factory = null;

        switch ($service) {

            case FCL:
                $factory = new FCLBuyerPostFactory();
                break;
            case LCL:
                $factory = new LCLBuyerPostFactory();
                break;

            case AirFreight:
                $factory = new AirFreightBuyerPostFactory();
                break;

        }

        return $factory;
    }

    public static function getSellerPostFactory($service)
    {

        $factory = null;

        switch ($service) {

            case FCL:
                $factory = new FCLSellerPostFactory();
                break;

            case LCL:
                $factory = new LCLSellerPostFactory();
                break;
            case AirFreight:
                $factory = new AirFreightSellerPostFactory();
                break;

        }

        return $factory;
    }

    public static function getSellerQuoteFactory($service)
    {

        $factory = null;

        switch ($service) {

            case FCL:
                $factory = new FCLSelleQuoteFactory();
                break;
            case LCL:
                $factory = new LCLSelleQuoteFactory();
                break;
            case AirFreight:
                $factory = new AirFreightSelleQuoteFactory();
                break;
        }

        return $factory;
    }

    public static function getOrderFactory($service)
    {

        $factory = null;

        switch ($service) {

            case FCL:
                $factory = new FCLOrderFactory();
                break;
            case LCL:
                $factory = new LCLOrderFactory();
                break;
            case AirFreight:
                $factory = new AirFreightOrderFactory();
                break;
        }

        return $factory;
    }
}