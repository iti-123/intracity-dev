<?php
/**
 * Created by PhpStorm.
 * User: sainath
 * Date: 4/12/17
 * Time: 11:32 AM
 */

namespace Api\Framework;

use Api\BusinessObjects\CartItemsBO;
use App\Exceptions\ApplicationException;
use Log;

class AbstractCartItemValidator implements ICartItemValidator
{

    public function createRulesInit()
    {
        return [
            'serviceId' => 'required',
            'serviceType' => 'required',
            'sellerId' => 'required',
            'buyerId' => 'required',
            'sellerName' => 'required',
            'buyerName' => 'required',
            'title' => 'required',
            'commodityType' => 'required',
            'loadPort' => 'required',
            'dischargePort' => 'required',
            'leadType' => 'required',
            'cargoReadyDate' => 'required'
        ];
    }

    public function createCustomInit()
    {
        return [];
    }

    public function validateSaveInit(CartItemsBO $bo)
    {
        $errors = ShpValidator::validate($bo, $this->rules, $this->custom);
        if (count($errors) > 0) {
            throw new ApplicationException([], $errors);
        }
    }

    public function validateSave(CartItemsBO $bo)
    {

        $errors = [];
        if ($bo->isGsaAccepted == 0) {
            $errors['isGsaAccepted'] = 'GSA not accepted';
        }
        if (!$bo->consignorName) {
            $errors['consignorName'] = 'Consignor Name is not provided';
        }
        if (!$bo->consignorEmail) {
            $errors['consignorEmail'] = 'Consignor Email is not provided';
        } else if (!(filter_var($bo->consignorEmail, FILTER_VALIDATE_EMAIL) !== false)) {
            $errors['consignorEmail'] = 'Consignor Email Id is not Valid';
        }

        if (!$bo->consignorMobile) {
            $errors['consignorMobile'] = 'Consignor Mobile No is not provided';
        }
        if (!$bo->consignorAddress1) {
            $errors['consignorAddress1'] = 'Consignor Address 1 is not provided';
        }
        /*
        if(!$bo->consignorAddress2) {
            $errors[] = 'Consignor Name is not provided';
        }
        if(!$bo->consignorAddress3) {
            $errors[] = 'Consignor Name is not provided';
        }
        */
        if (!$bo->consignorPincode) {
            $errors['consignorPincode'] = 'Consignor Pin Code is not provided';
        } else if (!ctype_digit($bo->consignorPincode) && count($bo->consignorPincode) != 6) {
            $errors['consignorPincode'] = 'Consignor Pin Code is not valid';
        }
        /*
        if(!$bo->consignorCity) {
            $errors[] = 'Consignor Name is not provided';
        }
        if(!$bo->consignorState) {
            $errors[] = 'Consignor Name is not provided';
        }
        */
        if (!$bo->consignorCountry) {
            $errors['consignorCountry'] = 'Consignor Country is not provided';
        }
        if (!$bo->consigneeName) {
            $errors['consigneeName'] = 'Consignee Name is not provided';
        }
        if (!$bo->consigneeEmail) {
            $errors['consigneeEmail'] = 'Consignee Email is not provided';
        } else if (!(filter_var($bo->consigneeEmail, FILTER_VALIDATE_EMAIL) !== false)) {
            $errors['consigneeEmail'] = 'Consignee Email Id is not Valid';
        }

        if (!$bo->consigneeMobile) {
            $errors['consigneeMobile'] = 'Consignee Mobile No is not provided';
        }
        if (!$bo->consigneeAddress1) {
            $errors['consigneeAddress1'] = 'Consignee Address 1 is not provided';
        }
        /*
        if(!$bo->consigneeAddress2) {
            $errors[] = 'Consignee Name is not provided';
        }
        if(!$bo->consigneeAddress3) {
            $errors[] = 'Consignee Name is not provided';
        }
        */
        if (!$bo->consigneePincode) {
            $errors['consigneePincode'] = 'Consignee Pin Code is not provided';
        } else if (!ctype_digit($bo->consigneePincode) && count($bo->consigneePincode) != 6) {
            $errors['consigneePincode'] = 'Consignee Pin Code is not valid';
        }
        /*
        if(!$bo->consigneeCity) {
            $errors[] = 'Consignee Name is not provided';
        }
        if(!$bo->consigneeState) {
            $errors[] = 'Consignee Name is not provided';
        }
        */
        if (!$bo->consigneeCountry) {
            $errors['consigneeCountry'] = 'Consignee Country is not provided';
        }

        LOG::info("Performing FCL CartItem Validations");
        LOG::info("Finished Performing FCL CartIteam Validations. Found " . sizeof($errors) . " error(s)");
        LOG::info($errors);
        if (count($errors)) {
            throw new ApplicationException([
                "buyerId" => $bo->buyerPostId,
                "sellerId" => $bo->buyerPostId
            ], $errors);
        }
    }

}