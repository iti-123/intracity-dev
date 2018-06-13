<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/22/2017
 * Time: 6:16 PM
 */

namespace ApiV2\Modules\LCL;

use ApiV2\BusinessObjects\BuyerPostBO;
use ApiV2\Framework\AbstractBuyerPostValidator;
use ApiV2\Framework\IBuyerPostValidator;
use ApiV2\Services\LocationService;
use Carbon\Carbon;
use Log;

class  LCLBuyerPostValidator extends AbstractBuyerPostValidator implements IBuyerPostValidator
{
    protected $currentDateTime;

    function validateGet()
    {
        $response = 'LCLBuyerPostValidator.validateGet() called';
        LOG::info($response);
        return ((array)$response);
    }

    function validateSave(BuyerPostBO $bo)
    {

        $errors = [];


        $errors = parent::validateSave($bo);
        $leadType = $bo->leadType;
        if (sizeof($errors) > 0) {
            return $errors;
        }

        //No errors found in basic buyer post data. Let us validate LCL specific attributes.

        LOG::info("Performing LCL Buyerpost Validations");

        $attributes = $bo->attributes;

        if ($leadType == "spot") {
            $errors = $this->validateLCLSpotAttributes($attributes);
        } else {
            $errors = $this->validateLCLTermAttributes($attributes);
        }

        LOG::info("Finished Performing LCL Buyerpost Validations. Found " . sizeof($errors) . " error(s)");

        LOG::info($errors);

        return $errors;
    }

    function validateLCLSpotAttributes(LCLBuyerPostAttributes $attributes)
    {


        $errors = [];
        $typeOfService = unserialize(SHIPPING__SERVICE_SUBTYPES);
        $current = Carbon::now();


        foreach ($attributes as $attr) {

            $this->currentDateTime = strtotime($current);

            /****
             * Check if commodity is empty
             */

            if ($attr->commodity == null) {
                $errors[101] = "Commodity cannot be null";
            }
            /***
             * Check id type of service subtype is valid
             */

            if (!in_array($attr->serviceSubType, $typeOfService)) {

                $errors[107] = "Invalid service subtype";
            }

            if (!isset($attr->priceType) || ($attr->priceType == '')) {
                $errors[109] = "Invalid pricetype";
            }
            /****
             * Check the valid port names
             */
            if (!LocationService::autocompletePorts($attr->loadPort)) {
                $errors[] = array(105 => "LoadPort not valid");
            }
            if (!LocationService::autocompletePorts($attr->dischargePort)) {
                $errors[] = array(106 => "DischargePort not valid");
            }
            if (!empty($attr->loadPort) && $attr->loadPort == $attr->dischargePort) {
                $errors[] = array(114 => "LoadPort and DischargePort should not be same");
            }
            if ($attr->serviceSubType == $typeOfService['D2D']) {
                if (!LocationService::autocompleteCities($attr->originLocation)) {
                    $errors[] = array(107 => "OriginLocation not valid");
                }
                if (!LocationService::autocompleteCities($attr->destinationLocation)) {
                    $errors[] = array(108 => "DestinationLocation not valid");
                }
            } else if ($attr->serviceSubType == $typeOfService['D2P']) {
                if (!LocationService::autocompleteCities($attr->originLocation)) {
                    $errors[] = array(107 => "OriginLocation not valid");
                }
            } else if ($attr->serviceSubType == $typeOfService['P2D']) {
                if (!LocationService::autocompleteCities($attr->destinationLocation)) {
                    $errors[] = array(108 => "DestinationLocation not valid");
                }
            }
            /**
             * check if hazardous set and the related validations
             */
            if ($attr->isHazardous == true) {

                if (!isset($attr->hazardousAttributes->imoClass) || ($attr->hazardousAttributes->imoClass == '')) {
                    $errors[201] = "Please fill IMO class";
                }
                if (!isset($attr->hazardousAttributes->imoSubclass) || ($attr->hazardousAttributes->imoSubclass == '')) {
                    $errors[202] = "Please fill IMO subclass";
                }
            }
            /**
             * Check if stackable - yes/no
             */

            if (!isset($attr->isStackable) || ($attr->isStackable == '')) {
                $errors[308] = "Please confirm if stackable";
            }
            /**
             * Mandatory package information
             */

            foreach ($attr->packageDimensions as $pkg) {

                if (!isset($pkg->packagingType) || ($pkg->packagingType == '')) {
                    $errors[303] = "Please enter packaging type";
                }
                if (!isset($pkg->noOfPackages) || ($pkg->noOfPackages == '')) {
                    $errors[304] = "Please enter package count";
                }
                if (!isset($pkg->length) || ($pkg->length <= 0)) {
                    $errors[305] = "Please enter package length";
                }
                if (!isset($pkg->breadth) || ($pkg->breadth <= 0)) {
                    $errors[306] = "Please enter package breadth";
                }
                if (!isset($pkg->height) || ($pkg->height <= 0)) {
                    $errors[307] = "Please enter package height";
                }
            }

            if ((empty($attr->cargoReadyDate)) || ($attr->cargoReadyDate <= strtotime($current))) {
                $errors[107] = "cargoReadyDate cannot be empty or less than current date/time";
            }
        } //End of for-each

        return $errors;

    }

    function validateLCLTermAttributes($attributes)
    {
        $errors = [];
        return $errors;

    }

    function validateDelete()
    {
        LOG::info('LCLBuyerPostValidator.validateDelete Called');
        return 'LCLBuyerPostValidator.validateDelete() called';
    }

    function validateLCLAttributes($routes)
    {
        $errors = [];
        for ($i = 0; $i < sizeof($routes); $i++) {

            Log::info("SpecialConditionType Check");
            /*if(  $routes[$i]->isHazardous && $routes[$i]->specialConditionType == "GOH" ) {
                array_push($errors, new ValidationError(103, "Hazardous and Special Condition GOH cannot be used together"));
                // array_push($errors, "Hazardous and Special Condition GOH cannot be used together");
            }*/
            /* if($routes[$i]->commodity==null){

                array_push($errors, new ValidationError(101, "Commodity cannot be empty"));

            }
           */
        }
        //return $errors;
    }
}