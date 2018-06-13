<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 14:33
 */

namespace Api\Modules\FCL;

use Api\Framework\ISellerPostTransformer;
use Api\Framework\SerializerServiceFactory;
use Api\Services\UserDetailsService;
use App\Exceptions\ApplicationException;
use League\Fractal\TransformerAbstract;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

//use Api\Transformers\SellerPostBOTransformer;

class FCLSellerPostTransformer extends TransformerAbstract implements ISellerPostTransformer
{


    public function ui2bo_save($payload)
    {

        $serializer = SerializerServiceFactory::create();
        $post = $serializer->deserialize($payload, 'Api\Modules\FCL\FCLSellerPostBO', 'json');
        $post->serviceId = FCL;
        return $post;

    }

    public function ui2bo_filter($payload)
    {
        $serializer = SerializerServiceFactory::create();
        $post = $serializer->deserialize($payload, 'Api\Modules\FCL\FCLSellerPostSearchBO', 'json');
        return $post;
    }

    public function ui2bo_postmaster_filter($payload)
    {
        $serializer = SerializerServiceFactory::create();
        $bo = $serializer->deserialize($payload, 'Api\Modules\FCL\FCLSellerPostMasterOutboundBO', 'json');
        return $bo;
    }

    public function ui2bo_postmasterinbound_filter($payload)
    {
        $serializer = SerializerServiceFactory::create();
        $bo = $serializer->deserialize($payload, 'Api\Modules\FCL\FCLSellerPostMasterInboundSearchBO', 'json');
        return $bo;
    }

    /**
     *
     * @param $model
     * @return array
     */
    public function model2boGet($model)
    {
//TODO: Implement model2boGet() method.
        $visibleToSellers = array();
        $model = $model["attributes"];
        $model['postId'] = $model["id"];
        $model['sellerName'] = UserDetailsService::getUserDetails($model["seller_id"])->username;
        //$model['sellerName'] = UserDetailsService::getUserDetails($model["sellerId"])->username;
        $model['attributes'] = json_decode($model["attributes"]);
        $visibleToBuyers = []; //$this->getVisibleToBuyersBuyerId($model);
        $model['visibleToBuyers'] = $visibleToBuyers;
        unset($model["id"]);
        return $model;
        // TODO: Implement model2boGet() method.
    }

    public function model2boGetAll($model)
    {
        //  dd($models);
        //TODO: Implement model2boGet() method.
        $visibleToSellers = array();
        for ($i = 0; $i < sizeof($model); $i++) {
            // dd( $models[$i]['id']);
            //$model[$i] = $models[$i]["attributes"];
            $model[$i]['postId'] = $model[$i]["id"];
            $model[$i]['attributes'] = json_decode($model[$i]["attributes"]);

            $visibleToSellers = []; //$this->getVisibleToBuyersBuyerId($model[$i]);
            $model[$i]['visibleToSellers'] = $visibleToSellers;
            unset($model[$i]["id"]);
        }
        return $model;
    }

    public function model2boSave($model)
    {
        $response = 'FCLSellerPostTransformer.model2boSave() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function getVisibleToBuyersBuyerId($model)
    {
        $sellectedBuyer = SelectedBuyers::where('post_id', $model["id"])->select('buyer_id')->get();
        foreach ($sellectedBuyer as $val) {
            $visibleToBuyers[] = $val->buyer_id;
        }
        return $visibleToBuyers;
    }

    public function bo2modelDelete($bo)
    {
        $response = 'FCLSellerPostTransformer.bo2modelDelete() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function model2boDelete($model)
    {
        $response = 'FCLSellerPostTransformer.model2boDelete() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function bo2modelSave($bo)
    {
        $response = 'FCLSellerPostTransformer.bo2modelSave() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function bo2modelGet($bo)
    {

        //$mapper = new JsonMapper();
        //$contactObject = $mapper->map($bo, new SellerPostBO());

        $response = 'FCLSellerPostTransformer.bo2modelGet() called';
        LOG::info($response);
        return ((array)$response);
    }


    public function xls2bo_save(array $master = [], array $details = [], array $discounts = [])
    {

        $bo = new FCLSellerPostBO();

        ///----- Process Master Sheet --------

        $bo->sellerId = JWTAuth::parseToken()->getPayload()->get('id');
        $bo->serviceId = FCL;
        $bo->serviceSubType = "P2P";

        $bo->isPublic = $master[9] == 'Yes' ? true : false;
        $bo->title = $master[1];
        $bo->validFrom = $master[2];
        $bo->validTo = $master[3];
        $bo->status = "draft";
        $bo->viewCount = 0;

        $bo->termsConditions = $master[8];
        $bo->isTermAccepted = "true";

        $bo->visibleToBuyers = null; //TODO Should we remove this field completely? Talk to Karunya

        $bo->attributes = new FCLSellerPostAttributes();

        //capture selected payment types
        if ($master[4] == 'Yes') {
            array_push($bo->attributes->selectedPayment, "Cash");
        }
        if ($master[5] == 'Yes') {
            array_push($bo->attributes->selectedPayment, "NEFT");
        }
        if ($master[7] == 'Yes') {
            array_push($bo->attributes->selectedPayment, "Credit");
        }


        ///---- Process Discounts Sheet ---------

        $size = count($discounts);
        $counter = 0;

        LOG::debug("Discounts setup => [" . $size . "]");

        $portLevelDiscounts = [];

        if ($size > 0) {

            foreach ($discounts as $row) {

                LOG::debug($row);

                LOG::debug("Processing Discounts for Buyer [" . $row[0] . "]");

                $discount = new Discount();
                $buyerId = UserDetailsService::getUserByEmail($row[0]);

                if (!isset($buyerId)) {
                    throw new ApplicationException(["sheet" => "Discounts", "row" => $counter], "No buyer found with email " . $row[0]);
                }

                $discount->buyerId = $buyerId;
                $discount->discountType = $row[3];
                $discount->discount = $row[4];
                $discount->creditDays = $row[5];

                if (!isset($row[1])) {
                    //This is a global discount. Add it.
                    array_push($bo->attributes->discount, $discount);

                } else {

                    //This is a port level discount
                    $portPair = $row[1] . "-" . $row[2];

                    LOG::debug("Processing port level discount for " . $portPair);

                    //store the discount by port-pair for future processing.
                    if (isset($portLevelDiscounts[$portPair])) {
                        $portDisc = $portLevelDiscounts[$portPair];
                    } else {
                        $portDisc = [];
                    }

                    array_push($portDisc, $discount);

                    $portLevelDiscounts[$portPair] = $portDisc;
                }

            }

        }

        LOG::debug("Port level buyer discounts setup are => ");
        LOG::debug($portLevelDiscounts);

        ///----- Process Detail Sheet --------

        $portPair = null;
        $carrier = null;
        $container = null;
        $freightCharge = null;
        $localCharge = null;


        $size = count($details);
        $counter = 0;

        if ($size > 0) {


            foreach ($details as $row) {

                //LOG::debug("Processing Detail Row [" . $counter . "]");

                if ($counter == 0) {

                    //Initialize
                    $portPair = $this->xl2portPair($row);
                    $carrier = $this->xl2carrier($row);
                    $container = $this->xl2container($row);
                }

                if ($row[0] != $portPair->loadPort || $row[1] != $portPair->dischargePort) {

                    //New port pair detected. Stow away the old pair and start anew

                    array_push($carrier->containers, $container);
                    array_push($portPair->carriers, $carrier);

                    //Store port level discounts.
                    if (array_key_exists($portPair->loadPort . "-" . $portPair->dischargePort, $portLevelDiscounts)) {
                        $portPair->discount = $portLevelDiscounts [$portPair->loadPort . "-" . $portPair->dischargePort];
                    }

                    array_push($bo->attributes->portPair, $portPair);

                    $portPair = $this->xl2portPair($row);
                    $carrier = $this->xl2carrier($row);
                    $container = $this->xl2container($row);

                }

                if ($row[2] != $carrier->carrierName) {

                    //New carrier detected. Stow away the old carrier and start anew
                    array_push($carrier->containers, $container);
                    array_push($portPair->carriers, $carrier);

                    $carrier = $this->xl2carrier($row);
                    $container = $this->xl2container($row);
                }

                if ($row[11] != $container->containerType) {

                    //New container detected. Stow away the old container and start anew
                    array_push($carrier->containers, $container);

                    $container = $this->xl2container($row);

                }

                $this->xl2freightCharges($row, $container);

                $this->xl2localCharges($row, $container);

                $counter = $counter + 1;
            }

            //Finish the process.
            array_push($carrier->containers, $container);
            array_push($portPair->carriers, $carrier);

            //Store port level discounts.
            if (array_key_exists($portPair->loadPort . "-" . $portPair->dischargePort, $portLevelDiscounts)) {
                $portPair->discount = $portLevelDiscounts [$portPair->loadPort . "-" . $portPair->dischargePort];
            }

            array_push($bo->attributes->portPair, $portPair);

        }

        return $bo;

    }


    private function xl2portPair(array $row)
    {

        $portPair = new FCLSellerPortPair();

        $portPair->loadPort = $row[0];
        $portPair->dischargePort = $row[1];

        LOG::debug("Processing port pair [" . $portPair->loadPort . " - " . $portPair->dischargePort . "]");

        return $portPair;

    }

    private function xl2carrier(array $row)
    {

        $carrier = new SellerCarriers();
        $carrier->carrierName = $row[2];
        $carrier->etd = $row[3]; //TODO : Does this need to be converted to a unixtime?
        $carrier->cyCutOffDate = $row[4]; //TODO : Should this field be renamed to cut-off time?.
        $carrier->transitDays = $row[5];
        $carrier->tracking = $row[6];
        $carrier->routingType = $row[7];

        if ($carrier->routingType == 'Via') {

            $carrier->routingVia = new routingVia();
            $carrier->routingVia->port1 = $row[8];
            $carrier->routingVia->port2 = $row[9];
            $carrier->routingVia->port3 = $row[10];

        }

        LOG::debug("Processing carrier [" . $carrier->carrierName . "]");

        return $carrier;

    }

    private function xl2container(array $row)
    {

        $container = new SellerContainer();
        $container->containerType = $row[11];

        LOG::debug("Processing container [" . $container->containerType . "]");

        return $container;

    }

    private function xl2freightCharges(array $row, SellerContainer $container)
    {

        if (!isset($row[12])) {
            return;
        }

        $freightChg = new Charge();
        $freightChg->chargeType = $row[12];
        $freightChg->currency = $row[13];
        $freightChg->amount = $row[14];

        if (is_null($container->freightCharges)) {
            $container->freightCharges = $freightChg;
        } else {
            array_push($container->freightCharges, $freightChg);
        }
    }

    private function xl2localCharges(array $row, SellerContainer $container)
    {

        if (!isset($row[15])) {
            return;
        }

        $chg = new Charge();
        $chg->chargeType = $row[15];
        $chg->currency = $row[16];
        $chg->amount = $row[17];

        if (is_null($container->localCharges)) {
            $container->localCharges = $chg;
        } else {
            array_push($container->localCharges, $chg);
        }


    }


}

class Charge
{

    public $chargeType;
    public $currency;
    public $amount;

}

