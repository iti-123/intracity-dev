<?php

namespace Api\Framework;

use Api\BusinessObjects\CartItemsBO;

interface ICartItemValidator
{

    public function validateSaveInit(CartItemsBO $bo);

    public function validateSave(CartItemsBO $bo);

}