<?php

namespace ApiV2\Framework;

use ApiV2\BusinessObjects\CartItemsBO;

interface ICartItemValidator
{

    public function validateSaveInit(CartItemsBO $bo);

    public function validateSave(CartItemsBO $bo);

}