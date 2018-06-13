<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 2/23/2017
 * Time: 12:39 PM
 */

namespace ApiV2\Controllers;

use Illuminate\Http\Request;

interface IOrderController
{
    public function addOrder(Request $request);

    public function getOrders($id, $serviceId);

    public function getStats($id, $status);

    public function updateOrderStatus($orderId, $transition);
}