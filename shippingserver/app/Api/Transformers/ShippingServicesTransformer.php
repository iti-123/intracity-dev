<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 2/7/2017
 * Time: 11:51 AM
 */

namespace Api\Transformers;

use Api\Utils\EmailService;
use App\ShippingServices as ShippingServices;
use League\Fractal\TransformerAbstract;

class ShippingServicesTransformer extends TransformerAbstract
{


    public static function transform()
    {
        $status = 'succ';
        $shippingservices = ShippingServices::all();
        return json_encode($shippingservices);


        //Log::info( array_push($return_menu_tree,$status));
        //Log::info ($return_menu_tree);
        //return json_encode($shippingservices);
    }
}