<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 14:33
 */

namespace Api\Modules\AirFreight;

use Api\BusinessObjects\SellerPostBO;
use Api\Framework\AbstractSellerPostValidator;
use Api\Framework\ISellerPostValidator;
use Log;

class AirFreightSellerPostValidator extends AbstractSellerPostValidator implements ISellerPostValidator
{
    function validateGet()
    {
        $response = 'AirFreightSellerPostValidator.validateGet() called';
        LOG::info($response);
        return ((array)$response);
    }

    function validateSave(SellerPostBO $bo)
    {
        $errors = [];

        $errors = parent::validateSave($bo);
        $leadType = $bo->leadType;
        if (sizeof($errors) > 0) {
            return $errors;
        }

        //No errors found in basic buyer post data. Let us validate AirFreight specific attributes.

        LOG::info("Performing AirFreight Sellerpost Validations");

        $attributes = $bo->attributes;
        if ($leadType == "spot") {
            //   $errors = $this->validateAirFreightSpotAttributes($attributes);
        } else {
            //   $errors = $this->validateAirFreightTermAttributes($attributes);
        }


        LOG::info("Finished Performing AirFreight Sellerpost Validations. Found " . sizeof($errors) . " error(s)");

        LOG::info($errors);

        return $errors;
    }

    function validateDelete()
    {
        LOG::info('AirFreightSellerPostValidator.validateDelete Called');
        return 'AirFreightSellerPostValidator.validateDelete() called';
    }

}