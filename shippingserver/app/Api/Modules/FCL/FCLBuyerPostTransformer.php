<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 14:33
 */

namespace Api\Modules\FCL;

use Api\Framework\IBuyerPostTransformer;
use Api\Framework\SerializerServiceFactory;
use Api\Model\SelectedSellers;
use Api\Modules\FCL\BuyerPost\Container;
use Api\Modules\FCL\BuyerPost\DestinationCustoms;
use Api\Modules\FCL\BuyerPost\ExportTPT;
use Api\Modules\FCL\BuyerPost\GOH;
use Api\Modules\FCL\BuyerPost\HazardousAttributes;
use Api\Modules\FCL\BuyerPost\ODC;
use Api\Modules\FCL\BuyerPost\OriginCustoms;
use Api\Modules\FCL\BuyerPost\SpecialCondition;
use Api\Modules\FCL\BuyerPost\TankTainer;
use Api\Modules\FCL\BuyerPost\TemperatureAttributes;
use Api\Services\UserDetailsService;
use Api\Utils\DateUtils;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class FCLBuyerPostTransformer implements IBuyerPostTransformer
{

    public function ui2bo_save($payload, $leadType)
    {

        //$leadType = json_decode($payload)->leadType;

        //Convert the request JSON into a BO
        $serializer = SerializerServiceFactory::create();
        if ($leadType == "contract") {
            $post = $serializer->deserialize($payload, 'Api\Modules\FCL\FCLContractBO', 'json');
        } else if ($leadType == "spot") {
            $post = $serializer->deserialize($payload, 'array<Api\Modules\FCL\FCLSpotBuyerPostBO>', 'json');
        } else {
            $post = $serializer->deserialize($payload, 'Api\Modules\FCL\FCLTermBuyerPostBO', 'json');
        }
        return $post;

    }

    public function ui2bo_filter($payload)
    {
        //Convert the request JSON into a BO
        $serializer = SerializerServiceFactory::create();
        $bo = $serializer->deserialize($payload, 'Api\Modules\FCL\FCLBuyerPostSearchBO', 'json');
        return $bo;
    }

    public function ui2bo_postmaster_filter($payload)
    {
        //Convert the request JSON into a BO
        $serializer = SerializerServiceFactory::create();
        $bo = $serializer->deserialize($payload, 'Api\Modules\FCL\FCLBuyerPostMasterOutboundBO', 'json');
        return $bo;
    }

    public function model2boGet($model)
    {
        //TODO: Implement model2boGet() method.
        $visibleToSellers = array();
        $model = $model["attributes"];
        $model['postId'] = $model["id"];
        $model['buyerName'] = UserDetailsService::getUserDetails($model["buyerId"])->username;
        $model['attributes'] = json_decode($model["attributes"]);
        $visibleToSellers = $this->getVisibleToSellersSellerId($model);
        $model['visibleToSellers'] = $visibleToSellers;
        unset($model["id"]);
        return $model;
    }

    public function getVisibleToSellersSellerId($model)
    {
        $sellectedSeller = SelectedSellers::where('post_id', $model["id"])->select('seller_id')->get();
        if (empty($sellectedSeller))
            return [];
        foreach ($sellectedSeller as $val) {
            $visibleToSellers[] = $val->seller_id;
        }
        if (empty($visibleToSellers))
            return [];
        return $visibleToSellers;
    }

    public function model2boGetAll($model)
    {
        $visibleToSellers = array();
        for ($i = 0; $i < sizeof($model); $i++) {
            //$model[$i] = $models[$i]["attributes"];
            $model[$i]['postId'] = $model[$i]["id"];
            $model[$i]['attributes'] = json_decode($model[$i]["attributes"]);
            $visibleToSellers = $this->getVisibleToSellersSellerId($model[$i]);
            $model[$i]['visibleToSellers'] = $visibleToSellers;
            unset($model[$i]["id"]);
        }
        return $model;
    }

    public function model2boSave($model)
    {
        $response = 'FCLBuyerPostTransformer.model2boSave() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function bo2modelDelete($bo)
    {
        $response = 'FCLBuyerPostTransformer.bo2modelDelete() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function model2boDelete($model)
    {
        $response = 'FCLSellerPostTransformer.model2boDelete() called';
        LOG::info($response);
        return ((array)$response);
    }


    public function bo2modelGet($bo)
    {

        //$mapper = new JsonMapper();
        //$contactObject = $mapper->map($bo, new SellerPostBO());

        $response = 'FCLBuyerPostTransformer.bo2modelGet() called';
        LOG::info($response);
        return ((array)$response);
    }


    public function spot_xls2bo_save(array $master = [], array $details = [], array $sellers = [])
    {

        ///----- Process Detail Sheet --------

        $bos = [];

        $bo = null;
        $route = null;
        $container = null;

        $size = count($details);
        $counter = 0;

        if ($size > 0) {

            foreach ($details as $row) {

                LOG::debug("Processing Detail Row [" . $counter . "]");

                if ($counter == 0) {

                    //Initialize
                    $bo = $this->spot_xls2base($master, $sellers);

                    $bo->title = $row[0];
                    $route = $this->spot_xl2route($row, $bo->userTimezone);


                }

                if ($row[1] != $route->originLocation || $row[2] != $route->destinationLocation ||
                    $row[3] != $route->loadPort || $row[4] != $route->dischargePort) {

                    //New route detected. Stow away the old route and start anew
                    $bo->attributes->route = $route;
                    array_push($bos, $bo);

                    $bo = $this->spot_xls2base($master, $sellers);

                    $bo->title = $row[0];
                    $route = $this->spot_xl2route($row, $bo->userTimezone);
                }


                $container = $this->spot_xl2container($row);
                array_push($route->containers, $container);

                $counter = $counter + 1;
            }

            //Finish the process.
            $bo->attributes->route = $route;
            array_push($bos, $bo);

        }

        return $bos;

    }


    private function spot_xls2base(array $master = [], array $sellers = [])
    {

        $bo = new FCLSpotBuyerPostBO();

        $bo->attributes = new FCLBuyerPostAttributes();

        ///----- Process Master Sheet --------

        $bo->buyerId = JWTAuth::parseToken()->getPayload()->get('id');
        $bo->serviceId = _HYPERLOCAL_; //TODO donot hard code service-id

        $bo->leadType = 'spot';
        $bo->isTermAccepted = "true";
        $bo->status = "draft";
        $bo->viewCount = 0;

        //Timestr will be of the form yyyymmdd-hhmmss. Multiply by 100 since user will not enter seconds info)
        $timestr = $master[1] . '-' . ($master[2] * 100);
        $userTimezone = $master[3];

        $bo->userTimezone = $userTimezone;

        LOG::debug($master);

        $bo->lastDateTimeOfQuoteSubmission = DateUtils::parseDateTime($timestr, $userTimezone);

        $bo->isPublic = $master[4] == 'Yes' ? true : false;

        ///---- Process Sellers Sheet ---------

        $size = count($sellers);
        $counter = 0;

        LOG::debug("Sellers setup => [" . $size . "]");

        if ($size > 0) {

            foreach ($sellers as $row) {

                //TODO: Thsi code has to be changed every time getting only first item what about other items -- TBD Karunya
                LOG::debug("Registering Private Invite to Seller [" . $row[0] . "]");

                $sellerId = UserDetailsService::getUserByEmail($row[0]);
                LOG::debug("UserDetailsService::getUserByEmail => [" . $sellerId . "]");

                if (($sellerId != null) && ($sellerId != "")) {
                    //This is a global discount. Add it.
                    array_push($bo->visibleToSellers, $sellerId);
                } else {

                    // $errors[100] = "No Seller found with email " . $row[0];
                    // throw new ApplicationException([ "sheet" => "Sellers", "row" => $counter], $errors);
                }

            }

        }

        return $bo;
    }

    private function spot_xl2route(array $row, $userTimezone)
    {

        $route = new Route();
        $route->originLocation = $row[1];
        $route->destinationLocation = $row[2];

        if (isset($route->originLocation) && isset($route->destinationLocation)) {
            $route->serviceSubType = 'D2D';
        } elseif (isset($route->originLocation) && !isset($route->destinationLocation)) {
            $route->serviceSubType = 'D2P';
        } elseif (!isset($route->originLocation) && isset($route->destinationLocation)) {
            $route->serviceSubType = 'P2D';
        } else {
            $route->serviceSubType = 'P2P';
        }

        $route->loadPort = $row[3];
        $route->dischargePort = $row[4];
        $route->incoTerms = $row[5];
        $route->commodity = $row[6];
        $route->commodityDescription = $row[7];
        $route->cargoReadyDate = DateUtils::parseDate($row[8], $userTimezone);
        $route->priceType = $row[9];
        $route->isFumigationRequired = ($row[10] == 'Yes' ? true : false);
        $route->isFactoryStuffingRequired = ($row[11] == 'Yes' ? true : false);
        $route->isHazardous = ($row[12] == 'Yes' ? true : false);

        if ($route->isHazardous == true) {

            $hazardousAttributes = new HazardousAttributes();

            $hazardousAttributes->imoClass = $row[13];
            $hazardousAttributes->imoSubclass = $row[14];
            $hazardousAttributes->flashpoint = $row[15];
            $hazardousAttributes->flashpointUnit = $row[16];
            $hazardousAttributes->properShippingName = $row[17];
            $hazardousAttributes->technicalName = $row[18];
            $hazardousAttributes->packingGroup = $row[19];

            $route->hazardousAttributes = $hazardousAttributes;
        }

        $specialConditions = $row[20];
        $route->specialConditionType = $row[20];
        $route->specialConditions = new SpecialCondition();

        if ($specialConditions == 'temperature') {

            $temperature = new TemperatureAttributes();
            $temperature->consignmentType = $row[21];
            $temperature->temperatureUnit = $row[22];
            $temperature->temperature = $row[23];

            $route->specialConditions->temperatureAttributes = $temperature;

        } elseif ($specialConditions == 'odc') {

            $odc = new ODC();

            $odc->dimensionUnit = $row[24];
            $odc->length = $row[25];
            $odc->breadth = $row[26];
            $odc->height = $row[27];
            $odc->weightUnit = $row[28];
            $odc->grossWeight = $row[29];
            $odc->packagingMethod = $row[30];

            $route->specialConditions->ODC = $odc;

        } elseif ($specialConditions == 'goh') {

            $goh = new GOH();

            $goh->numberOfBars = $row[31];
            $goh->numberOfRopes = $row[32];
            $goh->knotsPerRope = $row[33];

            $route->specialConditions->GOH = $goh;

        } elseif ($specialConditions == 'tanktainer') {

            $tanktainer = new TankTainer();

            $tanktainer->commodity = $row[34];
            $tanktainer->weightUnit = $row[35];
            $tanktainer->grossWeight = $row[36];

            $route->specialConditions->tankTainers = $tanktainer;
        }

        //Shipping Bill Type	Number of Bills	Is Returnable  (Yes/No)	Returnable  Category	Other Instructions

        $originCustoms = new OriginCustoms();
        $originCustoms->shippingBillType = $row[37];
        $originCustoms->numberOfBills = $row[38];
        $originCustoms->isReturnable = ($row[39] == 'Yes' ? true : false);
        $originCustoms->returnableCategory = $row[40];
        $originCustoms->otherInstructions = $row[41];

        $route->originCustoms = $originCustoms;

        //Import Bill Type	Number of Bills	Trailer Type
        $destinationCustoms = new DestinationCustoms();
        $destinationCustoms->importBillType = $row[42];
        $destinationCustoms->numberOfBills = $row[43];

        $route->destinationCustoms = $destinationCustoms;

        $exportTpt = new ExportTPT();
        $exportTpt->trailerType = $row[44];

        $route->exportTPT = $exportTpt;

        LOG::debug("Processing route [" . $route->originLocation . ' - ' . $route->loadPort . " - " . $route->dischargePort . '-' . $route->destinationLocation . "]");

        return $route;

    }

    private function spot_xl2container(array $row)
    {

        //Container Type	Quantity	Weight Unit	Gross Weight

        $container = new Container();
        $container->containerType = $row[45];
        $container->quantity = $row[46];
        $container->weightUnit = $row[47];
        $container->grossWeight = $row[48];
        $container->freightCharge = $row[49];

        LOG::debug("Processed container [" . $container->containerType . "]");

        return $container;

    }

    public function term_xls2bo_save(array $master = [], array $details = [], array $sellers = [])
    {

        $bo = new FCLTermBuyerPostBO();

        $bo->attributes = new FCLTermBuyerPostAttributes();

        ///----- Process Master Sheet --------

        $bo->buyerId = JWTAuth::parseToken()->getPayload()->get('id');
        $bo->serviceId = FCL;

        $bo->leadType = 'term';
        $bo->isTermAccepted = "true";
        $bo->status = "draft";
        $bo->viewCount = 0;

        $bo->title = $master[1];

        //convert input hhmm into hhmmss and overall datestring to yyyymmdd-hhmmss
        $timestr = $master[2] . '-' . ($master[3] * 100);
        $userTimezone = $master[4];

        $bo->lastDateTimeOfQuoteSubmission = DateUtils::parseDateTime($timestr, $userTimezone);

        $bo->attributes->emdMode = $master[5];
        $bo->attributes->emdText = $master[6];
        $bo->attributes->emdAmount = $master[7];

        $bo->attributes->contractAllotment = $master[8];

        //TODO The below field selectedPayment is not defined in the Attributes type.

        //capture selected payment types
//        if($master[9] == 'Yes'){
//            array_push($bo->attributes->selectedPayment , "Cash");
//        }
//        if($master[10] == 'Yes'){
//            array_push($bo->attributes->selectedPayment , "NEFT");
//        }
//        if($master[11] == 'Yes'){
//            array_push($bo->attributes->selectedPayment , "Credit");
//        }
//        if($master[12] == 'Yes'){
//            array_push($bo->attributes->selectedPayment , "Debit");
//        }

        $bo->attributes->creditDays = $master[13];
        $bo->attributes->paymentTerms = $master[14];

        $rfpEligibility = new RfpEligibility();
        $rfpEligibility->avgTurnOverLastThreeYears = $master[15];
        $rfpEligibility->incometaxAssement = ($master[16] == 'Yes' ? true : false);
        $rfpEligibility->numberOfYearsInBusiness = $master[17];
        $rfpEligibility->termContractWithOther = ($master[18] == 'Yes' ? true : false);

        $bo->attributes->rfpEligibility = $rfpEligibility;

        $bo->isPublic = $master[19] == 'Yes' ? true : false;

        ///---- Process Sellers Sheet ---------

        $size = count($sellers);
        $counter = 0;

        LOG::debug("Sellers setup => [" . $size . "]");

        if ($size > 0) {

            foreach ($sellers as $row) {

                //TODO: Thsi code has to be changed every time getting only first item what about other items -- TBD Karunya
                LOG::debug("Registering Private Invite to Seller [" . $row[0] . "]");

                $sellerId = UserDetailsService::getUserByEmail($row[0]);

                if (($sellerId != null) && ($sellerId != "")) {
                    //This is a global discount. Add it.
                    array_push($bo->visibleToSellers, $sellerId);
                } else {
                    //$errors[100] = "No Seller found with email " . $row[0];
                    //throw new ApplicationException([ "sheet" => "Sellers", "row" => $counter], $errors);
                }

            }

        }

        ///----- Process Detail Sheet --------

        $serviceType = null;
        $route = null;
        $container = null;

        $size = count($details);
        $counter = 0;

        if ($size > 0) {

            foreach ($details as $row) {

                LOG::debug("Processing Detail Row [" . $counter . "]");

                if ($counter == 0) {

                    //Initialize
                    $serviceType = $this->term_xl2serviceType($row);
                    $route = $this->term_xl2route($row, $userTimezone);

                }

                if ($row[0] != $serviceType->originLocation || $row[1] != $serviceType->destinationLocation) {

                    //New service detected. Stow away the old service and start anew

                    //array_push($bo->attributes->serviceType$attroute->containers, $container);
                    array_push($serviceType->routes, $route);
                    array_push($bo->attributes->serviceType, $serviceType);

                    $serviceType = $this->term_xl2serviceType($row);
                    $route = $this->term_xl2route($row, $userTimezone);

                }

                if ($row[2] != $route->loadPort || $row[3] != $route->dischargePort) {

                    //New route detected. Stow away the old route and start anew
                    array_push($serviceType->routes, $route);

                    $route = $this->term_xl2route($row, $userTimezone);
                }


                $container = $this->term_xl2container($row);
                array_push($route->containers, $container);

                $counter = $counter + 1;
            }

            //Finish the process.
            array_push($serviceType->routes, $route);
            array_push($bo->attributes->serviceType, $serviceType);

        }

        return $bo;

    }

    private function term_xl2serviceType(array $row)
    {
        $serviceType = new ServiceType();
        $serviceType->originLocation = $row[0];
        $serviceType->destinationLocation = $row[1];
        if (isset($serviceType->originLocation) && isset($serviceType->destinationLocation)) {
            $serviceType->serviceSubType = 'D2D';
        } elseif (isset($serviceType->originLocation) && !isset($serviceType->destinationLocation)) {
            $serviceType->serviceSubType = 'D2P';
        } elseif (!isset($serviceType->originLocation) && isset($serviceType->destinationLocation)) {
            $serviceType->serviceSubType = 'P2D';
        } else {
            $serviceType->serviceSubType = 'P2P';
        }

        $originCustoms = new OriginCustoms();
        $originCustoms->shippingBillType = $row[35];
        $originCustoms->numberOfBills = $row[36];
        $originCustoms->isReturnable = ($row[37] == 'Yes' ? true : false);
        $originCustoms->returnableCategory = $row[38];
        $originCustoms->otherInstructions = $row[39];

        $serviceType->originCustoms = $originCustoms;

        //Import Bill Type	Number of Bills	Trailer Type
        $destinationCustoms = new DestinationCustoms();
        $destinationCustoms->importBillType = $row[40];
        $destinationCustoms->numberOfBills = $row[41];

        $serviceType->destinationCustoms = $destinationCustoms;

        $exportTpt = new ExportTPT();
        $exportTpt->trailerType = $row[42];

        $serviceType->exportTPT = $exportTpt;

        LOG::debug("Processing port pair [" . $serviceType->originLocation . " - " . $serviceType->destinationLocation . "]");
        return $serviceType;
    }

    private function term_xl2route(array $row, $userTimezone)
    {

        $route = new TermRoute();

        $route->loadPort = $row[2];
        $route->dischargePort = $row[3];
        $route->incoTerms = $row[4];
        $route->commodity = $row[5];
        $route->commodityDescription = $row[6];

        $route->cargoReadyDate = DateUtils::parseDate($row[7], $userTimezone);
        $route->isFumigationRequired = ($row[8] == 'Yes' ? true : false);
        $route->isFactoryStuffingRequired = ($row[9] == 'Yes' ? true : false);
        $route->isHazardous = ($row[10] == 'Yes' ? true : false);

        if ($route->isHazardous == true) {

            $hazardousAttributes = new HazardousAttributes();

            $hazardousAttributes->imoClass = $row[11];
            $hazardousAttributes->imoSubclass = $row[12];
            $hazardousAttributes->flashpoint = $row[13];
            $hazardousAttributes->flashpointUnit = $row[14];
            $hazardousAttributes->properShippingName = $row[15];
            $hazardousAttributes->technicalName = $row[16];
            $hazardousAttributes->packingGroup = $row[17];

            $route->hazardousAttributes = $hazardousAttributes;
        }

        $specialConditions = $row[18];
        $route->specialConditionType = $row[18];
        $route->specialConditions = new SpecialCondition();

        if ($specialConditions == 'temperature') {

            $temperature = new TemperatureAttributes();
            $temperature->consignmentType = $row[19];
            $temperature->temperatureUnit = $row[20];
            $temperature->temperature = $row[21];

            $route->specialConditions->temperatureAttributes = $temperature;

        } elseif ($specialConditions == 'odc') {

            $odc = new ODC();

            $odc->dimensionUnit = $row[22];
            $odc->length = $row[23];
            $odc->breadth = $row[24];
            $odc->height = $row[25];
            $odc->weightUnit = $row[26];
            $odc->grossWeight = $row[27];
            $odc->packagingMethod = $row[28];

            $route->specialConditions->ODC = $odc;

        } elseif ($specialConditions == 'goh') {

            $goh = new GOH();

            $goh->numberOfBars = $row[29];
            $goh->numberOfRopes = $row[30];
            $goh->knotsPerRope = $row[31];

            $route->specialConditions->GOH = $goh;

        } elseif ($specialConditions == 'tanktainer') {

            $tanktainer = new TankTainer();

            $tanktainer->commodity = $row[32];
            $tanktainer->weightUnit = $row[33];
            $tanktainer->grossWeight = $row[34];

            $route->specialConditions->tankTainers = $tanktainer;
        }

        //Shipping Bill Type	Number of Bills	Is Returnable  (Yes/No)	Returnable  Category	Other Instructions

        /*  $originCustoms = new OriginCustoms();
          $originCustoms->shippingBillType = $row[35];
          $originCustoms->numberOfBills = $row[36];
          $originCustoms->isReturnable =  ( $row[37] == 'Yes' ? true : false );
          $originCustoms->returnableCategory = $row[38];
          $originCustoms->otherInstructions = $row[39];

          $route->originCustoms = $originCustoms;

          //Import Bill Type	Number of Bills	Trailer Type
          $destinationCustoms = new DestinationCustoms();
          $destinationCustoms->importBillType = $row[40];
          $destinationCustoms->numberOfBills = $row[41];

          $route->destinationCustoms = $destinationCustoms;

          $exportTpt = new ExportTPT();
          $exportTpt->trailerType = $row[42];

          $route->exportTPT = $exportTpt; */


        LOG::debug("Processing route [" . $route->loadPort . " - " . $route->dischargePort . "]");

        return $route;

    }

    private function term_xl2container(array $row)
    {

        //Container Type	Quantity	Weight Unit	Gross Weight

        $container = new Container();
        $container->containerType = $row[43];
        $container->quantity = $row[44];
        $container->weightUnit = $row[45];
        $container->grossWeight = $row[46];

        LOG::debug("Processed container [" . $container->containerType . "]");

        return $container;

    }

}