<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/21/17
 * Time: 12:34 PM
 */

namespace ApiV2\Services;

use ApiV2\BusinessObjects\OrderBO;
use ApiV2\BusinessObjects\OrderSearchBO;
use Doctrine\Common\Collections\Collection;

/**
 * The IOrderService
 * @package Api\Services
 */
interface IOrderService
{

    /**
     * Gets an order by ID
     * @param $orderId
     * @return OrderBO
     */
    public function getOrderById($orderId);


    /**
     * Get statistics for a set of orders
     * @param array $orderIds
     * @return Order statistics for a set of orders
     */
    public function getOrderStats($orderIds = []);


    /**
     * Create an order
     * @param $orderBO The orderBO
     * @param $transitionName The name of the transition to apply
     * @return OrderBO
     */
    public function createOrder(OrderBO $orderBO);

    /**
     * Perform an action on the order
     * @param $orderId The orderId
     * @param $transitionName The name of the transition to apply
     * @return OrderBO
     */
    public function performAction(OrderBO $bo, $stateMachine, $updateBo);


    /**
     * Filter orders based on various criteria
     * @param OrderSearchBO $orderFilter
     * @return Collection of Orders
     */
    public function filterOrders(OrderSearchBO $orderFilter);


}