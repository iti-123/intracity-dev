<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/22/2017
 * Time: 6:16 PM
 */

namespace ApiV2\Modules\LCL;

use ApiV2\BusinessObjects\BuyerPostBO;
use ApiV2\Framework\IBuyerPostTransformer;
use ApiV2\Framework\SerializerServiceFactory;
use ApiV2\Model\SelectedSellers;
use ApiV2\Services\UserDetailsService;
use ApiV2\Utils\DateUtils;
use App\Exceptions\ApplicationException;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class LCLBuyerPostTransformer implements IBuyerPostTransformer
{
    public function ui2bo_save($payload, $leadType)
    {
        //Convert the request JSON into a BO

        // LOG::info("LCLBuyerPostTransformer ui2bo_save => " . $payload);

        $serializer = SerializerServiceFactory::create();
        if ($leadType == "spot") {
            $post = $serializer->deserialize($payload, 'array<Api\Modules\LCL\LCLSpotBuyerPostBO>', 'json');
            //$post = json_decode($payload);
        } else {
            $post = $serializer->deserialize($payload, 'Api\Modules\LCL\LCLTermBuyerPostBO', 'json');
        }

        return $post;

    }

    public function ui2bo_filter($payload)
    {
        //Convert the request JSON into a BO
        $serializer = SerializerServiceFactory::create();
        $bo = $serializer->deserialize($payload, 'Api\Modules\LCL\LCLBuyerPostSearchBO', 'json');
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


        foreach ($sellectedSeller as $val) {
            $visibleToSellers[] = $val->seller_id;
        }
        if (empty($visibleToSellers))
            return [];
        return $visibleToSellers;
    }

    public function model2boGetAll($models)
    {

        //TODO: Implement model2boGet() method.
        $visibleToSellers = $model = array();
        for ($i = 0; $i < sizeof($models); $i++) {
            $model[$i]['postId'] = $models[$i]['id'];
            $model[$i]['attributes'] = json_decode($models[$i]["attributes"]);
            $visibleToSellers = $this->getVisibleToSellersSellerId($models[$i]);
            $model[$i]['visibleToSellers'] = $visibleToSellers;
            unset($model[$i]["id"]);
        }
        return $model;
    }

    public function model2boSave($model)
    {
        $response = 'LCLBuyerPostTransformer.model2boSave() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function bo2modelDelete($bo)
    {
        $response = 'LCLBuyerPostTransformer.bo2modelDelete() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function model2boDelete($model)
    {
        $response = 'LCLSellerPostTransformer.model2boDelete() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function bo2modelGet($bo)
    {

        //$mapper = new JsonMapper();
        //$contactObject = $mapper->map($bo, new SellerPostBO());

        $response = 'LCLBuyerPostTransformer.bo2modelGet() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function term_xls2bo_save(array $master = [], array $details = [], array $sellers = [])
    {

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


                //  $container = $this->spot_xl2container($row);
                // array_push($route->containers, $container);

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

        $bo = new LCLSpotBuyerPostBO();

        $bo->attributes = new LCLBuyerPostAttributes();

        ///----- Process Master Sheet --------

        $bo->buyerId = JWTAuth::parseToken()->getPayload()->get('id');
        $bo->serviceId = 23; //TODO donot hard code service-id

        $bo->leadType = 'spot';
        $bo->isTermAccepted = "true";
        $bo->status = "open";
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
        $route->isStackable = ($row[11] == 'Yes' ? true : false);
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

        $packageDimensions = new packageDimensions();

        $packageDimensions->noOfPackages = $row[20];
        $packageDimensions->length = $row[21];
        $packageDimensions->breadth = $row[22];
        $packageDimensions->height = $row[23];
        $packageDimensions->weightUnit = $row[24];
        $packageDimensions->grossWeight = $row[25];
        $packageDimensions->packagingType = $row[26];

        $route->packageDimensions = $packageDimensions;
        //$packageDimensions->packagingMethod = $row[26];

        /*$specialConditions = $row[20];
        $route->specialConditionType = $row[20];
        $route->specialConditions = new SpecialCondition();

        if($specialConditions == 'temperature') {

            $temperature = new TemperatureAttributes();
            $temperature->consignmentType = $row[21];
            $temperature->temperatureUnit = $row[22];
            $temperature->temperature = $row[23];

            $route->specialConditions->temperatureAttributes = $temperature;

        }elseif ($specialConditions  == 'odc') {

            $odc = new ODC();

            $odc->dimensionUnit = $row[24];
            $odc->length = $row[25];
            $odc->breadth = $row[26];
            $odc->height = $row[27];
            $odc->weightUnit = $row[28];
            $odc->grossWeight = $row[29];
            $odc->packagingMethod = $row[30];

            $route->specialConditions->ODC = $odc;

        }elseif ($specialConditions == 'goh') {

            $goh = new GOH();

            $goh->numberOfBars = $row[31];
            $goh->numberOfRopes = $row[32];
            $goh->knotsPerRope = $row[33];

            $route->specialConditions->GOH = $goh;

        }elseif ($specialConditions == 'tanktainer') {

            $tanktainer = new TankTainer();

            $tanktainer->commodity = $row[34];
            $tanktainer->weightUnit = $row[35];
            $tanktainer->grossWeight = $row[36];

            $route->specialConditions->tankTainers = $tanktainer;
        }*/

        //Shipping Bill Type	Number of Bills	Is Returnable  (Yes/No)	Returnable  Category	Other Instructions

        $originCustoms = new OriginCustoms();
        $originCustoms->shippingBillType = $row[27];
        $originCustoms->numberOfBills = $row[28];
        $originCustoms->isReturnable = ($row[29] == 'Yes' ? true : false);
        $originCustoms->returnableCategory = $row[30];
        $originCustoms->otherInstructions = $row[31];

        $route->originCustoms = $originCustoms;

        //Import Bill Type	Number of Bills	Trailer Type
        $destinationCustoms = new DestinationCustoms();
        $destinationCustoms->importBillType = $row[32];
        $destinationCustoms->numberOfBills = $row[33];

        $route->destinationCustoms = $destinationCustoms;

        $exportTpt = new ExportTPT();
        $exportTpt->trailerType = $row[34];

        $route->exportTPT = $exportTpt;

        LOG::debug("Processing route [" . $route->originLocation . ' - ' . $route->loadPort . " - " . $route->dischargePort . '-' . $route->destinationLocation . "]");

        return $route;

    }

    private function bo2model(BuyerPostBO $bo, $model)
    {
        LOG::info('bo2model Start');
        $now = date('Y-m-d H:i:s');
        if (!empty($bo->postId)) {
            $model->updatedBy = JWTAuth::parseToken()->getPayload()->get('id');
            $model->updatedIP = $_SERVER['REMOTE_ADDR'];
            $model->updated_at = $now;
            $model->version = (int)$model->version + 1;
        } else {
            $model->createdBy = JWTAuth::parseToken()->getPayload()->get('id');//SecurityPrincipal::getUserId();
            $model->updatedBy = $_SERVER['REMOTE_ADDR'];
            $model->created_at = $now;
            $model->version = 1;
        }
        $model->status = $bo->status;
        $model->buyerId = $bo->buyerId;
        $model->title = $bo->title;
        $model->serviceId = $bo->serviceId;
        //$model->serviceSubType= $bo->serviceSubType;
        $model->leadType = $bo->leadType;
        $model->lastDateTimeOfQuoteSubmission = $bo->lastDateTimeOfQuoteSubmission;
        $model->isPublic = $bo->isPublic;
        $model->isTermAccepted = $bo->isTermAccepted;
        //$model->originLocation= $bo->originLocation;
        //$model->destinationLocation= $bo->destinationLocation;
        $model->syncSearch = false;
        $model->syncLeads = false;
        $model->attributes = json_encode((array)$bo->attributes);
        return $model;
    }


}

