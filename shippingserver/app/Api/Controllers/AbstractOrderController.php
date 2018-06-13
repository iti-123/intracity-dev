<?php
/**
 * Created by PhpStorm.
 * User: sainath
 * Date: 2/20/17
 * Time: 3:19 PM
 */

namespace Api\Controllers;

use Api\Requests\BaseShippingResponse as ShipRes;
use Api\Services\GenerateFiles\GeneratePDF;
use Api\Services\ShpUploadFiles;
use App\Exceptions\ApplicationException;
use Illuminate\Http\Request;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class AbstractOrderController extends BaseController
{
    public $postService;

    public function add(Request $request)
    {

        try {

            $this->postService = $this->serviceFactory->makeService();

            $payload = $request->getContent();
            $parsedJson = json_decode($payload);
            $buyerId = JWTAuth::parseToken()->getPayload()->get('id');
            $paymentType = $parsedJson->paymentType;
            $cartItems = $this->postService->cartItemService->getCartItems($buyerId);
            if (!count($cartItems['cartItems'])) {
                throw new ApplicationException([], ["No cart items to create order"]);
            }

            $amountToPay = $cartItems['totalCharges']->amountToPay;
            $orders = [];
            foreach ($cartItems['cartItems'] as $cartItem) {
                $orders[] = $this->postService->cartItemService->bo2model($cartItem)->toArray();
            }
            $resultSet = $this->postService->createOrders($paymentType, $buyerId, $amountToPay, $orders);
            return ShipRes::ok($resultSet);

        } catch (\Exception $e) {

            LOG::error("Exception while saving Order details", (array)$e->getMessage());
            return $this->errorResponse($e);

        }
    }

    public function validateOrders($orders)
    {
        $sellerId = 0;
        $buyerId = 0;
        $serviceId = 0;
        foreach ($orders as $order) {
            if ($sellerId != $order->sellerId) {
                if ($sellerId != 0) {
                    $errors = ["Seller ids are not identical"];
                    throw new ApplicationException([], $errors);
                }
                $sellerId = $order->sellerId;
            }
            if ($buyerId != $order->buyerId) {
                if ($buyerId != 0) {
                    $errors = ["Buyers ids are not identical"];
                    throw new ApplicationException([], $errors);
                }
                $buyerId = $order->buyerId;
            }
            if ($serviceId != $order->serviceId) {
                if ($serviceId != 0) {
                    $errors = ["Service ids are not identical"];
                    throw new ApplicationException([], $errors);
                }
                $serviceId = $order->serviceId;
            }
        }
        return True;
    }

    public function get($id)
    {

        try {

            Log::info("User is trying to get order details");
            Log::info("Id: " . $id);

            $this->postService = $this->serviceFactory->makeService();
            $bo = $this->postService->getOrderById($id);
            $this->stateMachine = $this->serviceFactory->makeStateMachine($bo);
            $resultSet = $this->postService->updateBo($this->stateMachine, $bo);
            return ShipRes::ok($resultSet);

        } catch (\Exception $e) {

            LOG::error("Exception while getting order details by id", (array)$e->getMessage());
            return $this->errorResponse($e);

        }
    }

    public function updateStatus(Request $request, $id)
    {

        try {

            $payload = $request->getContent();
            $this->postService = $this->serviceFactory->makeService();
            $bo = $this->postService->getOrderById($id);
            $this->stateMachine = $this->serviceFactory->makeStateMachine($bo);

            $updateBo = $this->serviceFactory->makeTransformer()->ui2bo_save_updatebo($payload);

            $bo = $this->postService->performAction($bo, $this->stateMachine, $updateBo);
            $resultSet = $this->postService->updateBo($this->stateMachine, $bo);
            return ShipRes::ok($resultSet);

        } catch (\Exception $e) {

            LOG::error("Exception while updating status", (array)$e->getMessage());
            return $this->errorResponse($e);

        }
    }

    public function getOrders($userType, Request $request)
    {

        try {

            $userId = JWTAuth::parseToken()->getPayload()->get('id');

            Log::info("User is trying to get order details");
            Log::info("User Id: " . $userId);

            $this->postService = $this->serviceFactory->makeService();
            $resultSet = [];
            if ($userType == 'seller' || $userType == 'buyer') {
                $resultSet = $this->postService->getOrdersByUserId($userType, $userId, $request);
            }
            return ShipRes::ok($resultSet);

        } catch (\Exception $e) {

            LOG::error("Exception while getting error", (array)$e->getMessage());
            return $this->errorResponse($e);

        }
    }

    /*
     * This function is written to test order master filters and will be removed
     */
    public function getOrdersTest($userType, Request $request)
    {

        try {

            $userId = JWTAuth::parseToken()->getPayload()->get('id');

            Log::info("User is trying to get order details");
            Log::info("User Id: " . $userId);

            $this->postService = $this->serviceFactory->makeService();
            $resultSet = [];
            if ($userType == 'seller' || $userType == 'buyer') {
                $resultSet = $this->postService->getOrdersByUserIdTest($userType, $userId, $request);
            }
            return ShipRes::ok($resultSet);

        } catch (\Exception $e) {

            LOG::error("Exception while get orders test", (array)$e->getMessage());
            return $this->errorResponse($e);

        }
    }

    public function paymentSuccess(Request $request, $paymentType)
    {

        try {

            $this->postService = $this->serviceFactory->makeService();
            if ($request->get('DR')) {
                $transactionDetails = rawurldecode($request->get('DR'));
                $bo = $this->postService->saveTransaction($paymentType, $transactionDetails);
                $redirectUrl = CLIENT_REDIRECT . "/" . $this->serviceUiName . "/" . $bo->orderId;
                return redirect($redirectUrl);
            } else {
                $error = ['Problem with adding payment'];
                return $error;
            }

        } catch (\Exception $e) {

            LOG::error("Exception while payment success", (array)$e->getMessage());
            return $this->errorResponse($e);

        }

    }

    public function getOrdersPosts($postId)
    {

        try {

            Log::info("User is trying to get order details based on Post");
            Log::info("Post Id: " . $postId);

            $this->postService = $this->serviceFactory->makeService();
            $resultSet = $this->postService->getOrdersByPosts($postId);
            $boArray = [];
            foreach ($resultSet as $eachOrder) {
                $bo = $this->postService->modelToBo($eachOrder);
                $this->stateMachine = $this->serviceFactory->makeStateMachine($bo);
                $bo = $this->postService->updateBo($this->stateMachine, $bo);
                $boArray[] = $bo;
            }
            $finalResultSet = $this->postService->bosToSellerPostsOrderBo($boArray);
            return ShipRes::ok($finalResultSet);

        } catch (\Exception $e) {

            LOG::error("Exception while getting order details", (array)$e->getMessage());
            return $this->errorResponse($e);

        }
    }


    public function generateInvoice($orderId)
    {
        $genFiles = new GeneratePDF();
        return $genFiles->pdf();
    }

    public function getOrdersByOrderBatchId($orderBatchId)
    {

        try {

            $userId = JWTAuth::parseToken()->getPayload()->get('id');
            $this->postService = $this->serviceFactory->makeService();
            $resultSet = $this->postService->getOrdersByOrderBatchId($orderBatchId, $userId);
            return ShipRes::ok($resultSet);

        } catch (\Exception $e) {

            LOG::error("Exception while getting order batch details", (array)$e->getMessage());
            return $this->errorResponse($e);

        }

    }

}