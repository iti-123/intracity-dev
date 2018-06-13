<?php

namespace ApiV2\Modules\FCL;

use ApiV2\BusinessObjects\CartItemsBO;
use ApiV2\Framework\AbstractCartItemValidator;
use App\Exceptions\ApplicationException;

class FCLCartItemValidator extends AbstractCartItemValidator
{
    public $rules = [];
    public $custom = [];

    function validateSaveInit(CartItemsBO $bo)
    {

        $this->createRulesInit();
        $this->createCustomInit();
        parent::validateSaveInit($bo);

    }

    public function createRulesInit()
    {
        $rules = parent::createRulesInit();
        $this->rules = array_merge(
            $rules,
            [
                'attributes.containers.*.containerType' => 'required',
                'attributes.containers.*.quantity' => 'required',
                'attributes.containers.*.weightUnit' => 'required',
                'attributes.containers.*.grossWeight' => 'required',
            ]
        );
    }

    public function createCustomInit()
    {
        $custom = parent::createCustomInit();
        $this->custom = array_merge(
            $custom,
            []
        );
    }

    function validateSave(CartItemsBO $bo)
    {

        parent::validateSave($bo);
        $errors = [];
        if (count($errors)) {
            throw new ApplicationException([
                "buyerId" => $bo->buyerPostId,
                "sellerId" => $bo->buyerPostId
            ], $errors);
        }
    }

}