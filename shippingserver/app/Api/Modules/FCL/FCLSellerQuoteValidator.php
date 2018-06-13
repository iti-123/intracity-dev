<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 10:47 AM
 */

namespace Api\Modules\FCL;

use Api\BusinessObjects\SellerQuoteBO;
use Api\Framework\AbstractSellerQuoteValidator;
use Api\Framework\ISellerQuoteValidator;
use Api\Services\LocationService;
use Api\Utils\DateUtils;
use Log;

class FCLSellerQuoteValidator extends AbstractSellerQuoteValidator implements ISellerQuoteValidator
{
    function validateGet()
    {
        $response = 'FCLSellerQuoteValidator.validateGet() called';
        LOG::info($response);
        return ((array)$response);
    }

    function validateSave(SellerQuoteBO $bo, $offerType = '')
    {

        $errors = [];
        $unixNow = DateUtils::unixNow();
        $errors = parent::validateSave($bo);

        if (!LocationService::autocompletePorts($bo->loadPort)) {
            $errors[] = array(105 => "LoadPort not valid");
        }
        if (!LocationService::autocompletePorts($bo->dischargePort)) {
            $errors[] = array(106 => "DischargePort not valid");
        }
        $carrierCnt = 0;
        $status = $bo->status;
        if ($offerType != 'term') {
            foreach ($bo->attributes->carriers as $carrier) {
                if ($carrier->isRoutingTypeVia()) {
                    $routingVia = [];
                    foreach ($carrier->routingVia as $value) {
                        if (!empty($value))
                            $routingVia[] = $value;
                    }
                    if (sizeof($routingVia) == 0) {

                        $errors[] = array(107 => $carrier->carrierName . " - At least one route required");
                    }
                } else if (empty($carrier->routingType)) {
                    $errors[] = array(107 => "Routing type required");
                }

                $transitDays = $carrier->transitDays;

                if (!is_numeric($transitDays)) {
                    $errors[] = array(110 => $carrier->carrierName . " - Carrier transitDays must be an integer");
                } else if (is_numeric($transitDays)) {
                    if ($transitDays < 1 || $transitDays > 99) {
                        $errors[] = array(109 => $carrier->carrierName . " - Carrier transitDays must be with in 1 - 99");
                    }
                }
                $carrierCnt++;

                switch ($status) {
                    case 'Initial Offer':
                    case 'initial_offer':
                        $errors = array_merge($errors, $this->checkFreightAmount($carrier, "initialOffer"));
                        break;
                    case 'Counter Offer':
                    case 'counter_offer':
                        $errors = array_merge($errors, $this->checkFreightAmount($carrier, "counterOffer"));
                        break;
                    case 'Final Offer':
                    case 'Firm Offer':
                    case 'L1 Offer':
                    case 'final_offer':
                    case 'firm_offer':
                    case 'l1_offer':
                        $errors = array_merge($errors, $this->checkFreightAmount($carrier, "finalOffer"));
                        break;
                }
            }
        }

        Log::info((int)$carrierCnt);
        if ((int)$carrierCnt > 3) {
            $errors[] = array(108 => "Only 3 carriers alowed");
        }


        if (sizeof($errors) > 0) {
            return $errors;
        }
        //No errors found in basic buyer post data. Let us validate FCL specific attributes.
        LOG::info("Finished Performing FCL SellerQuote Validations. Found " . sizeof($errors) . " error(s)");

        LOG::info($errors);

        return $errors;
    }

    public function checkFreightAmount($carrier, $offerType)
    {

        $errors = [];
        $containers = $carrier->containers;


        foreach ($containers as $container) {

            $offers = $container->$offerType;
            $charges = [];

            if (is_array($offers->freightCharges)) {
                foreach ($offers->freightCharges as $value) {
                    if (!empty(trim($value->amount)))
                        $charges[] = $value->amount;
                    if (sizeof($charges) == 0) {
                        $errors[] = array(111 => $carrier->carrierName . " - At least one freightCharge required");
                    }
                }
                foreach ($offers->localCharges as $value) {
                    if (!empty(trim($value->amount)))
                        $charges[] = $value->amount;
                    if (sizeof($charges) == 0) {
                        $errors[] = array(112 => $carrier->carrierName . " - At least one localCharges required");
                    }
                }
            } else {
                if (!empty(trim($offers->freightCharges->amount)))
                    $charges[] = $offers->freightCharges->amount;
                if (sizeof($charges) == 0) {
                    $errors[] = array(113 => $carrier->carrierName . " - At least one freightCharge required");
                }
            }

        }

        return $errors;
    }

    function validateDelete()
    {
        LOG::info('FCLSellerQuoteValidator.validateDelete Called');
        return 'FCLSellerQuoteValidator.validateDelete() called';
    }


}