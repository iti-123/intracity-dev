<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 14:33
 */

namespace Api\Modules\AirFreight;

use Api\BusinessObjects\BuyerPostBO;
use Api\Framework\AbstractBuyerPostValidator;
use Api\Services\LocationService;
use Api\Utils\DateUtils;
use Log;

/**
 * Validator to perform validations specific to AirFrieght
 * Class AirFreightBuyerPostValidator
 * @package Api\Modules\AirFreight
 */
class AirFreightBuyerPostValidator extends AbstractBuyerPostValidator
{

    /**
     * Consists of validation to be applied during saving of AirFreight BuyerPost entity
     * @param BuyerPostBO $bo
     * @return array
     */
    public function validateSave(BuyerPostBO $bo)
    {
        Log::info('validateSave Called => ');

        $errors = [];
        $unixNow = DateUtils::unixNow();
        $basicPostErrors = parent::validateSave($bo);

        Log::info('Common Validations by its Parent is done. ');

        //Add Basic Errors
        $errors = array_merge($errors, $basicPostErrors);

        //No errors found in basic buyer post data. Let us validate attributes.
        $leadType = $bo->leadType;
        $attributes = $bo->attributes;

        if ($leadType == "spot") {
            Log::info('Validating the Spot attributes.');

            $commonSpotAttributeErrors = $this->validateSpotAttributes($attributes, $bo->lastDateTimeOfQuoteSubmission);
            $errors = array_merge($errors, $commonSpotAttributeErrors);
        } else {
            Log::info('Validating the Term attributes.');

            $commonTermAttributeErrors = $this->validateTermAttributes($attributes, $bo->lastDateTimeOfQuoteSubmission);
            $errors = array_merge($errors, $commonTermAttributeErrors);
        }

        LOG::info("Finished Performing Airfreight Buyerpost Validations. Found " . sizeof($errors) . " error(s)");

        LOG::info($errors);

        return $errors;
    }


    /**
     * Method to validate the Spot specific attributes
     * @param AirFreightBuyerPostAttributes $attributes
     * @return array
     */
    public function validateSpotAttributes($attributes, $lastDateOfQuoteSubmission)
    {
        $errors = [];
        //Get each Route  and Validate
        $routes = $attributes->routes;
        LOG::info("Total Number  of Routes Found => " . count($routes));
        //Incase of Spot there will be only one route
        foreach ($routes as $route) {
            if (!empty($route)) {

                array_push($errors, array(000 => "Errors are for the route " . $route->loadPort . ' - ' . $route->dischargePort));

                $commonAttributeErrors = $this->validateCommonAttributes($route);
                array_push($errors, $commonAttributeErrors);

                if ($route->cargoReadyDate < $lastDateOfQuoteSubmission) {
                    array_push($errors, array(110 => "cargoReadyDate must be greater than lastDateTimeOfQuoteSubmission"));
                }

                if (!empty($route->airfreightType)) {
                    array_push($errors, array(111 => "airfreightType not valid"));
                }
                //TODO: Include validations for applicable fields from following List
                /*
                 * airfreightType: General,
                    temperatureAttributes: null,
                    isStackable: true,
                    isRadioActive: false,
                    serviceSubType: P2P,
                    originLocation: null,
                    destinationLocation: null,
                    incoTerms: null,
                    loadPort: Oshawa Airport,
                    dischargePort: Dresden Airport,
                    commodity: Frozenitems,
                    cargoReadyDate: 1494354599,
                    priceType: Firm Price,
                    isFumigationRequired: null,
                    isHazardous: false,
                    hazardousAttributes: null,
                    specialConditions: null,
                    originCustoms: {
                    shippingBillType: null,
                    otherBillType: null,
                    numberOfBills: null,
                    isReturnable: false,
                    returnableCategory: null,
                    otherCategory: null,
                    otherInstructions: null,
                 */
            }
        }
        return $errors;
    }

    /**
     * Method to validate the common attributes between Spot and Term types
     * @param AirFreightBuyerPostAttributes $routes
     * @return array
     */
    private function validateCommonAttributes($route)
    {

        $errors = [];

        if (LocationService::isValidAirPort($route->loadPort) == false) {
            $errors[] = array(105 => "LoadPort => " . $route->loadPort . " is not a valid port");
        }
        if (LocationService::isValidAirPort($route->dischargePort) == false) {
            $errors[] = array(106 => "Discharge Port => " . $route->dischargePort . " is not a valid port");
        }
        if (!empty($route->loadPort) && $route->loadPort == $route->dischargePort) {
            $errors[] = array(114 => "LoadPort and DischargePort should not be same");
        }
        if ($route->serviceSubType == "Door to Door") {
            if (LocationService::isValidLoaction($route->originLocation)) {
                $errors[] = array(107 => "OriginLocation not valid");
            }
            if (LocationService::isValidLoaction($route->destinationLocation)) {
                $errors[] = array(108 => "DestinationLocation not valid");
            }
        } else if ($route->serviceSubType == "Door to Port") {
            if (LocationService::isValidLoaction($route->originLocation)) {
                $errors[] = array(107 => "OriginLocation not valid");
            }
        } else if ($route->serviceSubType == "Port to Door") {
            if (LocationService::isValidLoaction($route->destinationLocation)) {
                $errors[] = array(108 => "DestinationLocation not valid");
            }
        }
        Log::info("SpecialConditionType Check");
        if ($route->isHazardous && $route->specialConditionType == "GOH") {
            $errors[] = array(109 => "Hazardous and Special Condition GOH cannot be used together");
        }

        return $errors;
    }

    /**
     * Method to validate the term specific attributes
     * @param AirFreightBuyerPostAttributes $attributes
     * @return array
     */
    public function validateTermAttributes($attributes, $lastDateOfQuoteSubmission)
    {
        $errors = [];

        $serviceTypes = $attributes->serviceType;
        LOG::info("Total Number  of Service Types Found => " . count($serviceTypes));
        //Incase of Term there will be serviceType array, unders each servcieType there are routes availbale.

        foreach ($serviceTypes as $serviceType) {

            //Get each Route  and Validate
            $routes = $serviceType->routes;
            LOG::info("Total Number  of Routes Found => " . count($routes));

            foreach ($routes as $route) {
                if (!empty($route)) {

                    array_push($errors, array(000 => "Errors are for the route " . $route->loadPort . ' - ' . $route->dischargePort));
                    $commonAttributeErrors = $this->validateCommonAttributes($route);
                    array_push($errors, $commonAttributeErrors);

                    if ($route->cargoReadyDate < $lastDateOfQuoteSubmission) {
                        array_push($errors, array(110 => "cargoReadyDate must be greater than lastDateTimeOfQuoteSubmission"));
                    }

                    if (!empty($route->emdMode)) {
                        array_push($errors, array(110 => "EMD Mode is not valid"));
                    }

                    //TODO: Include RFP Eligibility mandatory fields
                    /*emdAmount
                    emdMode
                    awardCriteria
                    contractAllotment
                    credit
                    creditDays
                    comments
                    rfpEligibility
                    bidTermsAndConditions*/
                }
            }
        }
        return $errors;
    }

}