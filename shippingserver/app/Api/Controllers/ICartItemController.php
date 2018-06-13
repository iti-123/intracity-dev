<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 2/16/2017
 * Time: 1:34 PM
 */

namespace Api\Controllers;

use Illuminate\Http\Request;

interface ICartItemController
{
    public function getCartItems();

    public function updateCartDetails(Request $request, $cartId);

    public function deleteCartById($cartId);

    public function emptyCart();

}