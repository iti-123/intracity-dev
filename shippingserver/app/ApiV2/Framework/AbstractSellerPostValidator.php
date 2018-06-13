<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/8/17
 * Time: 7:23 PM
 */

namespace ApiV2\Framework;


use ApiV2\BusinessObjects\SellerPostBO;
use App\Exceptions\ValidationBuilder;
use Log;


class AbstractSellerPostValidator implements ISellerPostValidator
{

    public function createRulesSave()
    {

        $subServiceTypes = "Port to Port,Port to Door,Door to Port,Door to Door";
        return [
            //'serviceSubType' => 'required|in:'.$subServiceTypes,
            'title' => 'required|max:100',
            'validFrom' => 'required|valid_from_check',
            'validTo' => 'required'
        ];
    }

    public function createCustomSave()
    {
        return [];
    }

    function validateGet()
    {
    }

    function validateSave(SellerPostBO $bo)
    {

        LOG::info("Performing basic Sellerpost Validations");

        $errors = ShpValidator::validate($bo, $this->rules, $this->custom);

        if (count($errors) > 0) {
            LOG::debug((array)$errors);

            return $errors;
            ValidationBuilder::create()->errorLaravel($errors)->raise();

        }
        return;

    }

    function validateDelete()
    {

    }


}