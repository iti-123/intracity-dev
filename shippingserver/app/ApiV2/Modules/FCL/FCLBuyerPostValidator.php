<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 14:33
 */

namespace ApiV2\Modules\FCL;

use ApiV2\BusinessObjects\BuyerPostBO;
use ApiV2\BusinessObjects\ContractBO;
use ApiV2\Framework\AbstractBuyerPostValidator;
use ApiV2\Framework\IBuyerPostValidator;
use ApiV2\Services\LocationService;
use ApiV2\Utils\DateUtils;
use App\Exceptions\ValidationBuilder;
use Log;


class FCLBuyerPostValidator extends AbstractBuyerPostValidator implements IBuyerPostValidator
{
    public function validateGet()
    {
        $response = 'FCLBuyerPostValidator.validateGet() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function validateSave(BuyerPostBO $bo)
    {
        $errors = [];

        $unixNow = DateUtils::unixNow();

        $errors = parent::validateSave($bo);

        $validationBuilder = ValidationBuilder::create();

        if (empty($bo->serviceId)) {
            $validationBuilder->error("serviceId", "serviceId Required");
        }

        if (empty($bo->lastDateTimeOfQuoteSubmission)) {
            $validationBuilder->error("lastDateTimeOfQuoteSubmission", "lastDateTimeOfQuoteSubmission Required");
        } else if ((int)$bo->lastDateTimeOfQuoteSubmission < $unixNow) {
            $validationBuilder->error("lastDateTimeOfQuoteSubmission", "Invalid Last Date/Time for quote, must be greater than Today's date");
        }

        $validationBuilder->raise();

        return;

        if (empty($bo->isTermAccepted)) {
            array_push($errors, array(106 => "Term & Conditions not accepted"));
        }

        //No errors found in basic buyer post data. Let us validate FCL specific attributes.
        $leadType = $bo->leadType;
        $attributes = $bo->attributes;

        if ($leadType == "spot") {
            if ($bo->attributes->route->cargoReadyDate < $bo->lastDateTimeOfQuoteSubmission) {
                array_push($errors, array(105 => "cargoReadyDate must be greater than lastDateTimeOfQuoteSubmission"));
            }
            $errors = array_merge($errors, $this->validateFCLSpotAttributes($attributes));

        } else {
            $errors = $this->validateFCLTermAttributes($attributes);
        }

        /*if(!$bo->isPublic){
            foreach ($bo->visibleToSellers as $sellerId){
                $errors = array_merge($errors, $this->sellerServiceSubscription($sellerId));
            }
        }*/

    }

    public function validateFCLSpotAttributes(FCLBuyerPostAttributes $attributes)
    {

        $errors = [];
        $route = $attributes->route;
        /*if(sizeof($route)>0){
            $errors[] = array(111=>"At least one Port Pair required.");
        }*/
        $errors = array_merge($errors, $this->validateFCLAttributes($route));
        return $errors;
    }

    public function validateFCLAttributes($routes)
    {

        $errors = [];
        LOG::info('$routes->loadPort => ' . $routes->loadPort);
        LOG::info('$routes->dischargePort => ' . $routes->dischargePort);

        if (LocationService::isValidSeaPort($routes->loadPort) == 0) {
            $errors[105] = "LoadPort not valid";
        }
        if (LocationService::isValidSeaPort($routes->dischargePort) == 0) {
            $errors[106] = "DischargePort not valid";
        }
        if (!empty($routes->loadPort) && $routes->loadPort == $routes->dischargePort) {
            $errors[114] = "LoadPort and DischargePort should not be same";
        }
        if ($routes->serviceSubType == "Door to Door") {
            if (!LocationService::isValidLoaction($routes->originLocation)) {
                $errors[107] = "OriginLocation not valid";
            }
            if (!LocationService::isValidLoaction($routes->destinationLocation)) {
                $errors[108] = "DestinationLocation not valid";
            }
        } else if ($routes->serviceSubType == "Door to Port") {
            if (!LocationService::isValidLoaction($routes->originLocation)) {
                $errors[107] = "OriginLocation not valid";
            }
        } else if ($routes->serviceSubType == "Port to Door") {
            if (!LocationService::isValidLoaction($routes->destinationLocation)) {
                $errors[108] = "DestinationLocation not valid";
            }
        }
        Log::info("SpecialConditionType Check");
        if ($routes->isHazardous && $routes->specialConditionType == "GOH") {
            $errors[109] = "Hazardous and Special Condition GOH cannot be used together";
        }

        return $errors;
    }

    public function validateFCLTermAttributes($attributes)
    {
        $errors = [];
        return $errors;

    }

    public function validateContractSave(ContractBO $bo)
    {
        $errors = [];
        $unixNow = DateUtils::unixNow();
        $errors = parent::validateContractSave($bo);


        LOG::info("Finished Performing FCL BuyerContract Validations. Found " . sizeof($errors) . " error(s)");

        LOG::info($errors);

        return $errors;

    }

    public function validateDelete()
    {
        LOG::info('FCLBuyerPostValidator.validateDelete Called');
        return 'FCLBuyerPostValidator.validateDelete() called';
    }


}