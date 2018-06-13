<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 14:33
 */

namespace ApiV2\Modules\LCL;

use ApiV2\BusinessObjects\SellerPostBO;
use ApiV2\Framework\AbstractSellerPostValidator;
use ApiV2\Framework\ISellerPostValidator;
use ApiV2\Utils\DateUtils;
use Log;

class LCLSellerPostValidator extends AbstractSellerPostValidator implements ISellerPostValidator
{
    function validateGet()
    {
        $response = 'LCLSellerPostValidator.validateGet() called';
        // LOG::info($response);
        return ((array)$response);
    }

    function validateSave(SellerPostBO $bo)
    {

        $errors = [];
        $unixNow = DateUtils::unixNow();
        $errors = parent::validateSave($bo);

        if (sizeof($errors) > 0) {
            return $errors;
        }

        //No errors found in basic buyer post data. Let us validate LCL specific attributes.

        LOG::info("Performing LCL Sellerpost Validations");

        if (empty($bo->serviceId)) {
            array_push($errors, array(102 => "serviceId Required"));
        }

        if (empty($bo->validFrom)) {
            array_push($errors, array(103 => "validFrom Required"));
        } else if ((int)$bo->validFrom < $unixNow) {
            array_push($errors, array(104 => "Invalid valid From for Rate Card, must be greater than Today's date"));
        }
        if (empty($bo->validTo)) {
            array_push($errors, array(103 => "validFrom Required"));
        } else if ((int)$bo->validTo < $unixNow) {
            array_push($errors, array(104 => "Invalid valid To for Rate Card, must be greater than Today's date"));
        }

        if (empty($bo->isTermAccepted)) {
            array_push($errors, array(106 => "Term & Conditions not accepted"));
        }

        $attributes = $bo->attributes;

        LOG::info("Finished Performing LCL Sellerpost Validations. Found " . sizeof($errors) . " error(s)");

        LOG::info($errors);

        return $errors;
    }

    function validateDelete()
    {
        LOG::info('LCLSellerPostValidator.validateDelete Called');
        return 'LCLSellerPostValidator.validateDelete() called';
    }

}