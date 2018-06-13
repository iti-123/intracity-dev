<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 23/2/17
 * Time: 11:48 PM
 */

namespace ApiV2\Modules\RoRo;

use ApiV2\BusinessObjects\BuyerPostBO;
use ApiV2\Framework\AbstractBuyerPostValidator;
use ApiV2\Framework\IBuyerPostValidator;
use Log;

class RoRoBuyerPostValidator extends AbstractBuyerPostValidator implements IBuyerPostValidator
{
    function validateGet()
    {
        $response = 'RoRoBuyerPostValidator.validateGet() called';
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

        //No errors found in basic buyer post data. Let us validate RoRo specific attributes.

        LOG::info("Performing RoRo Buyerpost Validations");

        $attributes = $bo->attributes;
        if ($leadType == "spot") {
            $errors = $this->validateRoRoSpotAttributes($attributes);
        } else {
            $errors = $this->validateRoRoTermAttributes($attributes);
        }


        LOG::info("Finished Performing RoRo Buyerpost Validations. Found " . sizeof($errors) . " error(s)");

        LOG::info($errors);

        return $errors;
    }

    function validateRoRoSpotAttributes(RoRoBuyerPostAttributes $attributes)
    {

        $errors = [];
        $routes = $attributes->routes;
        $errors = $this->validateRoRoAttributes($routes);
        return $errors;
    }

    function validateRoRoAttributes($routes)
    {

        $errors = [];
        return $errors;
    }

    function validateRoRoTermAttributes($attributes)
    {
        $errors = [];
        return $errors;

    }

    function validateDelete()
    {
        LOG::info('RoRoBuyerPostValidator.validateDelete Called');
        return 'RoRoBuyerPostValidator.validateDelete() called';
    }


}