<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/23/2017
 * Time: 4:08 PM
 */

namespace ApiV2\Modules\AirFreight;

use ApiV2\BusinessObjects\Common\DestinationCustoms;
use ApiV2\BusinessObjects\Common\ExportTPT;
use ApiV2\BusinessObjects\Common\HazardousAttributes;
use ApiV2\BusinessObjects\Common\OriginCustoms;
use ApiV2\BusinessObjects\Common\PackageDimensions;
use ApiV2\BusinessObjects\Common\RfpEligibility;
use ApiV2\Framework\IBuyerPostTransformer;
use ApiV2\Framework\SerializerServiceFactory;
use ApiV2\Model\SelectedSellers;
use ApiV2\Services\UserDetailsService;
use ApiV2\Utils\DateUtils;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;


class AirFreightBuyerPostTransformer implements IBuyerPostTransformer
{


    public function ui2bo_save($payload, $leadType)
    {

        //Convert the request JSON into a BO
        $serializer = SerializerServiceFactory::create();
        if ($leadType == "spot") {
            $post = $serializer->deserialize($payload, 'array<Api\Modules\AirFreight\AirFreightSpotBuyerPostBO>', 'json');
        } else {
            $post = $serializer->deserialize($payload, 'Api\Modules\AirFreight\AirFreightTermBuyerPostBO', 'json');
        }
        return $post;
    }

    public function ui2bo_filter($payload)
    {
        //Convert the request JSON into a BO
        $serializer = SerializerServiceFactory::create();
        $bo = $serializer->deserialize($payload, 'Api\Modules\AirFreight\AirFreightSpotBuyerPostBO', 'json');
        return $bo;
    }

    public function ui2bo_postmaster_filter($payload)
    {
        //Convert the request JSON into a BO
        $serializer = SerializerServiceFactory::create();
        $bo = $serializer->deserialize($payload, 'Api\Modules\AirFreight\AirFreightSpotBuyerPostBO', 'json');
        return $bo;
    }

    public function model2boGet($model)
    {
        //TODO: Implement model2boGet() method.
        $visibleToSellers = array();
        $model = $model["attributes"];
        $model['postId'] = $model["id"];
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
        $response = 'AirFreightBuyerPostTransformer.model2boSave() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function bo2modelDelete($bo)
    {
        $response = 'AirFreightBuyerPostTransformer.bo2modelDelete() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function model2boDelete($model)
    {
        $response = 'AirFreightBuyerPostTransformer.model2boDelete() called';
        LOG::info($response);
        return ((array)$response);
    }


    public function bo2modelGet($bo)
    {

        //$mapper = new JsonMapper();
        //$contactObject = $mapper->map($bo, new SellerPostBO());

        $response = 'AirFreightBuyerPostTransformer.bo2modelGet() called';
        LOG::info($response);
        return ((array)$response);
    }

    /**
     * Extract excel data and prepare Bos in the format required to save the Airfrieght information
     * @param array $master
     * @param array $details
     * @param array $sellers
     * @return array
     */
    public function spot_xls2bo_save(array $master = [], array $details = [], array $sellers = [])
    {

        ///----- Process Detail Sheet --------

        $bos = [];
        $bo = null;
        $route = null;
        $packageDimension = null;
        $size = count($details);
        $counter = 0;


        if ($size > 0) {

            foreach ($details as $row) {

                LOG::debug("Processing Detail Row [" . $counter . "]");

                if ($counter == 0) {

                    //Initialize
                    $bo = $this->spot_xls2base($master, $sellers);

                    //get the title of Post
                    $bo->title = $row[0];

                    //Prepare route
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

                $packageDimension = $this->xl2packageDimensions($row);
                LOG::debug('Before pushing to array ******************');
                //  LOG::debug((array) $route);
                $route->packageDimensions[] = $packageDimension;
                //  array_push($route->packageDimensions, $packageDimension);

                $counter = $counter + 1;
            }

            //Set the attributes
            //Finish the process.
            $bo->attributes->route = $route;
            array_push($bos, $bo);

        }
        return $bos;
    }

    /**
     * Process base information  from master and seller sheets of the uploaded excel
     * @param array $master
     * @param array $sellers
     * @return AirFreightSpotBuyerPostBO
     */
    private function spot_xls2base(array $master = [], array $sellers = [])
    {

        $bo = new AirFreightSpotBuyerPostBO();

        $bo->attributes = new AirFreightBuyerPostAttributes();

        ///----- Process Master Sheet --------

        $bo->buyerId = JWTAuth::parseToken()->getPayload()->get('id');

        $bo->leadType = 'spot';
        $bo->isTermAccepted = "true";
        $bo->status = "draft";
        $bo->viewCount = 0;


        //Timestr will be of the form yyyymmdd-hhmmss. Multiply by 100 since user will not enter seconds info)
        $timestr = $master[1] . '-' . ($master[2] * 100);
        $userTimezone = $master[3];
        $bo->userTimezone = $userTimezone;
        $bo->lastDateTimeOfQuoteSubmission = DateUtils::parseDateTime($timestr, $userTimezone);
        $bo->isPublic = $master[4] == 'Yes' ? true : false;
        //  $bo->overWriteIfExist = $master[5];
        $bo->serviceId = $master[6];
        LOG::debug($master);

        ///---- Process Sellers Sheet ---------

        $size = count($sellers);
        $counter = 0;

        LOG::debug("Sellers setup => [" . $size . "]");

        if ($size > 0) {

            foreach ($sellers as $row) {

                LOG::debug("Registering Private Invite to Seller [" . $row[0] . "]");

                $sellerId = UserDetailsService::getUserByEmail($row[0]);

                if (isset($sellerId)) {
                    //This is a global discount. Add it.
                    array_push($bo->visibleToSellers, $sellerId);
                } else {
                    throw new ApplicationException(["sheet" => "Sellers", "row" => $counter], "No Seller found with email " . $row[0]);
                }

            }

        }

        return $bo;
    }

    /**
     * Extract routing information from the upload excel and returns the route object
     * @param array $row
     * @param $userTimezone
     * @return Route
     */
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
        //  $route->incoTerms = $row[5];
        $route->commodity = $row[5];
        $route->commodityDescription = $row[6];
        $route->cargoReadyDate = DateUtils::parseDate($row[7], $userTimezone);
        $route->priceType = $row[8];
        $route->packagingType = $row[9];
        $route->isStackable = ($row[10] == 'Yes' ? true : false);
        $route->isRadioActive = ($row[11] == 'Yes' ? true : false);

        $route->airFreightType = $row[12];

        if ($route->airFreightType == 'Temperature Controlled') {

            $temperature = new TemperatureAttributes();
            $temperature->isActive = $row[13];
            $temperature->isPassive = $row[14];
            $temperature->temperature = $row[15];
            $temperature->temperatureTo = $row[16];
            $temperature->temperatureUnit = $row[17];

            $route->temperatureAttributes = $temperature;
        }

        $route->isHazardous = ($row[18] == 'Yes' ? true : false);
        if ($route->isHazardous == true) {

            $hazardousAttributes = new HazardousAttributes();

            $hazardousAttributes->imoClass = $row[19];
            $hazardousAttributes->imoSubclass = $row[20];
            $hazardousAttributes->flashpoint = $row[21];
            $hazardousAttributes->flashpointUnit = $row[22];
            $hazardousAttributes->properShippingName = $row[23];
            $hazardousAttributes->technicalName = $row[24];
            $hazardousAttributes->packingGroup = $row[25];

            $route->hazardousAttributes = $hazardousAttributes;
        }

        /*$packageDimension = new PackageDimensions();
        $packageDimension->length = $row[26];
        $packageDimension->breadth  = $row[27];
        $packageDimension->height = $row[28];
        $packageDimension->lbhUnit= $row[29];
        $packageDimension->noOfPackages= $row[30];
        $packageDimension->totalCBM= $row[31];
        $packageDimension->grossWeight= $row[32];
        $packageDimension->weightUnit= $row[33];
        $route->packageDimensions []= $packageDimension; */

        //Shipping Bill Type	Number of Bills	Is Returnable  (Yes/No)	Returnable  Category	Other Instructions

        $originCustoms = new OriginCustoms();
        $originCustoms->shippingBillType = $row[34];
        $originCustoms->numberOfBills = $row[35];
        $originCustoms->isReturnable = ($row[36] == 'Yes' ? true : false);
        $originCustoms->returnableCategory = $row[37];
        $originCustoms->otherInstructions = $row[38];

        $route->originCustoms = $originCustoms;

        //Import Bill Type	Number of Bills	Trailer Type
        $destinationCustoms = new DestinationCustoms();
        $destinationCustoms->importBillType = $row[39];
        $destinationCustoms->numberOfBills = $row[40];

        $route->destinationCustoms = $destinationCustoms;

        $exportTpt = new ExportTPT();
        $exportTpt->trailerType = $row[41];

        $route->exportTPT = $exportTpt;

        LOG::debug("Processing route [" . $route->originLocation . ' - ' . $route->loadPort . " - " . $route->dischargePort . '-' . $route->destinationLocation . "]");

        return $route;

    }

    /**
     * Method to prepare packageDimensions for Airfreight
     * @param array $row
     * @return PackageDimensions
     */
    private function xl2packageDimensions(array $row)
    {

        $dimension = new PackageDimensions();
        $dimension->length = $row[26];
        $dimension->breadth = $row[27];
        $dimension->height = $row[28];
        $dimension->lbhUnit = $row[29];
        $dimension->noOfPackages = $row[30];
        $dimension->totalCBM = $row[31];
        $dimension->grossWeight = $row[32];
        $dimension->weightUnit = $row[33];
        return $dimension;
    }

    // ********************* Term Starts *********************************************//

    public function term_xls2bo_save(array $master = [], array $details = [], array $sellers = [])
    {

        $bo = new AirFreightTermBuyerPostBO();
        $bo->attributes = new AirFreightBuyerPostAttributes();

        ///----- Process Master Sheet --------
        $bo->buyerId = JWTAuth::parseToken()->getPayload()->get('id');

        $bo->leadType = 'term';
        $bo->isTermAccepted = "true";
        $bo->status = "draft";
        $bo->viewCount = 0;

        $bo->title = $master[1];
        $timestr = $master[2] . '-' . ($master[3] * 100);
        $userTimezone = $master[4];
        $bo->userTimezone = $userTimezone;
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

        $bo->attributes->creditDays = $master[9];
        $bo->attributes->paymentTerms = $master[10];

        $rfpEligibility = new RfpEligibility();
        $rfpEligibility->avgTurnOverLastThreeYears = $master[11];
        $rfpEligibility->incometaxAssement = ($master[12] == 'Yes' ? true : false);
        $rfpEligibility->numberOfYearsInBusiness = $master[13];
        $rfpEligibility->termContractWithOther = ($master[14] == 'Yes' ? true : false);

        $bo->attributes->rfpEligibility = $rfpEligibility;

        $bo->isPublic = $master[15] == 'Yes' ? true : false;
        $bo->serviceId = $master[17];
        ///---- Process Sellers Sheet ---------

        $size = count($sellers);
        $counter = 0;

        LOG::debug("Sellers setup => [" . $size . "]");

        if ($size > 0) {

            foreach ($sellers as $row) {

                LOG::debug("Registering Private Invite to Seller [" . $row[0] . "]");

                $sellerId = UserDetailsService::getUserByEmail($row[0]);

                if (isset($sellerId)) {
                    //This is a global discount. Add it.
                    array_push($bo->visibleToSellers, $sellerId);
                } else {
                    throw new ApplicationException(["sheet" => "Sellers", "row" => $counter], "No Seller found with email " . $row[0]);
                }

            }

        }

        ///----- Process Detail Sheet --------

        $serviceType = null;
        $route = null;
        $packageDimension = null;
        $size = count($details);
        $counter = 0;

        if ($size > 0) {

            foreach ($details as $row) {

                LOG::debug("Processing Detail Row [" . $counter . "]");

                if ($counter == 0) {

                    //Initialize
                    $serviceType = $this->term_xl2serviceType($row);

                    //Prepare route
                    $route = $this->term_xl2route($row, $userTimezone);
                }
                if ($row[1] != $serviceType->originLocation || $row[2] != $serviceType->destinationLocation) {

                    //New service detected. Stow away the old service and start anew

                    array_push($serviceType->routes, $route);
                    array_push($bo->attributes->serviceType, $serviceType);

                    $serviceType = $this->term_xl2serviceType($row);
                    $route = $this->term_xl2route($row, $userTimezone);

                }

                if ($row[3] != $route->loadPort || $row[4] != $route->dischargePort) {

                    //New route detected. Stow away the old route and start anew
                    array_push($serviceType->routes, $route);

                    $route = $this->term_xl2route($row, $userTimezone);
                }


                if ($row[1] != $serviceType->originLocation || $row[2] != $serviceType->destinationLocation) {

                    //New service detected. Stow away the old service and start anew

                    array_push($serviceType->routes, $route);
                    array_push($bo->attributes->serviceType, $serviceType);

                    $serviceType = $this->term_xl2serviceType($row);
                    $route = $this->term_xl2route($row, $userTimezone);

                }

                if ($row[3] != $route->loadPort || $row[4] != $route->dischargePort) {

                    //New route detected. Stow away the old route and start anew
                    array_push($serviceType->routes, $route);

                    $route = $this->term_xl2route($row, $userTimezone);
                }

                LOG::debug(" Packagiung Dimentions =>  %%%%%%%%%%%%%%%%%%%%%%");

                $packageDimension = $this->xl2packageDimensions($row);

                //TODO
                //A faster alternative to array_push() is to simply append values to your array using []
                // array_push($route->packageDimensions, $packageDimensions)
                $route->packageDimensions = $packageDimension;
                $counter = $counter + 1;
            }

            //Finish the process.
            array_push($serviceType->routes, $route);
            array_push($bo->attributes->serviceType, $serviceType);

        }

        return $bo;

    }

    //TODO For Term

    private function term_xl2serviceType(array $row)
    {
        $serviceType = new AirFreightServiceType();
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
        LOG::debug("Processing port pair [" . $serviceType->originLocation . " - " . $serviceType->destinationLocation . "]");
        return $serviceType;
    }

    private function term_xl2route(array $row, $userTimezone)
    {
        $route = new TermRoute();

        $route->serviceSubType = $row[0];;

        $route->originLocation = $row[1];
        $route->destinationLocation = $row[2];

        if ($route->airFreightType == 'Port to Port') {
            $route->serviceSubType = 'P2P';
        } else if ($route->airFreightType == 'Port to Door') {
            $route->serviceSubType = 'P2D';
        } else if ($route->airFreightType == 'Door to Port') {
            $route->serviceSubType = 'D2P';
        } else if ($route->airFreightType == 'Door to Door') {
            $route->serviceSubType = 'D2D';
        }

        //TODO:Following condition may not be needed as we are capturing the Service Subtype from excel.
        //Another reason for introducing is the details sheet in excel for spot and term will be same
        //so that team need not play with numbers where is what with
        /*if(isset($route->originLocation) && isset($route->destinationLocation)) {
            $route->serviceSubType = 'D2D';
        }elseif (isset($route->originLocation) && !isset($route->destinationLocation)){
            $route->serviceSubType = 'D2P';
        }elseif (!isset($route->originLocation) && isset($route->destinationLocation)) {
            $route->serviceSubType = 'P2D';
        }else{
            $route->serviceSubType = 'P2P';
        } */

        $route->loadPort = $row[3];
        $route->dischargePort = $row[4];
        //  $route->incoTerms = $row[5];
        $route->commodity = $row[5];
        $route->commodityDescription = $row[6];
        LOG::info('row[7]  =>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>' . $row[7]);
        $route->cargoReadyDate = DateUtils::parseDate($row[7], $userTimezone);
        $route->priceType = $row[8];
        $route->packagingType = $row[9];
        $route->isStackable = ($row[10] == 'Yes' ? true : false);
        $route->isRadioActive = ($row[11] == 'Yes' ? true : false);

        $route->airFreightType = $row[12];

        if ($route->airFreightType == 'Temperature Controlled') {

            $temperature = new TemperatureAttributes();
            $temperature->isActive = $row[13];
            $temperature->isPassive = $row[14];
            $temperature->temperature = $row[15];
            $temperature->temperatureTo = $row[16];
            $temperature->temperatureUnit = $row[17];

            $route->temperatureAttributes = $temperature;
        }

        $route->isHazardous = ($row[18] == 'Yes' ? true : false);
        if ($route->isHazardous == true) {

            $hazardousAttributes = new HazardousAttributes();

            $hazardousAttributes->imoClass = $row[19];
            $hazardousAttributes->imoSubclass = $row[20];
            $hazardousAttributes->flashpoint = $row[21];
            $hazardousAttributes->flashpointUnit = $row[22];
            $hazardousAttributes->properShippingName = $row[23];
            $hazardousAttributes->technicalName = $row[24];
            $hazardousAttributes->packingGroup = $row[25];

            $route->hazardousAttributes = $hazardousAttributes;
        }

        $originCustoms = new OriginCustoms();
        $originCustoms->shippingBillType = $row[34];
        $originCustoms->numberOfBills = $row[35];
        $originCustoms->isReturnable = ($row[36] == 'Yes' ? true : false);
        $originCustoms->returnableCategory = $row[37];
        $originCustoms->otherInstructions = $row[38];

        $route->originCustoms = $originCustoms;

        //Import Bill Type	Number of Bills	Trailer Type
        $destinationCustoms = new DestinationCustoms();
        $destinationCustoms->importBillType = $row[39];
        $destinationCustoms->numberOfBills = $row[40];

        $route->destinationCustoms = $destinationCustoms;

        $exportTpt = new ExportTPT();
        $exportTpt->trailerType = $row[41];

        $route->exportTPT = $exportTpt;

        LOG::debug("Processing route [" . $route->originLocation . ' - ' . $route->loadPort . " - " . $route->dischargePort . '-' . $route->destinationLocation . "]");
        return $route;
    }




    /*
     * UI /excel attributes
     Title
    Origin
    Destination
    Load Port
    Discharge Port
    Commodity
    commodityDescription
    Cargo Ready Date
    Price Type ? (Firm / Negotiable)
    PackagingType
    Is Stackabkle
    Is Radio Active
    Airfreight Type
    Active
    Passive
    TemperatureFrom
    TemperatureTo
    TemeratureUnit
    Is Hazardous (Yes/No)
    IMO Class
    IMO Subclass
    Flashpoint
    Flashpont UoM
    Proper Shipping Name
    Technical Name
    Packaging Group
    Length
    Breadth
    Height
    lbhUnit
    noOfPackages
    grossWeight
    weightUnit
    Shipping Bill Type
    Number of Bills
    Is Returnable
    Returnable Category
    Other Instructions
    Import Bill Type
    Number of Bills
    Trailer Type


     */
    /********* Term fields ***********
     * Service-SubType
     * Origin
     * Destination
     * Load Port
     * Discharge Port
     * Commodity
     * commodityDescription
     * "Cargo Ready Date
     * Price Type ? (Firm / Negotiable)
     * PackagingType
     * "Is Stackabkle
     * "Is Radio Active
     * Airfreight Type
     * Active
     * Passive
     * TemperatureFrom
     * TemperatureTo
     * TemeratureUnit
     * Is Hazardous (Yes/No)
     * IMO Class
     * IMO Subclass
     * Flashpoint
     * Flashpont UoM
     * Proper Shipping Name
     * Technical Name
     * Packaging Group
     * Length
     * Breadth
     * Height
     * lbhUnit
     * noOfPackages
     * Total CBM
     * grossWeight
     * weightUnit
     * Shipping Bill Type
     * Number of Bills
     * "Is Returnable
     * Returnable Category
     * Other Instructions
     * Import Bill Type
     * Number of Bills
     * Trailer Type
     **********/
}