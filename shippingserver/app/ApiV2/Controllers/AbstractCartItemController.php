<?php

namespace ApiV2\Controllers;

use ApiV2\Requests\BaseShippingResponse as ShipRes;
use App\Exceptions\ApplicationException;
use Illuminate\Http\Request;
use Log;
use PHPExcel_IOFactory;
use Tymon\JWTAuth\Facades\JWTAuth;

class AbstractCartItemController extends BaseController implements ICartItemController
{

    /**
     * @var AbstractCartItemFactory
     */
    protected $serviceFactory;

    public function getCartItems()
    {

        try {

            Log::info("In Get Cart Details");

            $this->postService = $this->serviceFactory->makeService();

            //TODO $request->attributes->add(['securityPrincipal' => 'myValue']);
            //$this->postService->setSecurityPrincipal($request->attributes->get("securityPrincipal"));

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            $buyerId = JWTAuth::parseToken()->getPayload()->get('id');

            //Delegate request to getcartItems
            $sp = $this->postService->getCartItems($buyerId);

            //Return Response
            LOG::info('response  from seller Post Service ', (array)$sp);
            return ShipRes::ok($sp);

        } catch (\Exception $e) {

            LOG::error("Could not get cart items", (array)$e->getMessage());
            return $this->errorResponse($e);

        }
    }

    public function getCartDetailsById($cartId)
    {

        try {

            Log::info("In Get Cart Details");

            $this->postService = $this->serviceFactory->makeService();

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to getCartdetailsById
            $sp = $this->postService->getCartdetailsById($cartId, "GET");

            return ShipRes::ok($sp);

        } catch (\Exception $e) {

            LOG::error("Could not get cart details by id", (array)$e->getMessage());
            return $this->errorResponse($e);

        }
    }

    public function addInitialCartDetails(Request $request)
    {

        try {

            Log::info("In Add to cart");
            $payload = $request->getContent();

            $bo = $this->serviceFactory->makeTransformer()->ui2bo_save($payload);
            $this->postService = $this->serviceFactory->makeService();
            $this->validateService = $this->serviceFactory->makeValidator();
            $bo = $this->postService->updateBoForInitData($bo);
            $this->validateService->validateSaveInit($bo);
            $boSaved = $this->postService->saveInit($bo);
            return ShipRes::ok($boSaved);

        } catch (\Exception $e) {

            LOG::error("Could not add to initial details by id", (array)$e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function updateCartDetails(Request $request, $cartId)
    {

        try {

            Log::info("In Add to cart");
            $payload = $request->getContent();
            $this->postService = $this->serviceFactory->makeService();
            $bo = $this->postService->getCartdetailsById($cartId, "UPDATE");
            $uiToBo = $this->serviceFactory->makeTransformer()->ui2bo_save($payload);
            $updatedBo = $this->postService->updateBoWithUiFileds($bo, $uiToBo);
            $this->validateService = $this->serviceFactory->makeValidator();
            $this->validateService->validateSave($updatedBo);
            $boSaved = $this->postService->save($updatedBo);
            return ShipRes::ok($boSaved);

        } catch (\Exception $e) {

            LOG::error("Could not update cart details", (array)$e->getMessage());
            return $this->errorResponse($e);

        }
    }

    /**
     * @param $id
     * @return ShipRes
     */

    public function deleteCartById($cartId)
    {

        try {

            Log::info("In delete cart by id");
            $this->postService = $this->serviceFactory->makeService();
            $this->postService->setServicefactory($this->serviceFactory);
            $buyerId = JWTAuth::parseToken()->getPayload()->get('id');
            $cartItems = $this->postService->deleteCartById($cartId, $buyerId);
            return ShipRes::ok($cartItems);

        } catch (\Exception $e) {

            LOG::error("Could not delete cart by id", (array)$e->getMessage());
            return $this->errorResponse($e);
        }
    }

    /**
     * @param $buyerId
     * @param $serviceId
     * @return ShipRes
     */

    public function emptyCart()
    {

        try {
            Log::info("In empty cart");
            $this->postService = $this->serviceFactory->makeService();

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            $buyerId = JWTAuth::parseToken()->getPayload()->get('id');
            //Delegate request to CartItemService
            $status = $this->postService->emptyCart($buyerId);

            //Return Response
            if (!$status) {
                return ShipRes::fail([], ["Problem deleting the cart item"]);
            }

            return ShipRes::ok(['cart emptied']);

        } catch (\Exception $e) {

            LOG::error("Could not empty cart", (array)$e->getMessage());
            return $this->errorResponse($e);

        }
    }


    /**
     * Upload Cart Items Via an Excel Workbook
     * TODO This is a test method. Remove the route after testing.
     * @param $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadCartExcel(Request $request)
    {
        try {

            if (!$request->hasFile('uploadFile')) {
                throw new ApplicationException([], ["uploadFile needs to be specified"]);
            }

            //get the file
            $file = $request->file('uploadFile');

            //Load Excel
            $objPHPExcel = PHPExcel_IOFactory::load($file);

            //Parse Details sheet
            $detailSheet = $objPHPExcel->getSheet(0);
            $topRow = $detailSheet->getHighestRow();

            $details = [];

            LOG::debug("Rows found in Details sheet [" . $topRow . "]");

            for ($row = 2; $row <= $topRow; ++$row) {
                $rowData = $detailSheet->rangeToArray('A' . $row . ':' . 'AM' . $row, NULL, TRUE, FALSE);
                array_push($details, $rowData[0]);
            }

            LOG::debug("Converting xls to bo");

            //Transform Excel rows into a BO.
            $bos = $this->serviceFactory->makeTransformer()->cart_xls2bo_save($details);

            LOG::debug("Converted to bo");

            //Get the service
            $this->postService = $this->serviceFactory->makeService();

            //Set BuyerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to BuyerPostService
            $boSaved = $this->postService->saveMultipleCartItems($bos);
            $paymentType = "HDFC";
            $Dr = "hbvnecu5L6NJNCOxUeQJ8tFoflw0zr0hN8q0J7mET3RKK8UTByvhpj6%206f2Tjsw5QwQevmhFBzZyY421inWf8wSc2KNpDhHX6zLbVuxzzJmoFlac2nixQ80pyJWECsvFoWyj6ZSlmaP%2FZH87KArJ%2FeCG%203XWA46ulzo3XJKk9gTZB2cylRt7GiF1TSqYGVcwYKIc%2F0jb91MnVXWvYkirPy4sPbXwhqlEZ2SsC685txPtK%20QfnJv73lmN5bqEQktFjRv9%2F8upGBgJb8rRL7K1UZTNTFw6CFbqnW3TL2mLM5WLXsbxvh2z8oEKlKnjOH1G5Lf1KKlTXwPkpbtiQrons38wAHCI5f4XVz%20JsvGqVu9RYoeczAlYfhSH9txtGu5Q9VWVVTLX7vsKf6z4d8Ft3g%20%2FIQJRuflgXd2JiNkuTljJSAcq8LWmKVMjm6a7b1UMddGDnNEcjpXoLmCXtGq51L92WCHxLwpLjl%2FRV8l%20MAfxrY%2FvPGsDz5HED18gSXBjucP1C5D0iSVC%20hJCXdBKCcZe6mMFVgC2WnPrKuHztMoDmCUq9OFdKLl0F0hKS1rpxn%2FGL6Q0xEqG%2FPql9WurKjKRZ6miP3GKWsAHC787E%20mRL6TuB1tKhvsaSdfohz%2FSXtnlG7x26fHN0TAhe5sK8%20OJzNhrvpBrbw697DICC%2FO7UJDBQTaqtCHE0RLTzXJiqYFWfftjbSWe4g%3D%3D";
            $finalRs = [];
            $uniqKeys = [];
            foreach ($bos as $eachBo) {

                $buyerId = $eachBo->buyerId;
                $serviceId = $eachBo->serviceId;
                $uniqKey = $buyerId . "&&" . $serviceId;
                if (in_array($uniqKey, $uniqKeys)) {
                    continue;
                }
                $uniqKeys[] = $uniqKey;

                $cartItems = $this->postService->getCartItems($buyerId, $serviceId);
                $amountToPay = $cartItems['totalCharges']->totalCharges;
                $orders = [];
                foreach ($cartItems['cartItems'] as $cartItem) {
                    $orders[] = $this->postService->bo2model($cartItem)->toArray();
                }
                $this->orderPostService = $this->serviceFactory->makeOrderService();
                $resultSet = $this->orderPostService->createOrders($paymentType, $buyerId, $amountToPay, $orders);


                $transactionDetails = rawurldecode($Dr);
                $finalRs[] = $this->orderPostService->saveTransaction($paymentType, $transactionDetails);
            }

            LOG::info('Service returned ', (array)$boSaved);

            //Now checkout these items automatically.
//            $service = (new FCLOrderFactory())->makeService();
//            $service->createOrders()
//
            //Return Response
            return shipres::ok($boSaved);

        } catch (\Exception $e) {

            LOG::error("Cart excel upload", (array)$e->getMessage());
            return $this->errorResponse($e);

        }
    }

}