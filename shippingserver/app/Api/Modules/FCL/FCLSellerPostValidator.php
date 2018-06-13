<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 14:33
 */

namespace Api\Modules\FCL;

use Api\BusinessObjects\SellerPostBO;
use Api\Framework\AbstractSellerPostValidator;
use Api\Framework\ISellerPostValidator;
use Api\Services\LocationService;
use App\Exceptions\ValidationBuilder;
use Log;


class FCLSellerPostValidator extends AbstractSellerPostValidator implements ISellerPostValidator
{
    public $rules = [];
    public $custom = [];

    public function createRulesSave()
    {

        $seaPortTableName = "shp_lkp_seaports";
        $seaPortColumnName = "seaport_name";
        $rules = parent::createRulesSave();
        $this->rules = array_merge($rules, [
            'attributes.portPair' => 'required|array',
            'attributes.portPair.*.loadPort' => 'required|exists:' . $seaPortTableName . ',' . $seaPortColumnName,
            'attributes.portPair.*.dischargePort' => 'required|exists:' . $seaPortTableName . ',' . $seaPortColumnName,
            'attributes.portPair.*.carriers.*.transitDays' => 'required|digits_between:1,3',
            'attributes.portPair.*.carriers.*.carrierName' => 'required|max:50',
            'attributes.portPair.*.carriers.*.containers' => 'required|array',
            'attributes.portPair.*.carriers.*.containers.*.containerType' => 'required',
            'attributes.portPair.*.carriers.*.containers.*.freightCharges' => 'required|array',
            'attributes.portPair.*.carriers.*.containers.*.freightCharges.*.amount' => 'required',
            'attributes.portPair.*.carriers.*.containers.*.localCharges' => 'required|array',
            'attributes.portPair.*.carriers.*.containers.*.localCharges.*.amount' => 'required',
        ]);
    }

    public function createCustomSave()
    {
        $custom = parent::createCustomSave();
        $this->custom = array_merge($custom, []);
    }

    function validateGet()
    {
        $response = 'FCLSellerPostValidator.validateGet() called';
        LOG::info($response);
        return ((array)$response);
    }

    function validateSave(SellerPostBO $bo)
    {

        $validationErrors = ValidationBuilder::create();
        //For now  tehse are commented and handling Normal way.  After the release pl. use Laravel way of validations
//        dd($this->validationErrors);
        // $this->createRulesSave();
        //  $this->createCustomSave();

        $validationErrors->errorLaravel(parent::validateSave($bo));

        //No errors found in basic buyer post data. Let us validate FCL specific attributes.
        LOG::info("Performing FCL Sellerpost Validations");
        $attributes = $bo->attributes;

        $this->validateRateCardAttributes($attributes, $validationErrors);
        $validationErrors->raise();
    }

    public function validateRateCardAttributes(FCLSellerPostAttributes $attributes, $validationErrors)
    {

        if ($attributes != null && count($attributes) > 0) {
            $portPairs = $attributes->portPair;
            foreach ($portPairs as $portPair) {

                if ($portPair->loadPort == $portPair->dischargePort) {

                    $validationErrors->error("portPair", "load port and discharge port cannot be the same");
                    //ValidationBuilder::create()->error("portPair", "load port and discharge port cannot be the same")->raise();
                }
                $this->validateRouteAttributes($portPair, $validationErrors);
            }
        } else {
            $validationErrors->error("portPair", "At least one Port Pair must be specified");
            //ValidationBuilder::create()->error("portPair", "At least one Port Pair must be specified");
        }

    }

    /**
     * Method to validate the common attributes between Spot and Term types
     * @param FCLSellerPostAttributes $route
     * @return array
     */
    private function validateRouteAttributes($route, $validationErrors)
    {

        $flag = LocationService::isValidSeaPort($route->loadPort);
        Log::info("Flag =>");
        Log::info($flag);

        if (LocationService::isValidSeaPort($route->loadPort) == 0) {
            //$errors[112] = array ('112','Invalid Load Port '.$route->loadPort);
            $validationErrors->error("Load Port", "Invalid Load Port");

        }
        if (LocationService::isValidSeaPort($route->dischargePort) == 0) {
            // $errors[113] = array ('113','Invalid Discharge Port '.$route->loadPort);

            $validationErrors->error("DischargePort", "Invalid Discharge Port");
        }
        if (!empty($route->loadPort) && $route->loadPort == $route->dischargePort) {
            $validationErrors->error("DischargePort", "Invalid Discharge Port");
        }

        //Seller can give multiple carrier options (up to 3 carriers only).
        $carriers [] = $route->carriers;
        if (!empty($carriers) && count($carriers) > 3) {
            $validationErrors->error("Carrier", "Maximum of three carriers can be specified at each port pair level");
            //$errors[115] = array ('114','Maximum of three carriers can be specified at each port pair level');
        } elseif (count($carriers) > 0) {
            foreach ($route->carriers as $carrier) {
                if (isset($carrier->transitDays)) {
                    $this->validateCarrierAttributes($carrier, $validationErrors);
                }
            }
        }

        //Routing information (via ports) will have maximum of three ports and one port is mandatory field.

        /*  if($route->serviceSubType == "Door to Door"){
              if(LocationService::isValidLoaction($route->originLocation)){
                  $errors[] = array(107=>"Origin Location not valid");
              }
              if(LocationService::isValidLoaction($route->destinationLocation)){
                  $errors[] = array(108=>"Destination Location not valid");
              }
          }else if($route->serviceSubType == "Door to Port"){
              if(LocationService::isValidLoaction($route->originLocation)){
                  $errors[] = array(107=>"Origin Location not valid");
              }
          }else if($route->serviceSubType == "Port to Door"){
              if(LocationService::isValidLoaction($route->destinationLocation)){
                  $errors[] = array(108=>"Destination Location not valid");
              }
          } */

    }

    private function validateCarrierAttributes(SellerCarriers $carrier, $validationErrors)
    {

        //These checks are needed when selelr provides an offer

        //Transit days must be whole number between 1 and 99.
        if (!($carrier->transitDays != null) && ($carrier->transitDays != "")) {
            if ($carrier->transitDays < 1 || $carrier->transitDays > 99)
                // $errors[114] = array ('114','Transit days must be whole number between 1 and 99');
                $validationErrors->error("TransitDays", "Transit days must be whole number between 1 and 99");
        }
        //Cargo ready date is less than CY Cut-off date (valid from Buyer perspective

        //CY Cut-off date should be less than ETD
        /*        if( ($carrier->cyCutOffDate != null)|| ($carrier->cyCutOffDate != "") ) {
                    if (($carrier->etd != null) || ($carrier->etd != "")) {

                        if ($carrier->cyCutOffDate > $carrier->etd) {
                            $errors[115] = array('115', 'CY Cut-off date should be less than ETD');
                        }
                    }
                }
        */

    }

    function validateDelete()
    {
        LOG::info('FCLSellerPostValidator.validateDelete Called');
        return 'FCLSellerPostValidator.validateDelete() called';
    }
}