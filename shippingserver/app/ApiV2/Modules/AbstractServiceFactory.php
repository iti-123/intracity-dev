<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 16:22
 */

namespace ApiV2\Modules;


use ApiV2\Modules\AirFreight\AirFreightBuyerPostFactory;
use ApiV2\Modules\AirFreight\AirFreightOrderFactory;
use ApiV2\Modules\AirFreight\AirFreightSelleQuoteFactory;
use ApiV2\Modules\AirFreight\AirFreightSellerPostFactory;
use ApiV2\Modules\FCL\FCLBuyerPostFactory;
use ApiV2\Modules\FCL\FCLOrderFactory;
use ApiV2\Modules\FCL\FCLSelleQuoteFactory;
use ApiV2\Modules\FCL\FCLSellerPostFactory;
use ApiV2\Modules\LCL\LCLBuyerPostFactory;
use ApiV2\Modules\LCL\LCLOrderFactory;
use ApiV2\Modules\LCL\LCLSelleQuoteFactory;
use ApiV2\Modules\LCL\LCLSellerPostFactory;
use App\Exceptions\ApplicationException;
use ApiV2\Model\BlueCollar\Post as BlueCollorBuyerPost;
use ApiV2\Model\BlueCollar\SellerRegistration;

use ApiV2\Model\Order as OrderFactory;


use ApiV2\Model\IntraHyperBuyerPost;
use ApiV2\Model\IntraHyperSellerPost;


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
            case _INTRACITY_:
                $factory = new IntraHyperBuyerPost();
                break;
            case _HYPERLOCAL_:
                $factory = new IntraHyperBuyerPost();
                break;
            case _BLUECOLLAR_:
                $factory = new BlueCollorBuyerPost();
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
            case _INTRACITY_:
                $factory = new IntraHyperSellerPost();
                break;
            case _HYPERLOCAL_:
                $factory = new IntraHyperSellerPost();
                break;
            case _BLUECOLLAR_:
                $factory = new SellerRegistration();
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
            case _INTRACITY_:
                $factory = new OrderFactory();
                break;
            case _HYPERLOCAL_:
                $factory = new OrderFactory();
                break;
            case _BLUECOLLAR_:
                $factory = new OrderFactory();
            break;
        }

        return $factory;
    }
}