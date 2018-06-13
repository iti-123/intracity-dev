<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/21/17
 * Time: 12:43 PM
 */

namespace Api\Services;

use Api\BusinessObjects\Details;
use Api\BusinessObjects\OrderBaseBO;
use Api\BusinessObjects\OrderBatchBo;
use Api\BusinessObjects\OrderBO;
use Api\BusinessObjects\OrderCharges;
use Api\BusinessObjects\OrderFilterBO;
use Api\BusinessObjects\OrdersBatchBo;
use Api\BusinessObjects\OrderSearchBO;
use Api\BusinessObjects\PostDetails;
use Api\Model\BuyerContract;
use Api\Model\CartItem;
use Api\Model\Order;
use Api\Model\OrderBatch;
use Api\Services\Payments\PaymentService;
use App\Exceptions\ApplicationException;
use App\Jobs\SendEmailAlert;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderService extends BaseService implements IOrderService
{

    use DispatchesJobs;

    protected $serviceName = "BASE";

    private $orderStatusMessages = [
        'INITIAL' => 'Order Created',
        'ORDER_CONFIRMED' => '',
        'CRO_RELEASED' => [
            'mailTemplate' => ORDER_STAUS_EMAIL_ALERT_NEEDED_RESPONSE,
            'msg' => 'has submitted the CRO document'
        ],
        'CRO_ACKNOWLEDGED' => [
            'mailTemplate' => ORDER_STAUS_EMAIL_ALERT,
            'msg' => 'CRO Acknowledged'
        ],
        'VEHICLE_PLACED_AT_FACTORY' => [
            'mailTemplate' => ORDER_STAUS_EMAIL_ALERT,
            'msg' => ''
        ],
        'VEHICLE_CONFIRMED' => [
            'mailTemplate' => ORDER_STAUS_EMAIL_ALERT,
            'msg' => ''
        ],
        'CUSTOM_CLEARENCE' => [
            'mailTemplate' => ORDER_STAUS_EMAIL_ALERT,
            'msg' => ''
        ],
        'SI_DOC_UPLOADED' => [
            'mailTemplate' => ORDER_STAUS_EMAIL_ALERT,
            'msg' => 'Shipping Instruction document is submitted'
        ],
        'BL_DRAFT_ISSUED' => [
            'mailTemplate' => ORDER_STAUS_EMAIL_ALERT_NEEDED_RESPONSE,
            'msg' => 'has submitted the Draft Bill of laiding document'
        ],
        'BL_AMMENDED' => [
            'mailTemplate' => ORDER_STAUS_EMAIL_ALERT_NEEDED_RESPONSE,
            'msg' => 'has submitted the Ammendment for  Draft Bill of laiding document'
        ],
        'AMMENDED_BL' => [
            'mailTemplate' => ORDER_STAUS_EMAIL_ALERT_NEEDED_RESPONSE,
            'msg' => 'has submitted the Final  Draft Bill of laiding document'
        ],
        'DRAFT_BL_CONFIRMED' => [
            'mailTemplate' => ORDER_STAUS_EMAIL_ALERT,
            'msg' => 'B/L Draft  is confirmed'
        ],
        'ON_BOARDED' => [
            'mailTemplate' => ORDER_STAUS_EMAIL_ALERT,
            'msg' => 'is Onboarded'
        ],
        'OBL_RELEASED' => [
            'mailTemplate' => ORDER_STAUS_EMAIL_ALERT_NEEDED_RESPONSE,
            'msg' => 'have reached destination port '
        ],
        'REACHED_DESTINATION_PORT' => [
            'mailTemplate' => ORDER_STAUS_EMAIL_ALERT,
            'msg' => 'confimed delivery'
        ],
        'COMPLETED' => [
            'mailTemplate' => ORDER_STAUS_EMAIL_ALERT,
            'msg' => 'confimed delivery'
        ]
    ];

    /**
     * Gets an order by ID
     * @param $orderId
     * @return OrderBO
     */
    public function getOrderById($orderId)
    {
        $model = new Order();
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $model = $model->getOrderItemDetailsById($orderId, $userId);
        if (!count($model)) {
            Log::info("Details doesn't exists for order id $orderId");
            throw new ApplicationException([], ["Details doesn't exists"]);
        }
        $bo = $this->modelToBo($model);
        return $bo;
    }

    public function modelToBo($model)
    {

        $bo = new OrderBO();
        if (!is_array($model)) {
            $model = $model->toArray();
        }

        $bo->orderId = $model['id'];
        $bo->orderBatchId = $model['order_id'];
        $bo->orderNo = $model['order_no'];
        $bo->orderStatus = $model['status'];
        $bo->orderStatusLabel = $model['status_label'];
        $bo->serviceId = $model['lkp_service_id'];
        $bo->serviceType = $model['service_type'];
        $bo->postType = $model['post_type'];
        $bo->buyerPostId = $model['buyer_post_id'];
        $bo->sellerQuoteId = $model['seller_quote_id'];
        $bo->sellerId = $model['seller_id'];
        $bo->buyerId = $model['buyer_id'];
        $bo->title = $model['title'];
        $bo->sellerName = $model['seller_name'];
        $bo->buyerName = $model['buyer_name'];

        $bo->commodityType = $model['commodity_type'];
        $bo->cargoReadyDate = $model['cargo_ready_date'];
        $bo->loadPort = $model['load_port'];
        $bo->dischargePort = $model['discharge_port'];
        $bo->insuranceCharges = $model['insurance_charges'];
        $bo->serviceTax = $model['service_tax'];
        $bo->freightCharges = $model['freight_charges'];
        $bo->localCharges = $model['local_charges'];
        $bo->leadType = $model['lead_type'];
        $bo->paymentStatus = $model['payment_status'];

        $bo->consignorName = $model['consignor_name'];
        $bo->consignorEmail = $model['consignor_email'];
        $bo->consignorMobile = $model['consignor_mobile'];
        $bo->consignorAddress1 = $model['consignor_address1'];
        $bo->consignorAddress2 = $model['consignor_address2'];
        $bo->consignorAddress3 = $model['consignor_address3'];
        $bo->consignorPincode = $model['consignor_pincode'];
        $bo->consignorCity = $model['consignor_city'];
        $bo->consignorState = $model['consignor_state'];
        $bo->consignorCountry = $model['consignor_country'];

        $bo->validFrom = $model['valid_from'];
        $bo->validTo = $model['valid_to'];

        $bo->consigneeName = $model['consignee_name'];
        $bo->consigneeEmail = $model['consignee_email'];
        $bo->consigneeMobile = $model['consignee_mobile'];
        $bo->consigneeAddress1 = $model['consignee_address1'];
        $bo->consigneeAddress2 = $model['consignee_address2'];
        $bo->consigneeAddress3 = $model['consignee_address3'];
        $bo->consigneePincode = $model['consignee_pincode'];
        $bo->consigneeCity = $model['consignee_city'];
        $bo->consigneeState = $model['consignee_state'];
        $bo->consigneeCountry = $model['consignee_country'];
        $bo->attributes = json_decode($model['attributes']);

        $bo->additionalDetails = json_decode($model['additional_details']);
        $bo->consigneeCountry = $model['consignee_country'];
        $bo->isGsaAccepted = $model['is_gsa_accepted'];
        $bo->isConsignmentInsured = $model['is_consignment_insured'];
        if (isset($model['document_details'])) {
            $bo->documentDetails = $model['document_details'];
        }
        if (isset($model['mile_stone_details'])) {
            $mileStoneDetails = $model['mile_stone_details'];
            foreach ($mileStoneDetails as $key => $value) {
                $mileStoneDetails[$key]['additional_details'] = json_decode($mileStoneDetails[$key]['additional_details']);
            }
            $bo->mileStoneDetails = $mileStoneDetails;
        }

        $bo->quoteDetails = [];
        if (!empty($model['quote_details'])) {
            if ($model['quote_details']['attributes']) {
                $model['quote_details']['attributes'] = json_decode($model['quote_details']['attributes']);
            }
            $bo->quoteDetails = $model['quote_details'];
        }
        $bo->postDetails = [];
        if (!empty($model['post_details'])) {
            if ($model['post_details']['attributes']) {
                $model['post_details']['attributes'] = json_decode($model['post_details']['attributes']);
            }
            $bo->postDetails = $model['post_details'];
        }

        $charges = new OrderCharges();
        $charges->freightCharges = $bo->freightCharges;
        $charges->localCharges = $bo->localCharges;
        $charges->insuranceCharges = $bo->insuranceCharges;
        $charges->serviceTax = $bo->serviceTax;
        $charges->totalCharges = CurrencyConvertion::get('USD', 'INR', $charges->freightCharges)
            + $charges->localCharges
            + $charges->insuranceCharges
            + $charges->serviceTax;
        $bo->charges = $charges;

        return $bo;
    }

    /**
     * Gets orders by user type and user id
     * @param $userType
     * @param $userId
     * @return OrdersBOs
     */
    public function getOrdersByUserId($userType, $userId, $request)
    {

        $filter_key = [];


        $filter_key = $this->getPostFilters($request);
        // dd($filter_key);

        $dbObj = new Order();

        $resultSet = $dbObj->getOrdersByUserId($userType, $userId);


        if ($filter_key['type']) {
            $resultSet = $resultSet->whereIn('lead_type', $filter_key['type']);
        }
        if ($filter_key['loadPort']) {
            $resultSet = $resultSet->whereIn('load_port', $filter_key['loadPort']);
        }
        if ($filter_key['dischargePort']) {
            $resultSet = $resultSet->whereIn('discharge_port', $filter_key['dischargePort']);
        }
        if ($filter_key['name']) {
            if ($userType == 'seller') {
                $resultSet = $resultSet->whereIn('buyer_name', $filter_key['name']);
            } else if ($userType == 'buyer') {
                $resultSet = $resultSet->whereIn('seller_name', $filter_key['name']);
            }

        }
        if ($filter_key['commodityType']) {
            $resultSet = $resultSet->whereIn('commodity_type', $filter_key['commodityType']);
        }
        if ($filter_key['containerType']) {
            $resultSet = $resultSet->whereIn('attributes->containers->containerType', $filter_key['containerType']);
        }
        if ($filter_key['orderNo']) {
            $resultSet = $resultSet->whereIn('order_no', $filter_key['orderNo']);
        }
        if ($filter_key['consignee']) {
            $resultSet = $resultSet->whereIn('consignee_name', $filter_key['consignee']);
        }
        // dd($resultSet);
        $loadPorts = $resultSet->unique('load_port')->pluck('load_port');

        $dischargePorts = $resultSet->unique('discharge_port')->pluck('discharge_port');

        $commodityTypes = $resultSet->unique('commodity_type')->pluck('commodity_type');

        if ($userType == 'seller') {
            $names = $resultSet->unique('buyer_name')->pluck('buyer_name');

        } else if ($userType == 'buyer') {
            $names = $resultSet->unique('seller_name')->pluck('seller_name');

        }
        $boArray = [];
        // dd($resultSet);
        foreach ($resultSet as $eachOrder) {
            $bo = $this->modelToBo($eachOrder);
            $boArray[] = $bo;
        }
        $orderCollection = new Collection($boArray);
        $allContainers = $orderCollection->map(function ($order) {
            $containers = collect($order->attributes->containers);
            foreach ($containers as $eachContainer) {
                return $eachContainer;
            }
        });
        // dd($allContainers);
        $containerTypes = $allContainers->unique('containerType')->pluck('containerType');
        // dd($containerTypes);
        $commodityTypes = $resultSet->unique('commodity_type')->pluck('commodity_type');

        $orderNos = $resultSet->unique('order_no')->pluck('order_no');

        $consignees = $resultSet->unique('consignee_name')->pluck('consignee_name');


        $filters = $this->getFilterBO($loadPorts, $dischargePorts, $commodityTypes, $names, $containerTypes, $orderNos, $consignees);

        if ($userType == 'buyer') {
            $contractsObj = new BuyerContract();
            $contractsResultSet = $contractsObj->getContractsByUserId($userId);
            if (count($contractsResultSet)) {
                $contractsResultSet = $contractsResultSet->toArray();
            }
            $contracts = [];
            foreach ($contractsResultSet as $eachContract) {
                $eachContract['attributes'] = json_decode($eachContract['attributes']);
                $eachContract['sellerName'] = UserDetailsService::getUserDetails($eachContract['sellerId'])->username;
                $contracts[] = $eachContract;
            }
            $boArray = [];
            $boArray['orders'] = [];
            foreach ($resultSet as $eachOrder) {
                $bo = $this->modelToBaseBo($eachOrder);
                $boArray['orders'][] = $bo;
            }
            $boArray['contracts'] = $contracts;
            $orders = [
                'data' => $boArray,
                'filters' => $filters
            ];
        } else {
            $groupedOrders = $resultSet->groupBy('post_type')->transform(function ($item, $k) {
                return $item->groupBy('seller_quote_id');
            });
            $data = [];
            foreach ($groupedOrders as $eachOrderGroupedByPostType) {
                foreach ($eachOrderGroupedByPostType as $eachOrderGroupedByQuoteId) {
                    $eachOrderGroup = [];
                    foreach ($eachOrderGroupedByQuoteId as $eachOrder) {
                        $bo = $this->modelToBaseBo($eachOrder);
                        $eachOrderGroup[] = $bo;
                    }
                    $data[] = $eachOrderGroup;
                }
            }

            $orders = [
                'data' => $data,
                'filters' => $filters
            ];

        }


        return $orders;

    }

    public function getPostFilters($request)
    {

        $filter_key = [];
        $type = $request->all()['filters']['type'];
        $type_key = [];
        foreach ($type as $type) {
            if ($type) {
                array_push($type_key, $type);
            }
        }

        $filter_key['type'] = $type_key;
        $loadPort = $request->all()['filters']['loadPort'];
        $loadPort_key = [];
        foreach ($loadPort as $loadPort) {
            if ($loadPort) {
                array_push($loadPort_key, $loadPort);
            }
        }
        $filter_key['loadPort'] = $loadPort_key;
        $dischargePort = $request->all()['filters']['dischargePort'];
        $dischargePort_key = [];
        foreach ($dischargePort as $dischargePort) {
            if ($dischargePort) {
                array_push($dischargePort_key, $dischargePort);
            }
        }
        $filter_key['dischargePort'] = $dischargePort_key;
        $name = $request->all()['filters']['name'];
        $name_key = [];
        foreach ($name as $name) {
            if ($name) {
                array_push($name_key, $name);
            }
        }
        $filter_key['name'] = $name_key;
        $commodityType = $request->all()['filters']['commodityType'];
        $commodityType_key = [];
        foreach ($commodityType as $commodityType) {
            if ($commodityType) {
                array_push($commodityType_key, $commodityType);
            }
        }
        $filter_key['commodityType'] = $commodityType_key;
        $containerType = $request->all()['filters']['containerType'];
        $containerType_key = [];
        foreach ($containerType as $containerType) {
            if ($containerType) {
                array_push($containerType_key, $containerType);
            }
        }
        $filter_key['containerType'] = $containerType_key;
        $orderNo = $request->all()['filters']['orderNo'];
        $orderNo_key = [];
        foreach ($orderNo as $orderNo) {
            if ($orderNo) {
                array_push($orderNo_key, $orderNo);
            }
        }
        $filter_key['orderNo'] = $orderNo_key;
        $consignee = $request->all()['filters']['consignee'];
        $consignee_key = [];
        foreach ($consignee as $consignee) {
            if ($consignee) {
                array_push($consignee_key, $consignee);
            }
        }
        $filter_key['consignee'] = $consignee_key;

        return $filter_key;
    }

    public function getFilterBO($loadPorts, $dischargePorts, $commodityTypes, $names, $containerTypes, $orderNos, $consignees)
    {

        $bo = new OrderFilterBO();
        $bo->loadPort = $loadPorts;
        $bo->dischargePort = $dischargePorts;
        $bo->commodityType = $commodityTypes;
        $bo->containerType = $containerTypes;
        $bo->name = $names;
        $bo->orderNo = $orderNos;
        $bo->consignee = $consignees;
        return $bo;
    }

    public function modelToBaseBo($model)
    {

        $bo = new OrderBaseBO();
        $bo_details = new Details();
        $bo_post_details = new PostDetails();
        $model = $model->toArray();
        $bo_details->orderId = $model['id'];
        $bo_details->orderNo = $model['order_no'];
        $bo->orderStatus = $model['status'];
        $bo->serviceId = $model['lkp_service_id'];
        $bo->serviceType = $model['service_type'];
        $bo->postType = $model['post_type'];
        $bo->sellerId = $model['seller_id'];
        $bo->buyerId = $model['buyer_id'];
        $bo->title = $model['title'];
        $bo->sellerName = $model['seller_name'];
        $bo->buyerName = $model['buyer_name'];
        $bo->sellerQuoteId = $model['seller_quote_id'];
        $bo->buyerPostId = $model['buyer_post_id'];
        $bo->validFrom = $model['valid_from'];
        $bo->validTo = $model['valid_to'];
        // dd($model['get_seller_post_details']['title']);
        if ($model['post_type'] == 'SP') {
            $bo_post_details->postId = $model['get_seller_post_details']['id'];
            $bo_post_details->postTitle = $model['get_seller_post_details']['title'];

        } else if ($model['post_type'] == 'SQ') {
            $bo_post_details->postId = $model['get_seller_post_details']['id'];
            $bo_post_details->postTitle = $model['post_details']['title'];
        }
        $bo_details->commodityType = $model['commodity_type'];
        $bo->loadPort = $model['load_port'];
        $bo->dischargePort = $model['discharge_port'];
        $bo_details->consignorName = $model['consignor_name'];
        $bo_details->consigneeName = $model['consignee_name'];

        $attributes = json_decode($model['attributes']);
        $containers_array = [];
        $no_of_containers = 0;

        $containers = $attributes->containers;
        $carrier = isset($attributes->carrierDetails) ? $attributes->carrierDetails : "";
        foreach ($containers as $item) {
            $containerType = $item->containerType;
            $no_of_containers = $no_of_containers + $item->quantity;
            array_push($containers_array, $containerType);
        }

        $bo_details->containerType = $containers_array;
        $bo_details->containers = $no_of_containers;
        $bo->details = $bo_details;
        $bo->postDetails = $bo_post_details;

        return $bo;

    }

    /**
     * Gets an orders by PostId
     * @param $postId
     * @return OrderBO
     */
    public function getOrdersByPosts($postId)
    {
        $resultSet = Order::where('seller_quote_id', $postId)
            ->where('payment_status', "SUCCESS")
            ->with('documentDetails')
            ->get();

        if (!count($resultSet)) {
            Log::info("Details doesn't exists for post id $postId");
            throw new ApplicationException([], ["Details doesn't exists"]);
        }
        return $resultSet;
    }

    /**
     * Get statistics for a set of orders
     * @param array $orderIds
     * @return Order statistics for a set of orders
     */
    public function getOrderStats($orderIds = [])
    {

    }

    /**
     * Create an order
     * @param $orderBO The orderBO
     * @param $transitionName The name of the transition to apply
     * @return OrderBO
     */

    public function createOrder(OrderBO $orderBO)
    {
        DB::beginTransaction();
        $model = $this->boToModel($orderBO);
        $isSaved = $model->save();
        DB::commit();
        if (!$isSaved) {
            Log::info("Not able to save orde details");
            throw new ApplicationException([], "Not able to save order details");
        }
        return $this->modelToBo($model);
    }

    public function boToModel($bo)
    {
        if (isset($bo->orderId)) {
            $model = Order::find($bo->orderId);
            $model->status = $bo->orderStatus;
            $model->status_label = $bo->orderStatusLabel;
        } else {
            $model = new Order();
            $model->status = "INITIAL";
            $model->status_label = "INITIAL";
        }

        $model->lkp_service_id = $bo->serviceId;
        $model->service_type = $bo->serviceType;
        $model->post_type = $bo->postType;
        $model->buyer_post_id = $bo->buyerPostId;
        $model->seller_quote_id = $bo->sellerQuoteId;
        $model->seller_id = $bo->sellerId;
        $model->buyer_id = $bo->buyerId;
        $model->buyer_name = $bo->buyerName;
        $model->seller_name = $bo->sellerName;

        $model->consignor_name = $bo->consignorName;
        $model->consignor_email = $bo->consignorEmail;
        $model->consignor_mobile = $bo->consignorMobile;
        $model->consignor_address1 = $bo->consignorAddress1;
        $model->consignor_address2 = $bo->consignorAddress2;
        $model->consignor_address3 = $bo->consignorAddress3;
        $model->consignor_pincode = $bo->consignorPincode;
        $model->consignor_city = $bo->consignorCity;
        $model->consignor_state = $bo->consignorState;
        $model->consignor_country = $bo->consignorCountry;

        $model->consignee_name = $bo->consigneeName;
        $model->consignee_email = $bo->consigneeEmail;
        $model->consignee_mobile = $bo->consigneeMobile;
        $model->consignee_address1 = $bo->consigneeAddress1;
        $model->consignee_address2 = $bo->consigneeAddress2;
        $model->consignee_address3 = $bo->consigneeAddress3;
        $model->consignee_pincode = $bo->consigneePincode;
        $model->consignee_city = $bo->consigneeCity;
        $model->consignee_state = $bo->consigneeState;
        $model->consignee_country = $bo->consigneeCountry;

        $model->attributes = json_encode((array)$bo->attributes);
        $model->additional_details = $bo->additionalDetails;

        $model->is_gsa_accepted = $bo->isGsaAccepted;
        $model->is_consignment_insured = $bo->isConsignmentInsured;

        return $model;
    }

    /**
     * Perform an action on the order
     * @param $orderId The orderId
     * @param $transitionName The name of the transition to apply
     * @return OrderBO
     */
    public function performAction(OrderBO $bo, $stateMachine, $updateBo)
    {

        $transitionName = $updateBo->transition;

        $bo = $stateMachine->run($transitionName, $bo, $updateBo);

        DB::beginTransaction();
        $model = $this->boToModel($bo);


        $isSaved = $model->save();
        DB::commit();
        if (!$isSaved) {
            Log::info("Not able to save order details");
            throw new ApplicationException([], ["Not able to save order details"]);
        }
        $this->raiseAlert($bo);
        return $this->modelToBo($model);
    }

    public function raiseAlert($bo)
    {

        $params = [];
        if (!$this->orderStatusMessages[$bo->orderStatus]) {
            return true;
        }
        $params['tostatus'] = $bo->orderStatusLabel;
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $params['buyername'] = $bo->buyerName;
        $params['sellername'] = $bo->sellerName;
        if ($userId == $bo->buyerId) {
            $params['username'] = $bo->buyerName;
            $params['email'] = UserDetailsService::getUserDetails($bo->buyerId)->email;
        } else if ($userId == $bo->sellerId) {
            $params['username'] = $bo->sellerName;
            $params['email'] = UserDetailsService::getUserDetails($bo->sellerId)->email;
        }
        $params['status_update'] = $this->orderStatusMessages[$bo->orderStatus]['msg'];
        $params['emailAlertTemplateId'] = $this->orderStatusMessages[$bo->orderStatus]['mailTemplate'];

        $job = new SendEmailAlert((array)$params['email'], $params, $params['emailAlertTemplateId']);
        $this->dispatch($job);

    }

    public function updateBo($stateMachine, $bo)
    {

        $state = $bo->orderStatus;
        $bo->allowedTransitions = $stateMachine->getAllowedTransitions($state);
        return $bo;
    }


    /*
     * Get Post filter values
     */

    /**
     * Filter orders based on various criteria
     * @param OrderSearchBO $orderFilter
     * @return Collection of Orders
     */
    public function filterOrders(OrderSearchBO $orderFilter)
    {


    }

    public function createOrders($paymentType, $buyerId, $amountToPay, $cartItems)
    {

        $orderObj = new OrderBatch();
        $orderObj->buyer_id = $buyerId;
        $orderObj->payment_type = $paymentType;
        $orderObj->amount_to_pay = $amountToPay;
        $orderObj->amount_received = 0;
        //$orderObj->payment_status = "INPROGRESS";
        $orderObj->payment_status = "SUCCESS";
        $orderObj->save();
        $orderId = $orderObj->id;

        foreach ($cartItems as $cartItem) {
            $orderItemObj = $this->cartItemToOrderItem($cartItem);
            $orderItemObj->order_id = $orderId;
            $orderItemObj->save();
            if (!$orderItemObj->id) {
                throw new ApplicationException([], ["Failed to insert Data"]);
            }
            $orderNumber = GenerateSequenceNumber::get($this->serviceName, $orderItemObj->id);
            $orderItemObj->order_no = $orderNumber;
            $orderItemObj->save();
            $cartItemObj = new CartItem();
            $cartItemObj->updateCartItemStatus($cartItem['id']);
            /*
             * TODO: Populate container types in order Items table
             * */
        }
        $resultSet = [];
        $paymentObj = PaymentService::createPaymentObj($paymentType);
        $resultSet['action'] = $paymentObj->getActionUrl();
        $resultSet['formFields'] = $paymentObj->getFormFields($orderId, $amountToPay, $buyerId);

        return $resultSet;

    }

    public function cartItemToOrderItem($cartItemDetails)
    {

        $orderItemModelObj = new Order();

        $orderItemModelObj->title = $cartItemDetails['title'];
        $orderItemModelObj->buyer_id = $cartItemDetails['buyer_id'];
        $orderItemModelObj->seller_id = $cartItemDetails['seller_id'];
        $orderItemModelObj->post_type = $cartItemDetails['post_type'];
        $orderItemModelObj->lkp_service_id = $cartItemDetails['lkp_service_id'];
        $orderItemModelObj->service_type = $cartItemDetails['service_type'];
        $orderItemModelObj->buyer_post_id = $cartItemDetails['buyer_post_id'];
        $orderItemModelObj->seller_quote_id = $cartItemDetails['seller_quote_id'];
        $orderItemModelObj->is_consignment_insured = $cartItemDetails['is_consignment_insured'];
        $orderItemModelObj->insurance_details = $cartItemDetails['insurance_details'];

        //$orderItemModelObj->payment_status = "INPROGRESS";
        $orderItemModelObj->payment_status = "SUCCESS";
        $orderItemModelObj->post_type = $cartItemDetails['post_type'];
        $orderItemModelObj->buyer_name = $cartItemDetails['buyer_name'];
        $orderItemModelObj->seller_name = $cartItemDetails['seller_name'];
        $orderItemModelObj->lead_type = $cartItemDetails['lead_type'];
        $orderItemModelObj->load_port = $cartItemDetails['load_port'];
        $orderItemModelObj->discharge_port = $cartItemDetails['discharge_port'];
        $orderItemModelObj->cargo_ready_date = $cartItemDetails['cargo_ready_date'];
        $orderItemModelObj->commodity_type = $cartItemDetails['commodity_type'];

        $orderItemModelObj->valid_from = $cartItemDetails['valid_from'];
        $orderItemModelObj->valid_to = $cartItemDetails['valid_to'];

        $orderItemModelObj->consignor_name = $cartItemDetails['consignor_name'];
        $orderItemModelObj->consignor_email = $cartItemDetails['consignor_email'];
        $orderItemModelObj->consignor_mobile = $cartItemDetails['consignor_mobile'];
        $orderItemModelObj->consignor_address1 = $cartItemDetails['consignor_address1'];
        $orderItemModelObj->consignor_address2 = $cartItemDetails['consignor_address2'];
        $orderItemModelObj->consignor_address3 = $cartItemDetails['consignor_address3'];
        $orderItemModelObj->consignor_pincode = $cartItemDetails['consignor_pincode'];
        $orderItemModelObj->consignor_city = $cartItemDetails['consignor_city'];
        $orderItemModelObj->consignor_state = $cartItemDetails['consignor_state'];
        $orderItemModelObj->consignor_country = $cartItemDetails['consignor_country'];

        $orderItemModelObj->consignee_name = $cartItemDetails['consignee_name'];
        $orderItemModelObj->consignee_email = $cartItemDetails['consignee_email'];
        $orderItemModelObj->consignee_mobile = $cartItemDetails['consignee_mobile'];
        $orderItemModelObj->consignee_address1 = $cartItemDetails['consignee_address1'];
        $orderItemModelObj->consignee_address2 = $cartItemDetails['consignee_address2'];
        $orderItemModelObj->consignee_address3 = $cartItemDetails['consignee_address3'];
        $orderItemModelObj->consignee_pincode = $cartItemDetails['consignee_pincode'];
        $orderItemModelObj->consignee_city = $cartItemDetails['consignee_city'];
        $orderItemModelObj->consignee_state = $cartItemDetails['consignee_state'];
        $orderItemModelObj->consignee_country = $cartItemDetails['consignor_country'];

        $orderItemModelObj->is_gsa_accepted = $cartItemDetails['is_gsa_accepted'];

        $orderItemModelObj->attributes = $cartItemDetails['attributes'];
        $orderItemModelObj->search_data = $cartItemDetails['search_data'];

        $orderItemModelObj->status = 'INITIAL';

        $orderItemModelObj->freight_charges = $cartItemDetails['freight_charges'];
        $orderItemModelObj->local_charges = $cartItemDetails['local_charges'];
        $orderItemModelObj->insurance_charges = json_encode($cartItemDetails['insurance_charges']);
        $orderItemModelObj->service_tax = $cartItemDetails['service_tax'];

        return $orderItemModelObj;
    }

    public function saveTransaction($paymentType, $transactionDetails)
    {

        $paymentService = PaymentService::createPaymentObj($paymentType);
        $bo = $paymentService->saveTransaction($transactionDetails);
        $this->updateOrderPaymentStatus($bo);
        return $bo;

    }

    public function updateOrderPaymentStatus($bo)
    {

        $orderModel = OrderBatch::find($bo->orderId);

        $paymentStatus = "FAILED";
        if ($bo->paymentStatus) {
            $paymentStatus = "SUCCESS";
        }
        $orderModel->payment_status = $paymentStatus;
        $orderModel->reference_no = $bo->transactionId;
        $orderModel->save();

        $orderItemModel = new Order();
        $orderItemModel = $orderItemModel
            ->where("order_id", $bo->orderId)
            ->update([
                'payment_status' => $paymentStatus
            ]);

        return $orderItemModel;

    }


    public function getOrdersByOrderBatchId($orderBatchId, $userId)
    {

        $orderBatchObj = new OrderBatch();
        $resultSet = $orderBatchObj->getOrders($orderBatchId, $userId)->toArray();
        if (!count($resultSet)) {
            throw new ApplicationException([], "Invalid Order Batch Id");
        }
        return $this->modelToBoOrderBatch($resultSet);

    }

    public function modelToBoOrderBatch($model)
    {

        $bo = new OrderBatchBo();
        $bo->orderBatchId = $model['id'];
        $bo->amountToPay = $model['amount_to_pay'];
        $bo->amountReceived = $model['amount_received'];
        $bo->paymentSuccess = $model['payment_status'];
        foreach ($model['orders'] as $eachOrder) {
            $bo->orders[] = $this->modelToBo($eachOrder);
        }
        return $bo;

    }

}