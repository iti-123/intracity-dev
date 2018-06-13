<?php
/**
 * Created by PhpStorm.
 * User: sainath
 * Date: 2/20/17
 * Time: 3:40 PM
 */

namespace Api\Modules\FCL;

use Api\BusinessObjects\OrderBO;
use Api\Framework\Workflow\AbstractStateMachine;
use Api\Framework\Workflow\IState;
use Api\Framework\Workflow\Transition;
use Api\Model\OrderDoc;
use Api\Model\OrderMilestone;
use Api\Services\ShpUploadFiles;
use App\Exceptions\ApplicationException;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class FCLOrderStateMachine extends AbstractStateMachine
{

    const NAME = "Api\Modules\FCL\FCLOrderStateMachine",
        P2P = "P2P",
        P2D = "P2D",
        D2D = "D2D",
        D2P = "D2P";

    protected $bo = null;

    public function __construct(OrderBO $bo)
    {
        $this->bo = $bo;
        $this->addStates();
        $this->addTransitions();

    }

    public function addStates()
    {

        $this->addState('INITIAL', "Order Placed", IState::INITIAL_STATE);
        $this->addState('ORDER_CONFIRMED', "Order confirmed by seller");
        $this->addState('CRO_RELEASED', "CRO Released");
        $this->addState('CRO_ACKNOWLEDGED', "CRO Acknowledged");

        if ($this->bo->serviceType == self::D2P || $this->bo->serviceType == self::D2D) {
            $this->addState('VEHICLE_PLACED_AT_FACTORY', "Vehicle Placed at Factory");
            $this->addState('VEHICLE_CONFIRMED', "Vehicle Confirmed");
            $this->addState('CUSTOM_CLEARENCE', "Custom Clearence at Origin");
        }

        $this->addState('SI_DOC_UPLOADED', "SI Uploaded");
        $this->addState('BL_DRAFT_ISSUED', "Draft BL Issued");
        $this->addState('BL_AMMENDED', "BL Ammended");
        $this->addState('AMMENDED_BL', "BL Ammended");
        $this->addState('FINAL_BL_ISSUED', "Final BL Issued");
        $this->addState('DRAFT_BL_CONFIRMED', "Draft BL confirmed");
        $this->addState('ON_BOARDED', "Onboarded");
        $this->addState('OBL_RELEASED', "BL Release");
        /*
                if($this->bo->serviceType == self::P2D  || $this->bo->serviceType == self::D2D ) {
                    $this->addState('REACHED_PORT', "Reached Port");
                }
        */
        $this->addState('REACHED_DESTINATION_PORT', "Reached Destination Port");
        $this->addState('COMPLETED', "Order Completed", IState::FINAL_STATE);

    }

    public function addTransitions()
    {
        /*
                $this->addTransition(
                    new ConfirmOrder('CONFIRM_ORDER',
                        $this->getState('INITIAL'),
                        $this->getState('ORDER_CONFIRMED'))
                );

                $this->addTransition(
                    new CROIssued('ISSUE_CRO',
                        $this->getState('ORDER_CONFIRMED'),
                        $this->getState('CRO_RELEASED'))
                );
        */

        $this->addTransition(
            new CROIssued('ISSUE_CRO',
                $this->getState('INITIAL'),
                $this->getState('CRO_RELEASED'))
        );
        $this->addTransition(
            new AcknowledgeCRO('ACKNOWLEDGE_CRO',
                $this->getState('CRO_RELEASED'),
                $this->getState('CRO_ACKNOWLEDGED'))
        );
        $this->addTransition(
            new UploadSIDoc('UPLOAD_SI_DOC',
                $this->getState('CRO_ACKNOWLEDGED'),
                $this->getState('SI_DOC_UPLOADED'))
        );

        if ($this->bo->serviceType == self::D2P || $this->bo->serviceType == self::D2D) {
            $this->addTransition(
                new PlaceVehicle('PLACE_VEHICLE',
                    $this->getState('SI_DOC_UPLOADED'),
                    $this->getState('VEHICLE_PLACED_AT_FACTORY'))
            );
            $this->addTransition(
                new ConfirmVehicleReach('CONFIRM_VEHICLE_REACH',
                    $this->getState('VEHICLE_PLACED_AT_FACTORY'),
                    $this->getState('VEHICLE_CONFIRMED'))
            );
            $this->addTransition(
                new CustomClearence('CUSTOM_CLEARENCE_AT_ORIGIN',
                    $this->getState('VEHICLE_CONFIRMED'),
                    $this->getState('CUSTOM_CLEARENCE'))
            );
            $this->addTransition(
                new IssueBLDraft('ISSUE_BL_DRAFT',
                    $this->getState('CUSTOM_CLEARENCE'),
                    $this->getState('BL_DRAFT_ISSUED'))
            );
        } else {
            $this->addTransition(
                new IssueBLDraft('ISSUE_BL_DRAFT',
                    $this->getState('SI_DOC_UPLOADED'),
                    $this->getState('BL_DRAFT_ISSUED'))
            );
        }

        $this->addTransition(
            new ConfirmBLDraft('CONFIRM_BL',
                $this->getState('BL_DRAFT_ISSUED'),
                $this->getState('DRAFT_BL_CONFIRMED'))
        );
        $this->addTransition(
            new AmmendBlDraft('AMMEND_BL',
                $this->getState('BL_DRAFT_ISSUED'),
                $this->getState('BL_AMMENDED'))
        );
        $this->addTransition(
            new AmmendedBl('AMMENDED_BL',
                $this->getState('BL_AMMENDED'),
                $this->getState('AMMENDED_BL'))
        );
        $this->addTransition(
            new ConfirmBLDraft('CONFIRM_FINAL_BL',
                $this->getState('AMMENDED_BL'),
                $this->getState('DRAFT_BL_CONFIRMED'))
        );
        $this->addTransition(
            new OnboardGoods('ONBOARD_GOODS',
                $this->getState('DRAFT_BL_CONFIRMED'),
                $this->getState('ON_BOARDED'))
        );
        $this->addTransition(
            new IssueOBL('ISSUE_OBL',
                $this->getState('ON_BOARDED'),
                $this->getState('OBL_RELEASED'))
        );
        $this->addTransition(
            new GoodsDelivered('GOODS_DELIVERED',
                $this->getState('OBL_RELEASED'),
                $this->getState('REACHED_DESTINATION_PORT'))
        );
        $this->addTransition(
            new ConfirmDelivery('CONFIRM_DELIVERY',
                $this->getState('REACHED_DESTINATION_PORT'),
                $this->getState('COMPLETED'))
        );
        /*
                if($this->bo->serviceType == self::P2D  || $this->bo->serviceType == self::D2D ) {
                    $this->addTransition(
                        new SellerConfirmsDelivery('SELLER_CONFIRMS_SHIP_REACHED_PORT',
                            $this->getState('OBL_RELEASED'),
                            $this->getState('REACHED_PORT'))
                    );
                    $this->addTransition(
                        new SellerConfirmsDelivery('SELLER_CONFIRMS_DELIVERY',
                            $this->getState('REACHED_PORT'),
                            $this->getState('SELLER_CONFIRMED_DELIVERY'))
                    );
                } else {
                    $this->addTransition(
                        new SellerConfirmsDelivery('SELLER_CONFIRMS_DELIVERY',
                            $this->getState('OBL_RELEASED'),
                            $this->getState('SELLER_CONFIRMED_DELIVERY'))
                    );
                }

                $this->addTransition(
                    new ConfirmDelivery('BUYER_CONFIRMS_DELIVERY',
                        $this->getState('SELLER_CONFIRMED_DELIVERY'),
                        $this->getState('COMPLETED'))
                );
        */
    }

    /**
     * The name of this state machine
     * @return mixed
     */
    public function getName()
    {
        return FCLOrderStateMachine::NAME;
    }

    public function run($trasitionName, OrderBO $bo, $updateBo)
    {

        $trasition = isset($this->transitions[$trasitionName]) ? $this->transitions[$trasitionName] : false;
        if (!$trasition) {
            Log::info("Invalid Request. Transition is invalid.");
            throw new ApplicationException([], ["Invalid Request"]);
        }
        if (!$trasition->isReady($trasition, $bo)) {
            Log::info("Action can't perform");
            throw new ApplicationException([], ["Action can't perform"]);
        }
        $bo = $trasition->apply($trasition, $bo, $updateBo);
        return $bo;
    }

    public function getAllowedTransitions($fromState)
    {
        $allowedTransitions = [];
        foreach ($this->transitions as $transition) {
            if ($transition->fromStateName() == $fromState) {
                $allowedTransitions[] = $transition->getName();
            }
        }
        return $allowedTransitions;
    }

}

class ConfirmOrder extends Transition
{

    public function apply($trasition, OrderBO $bo, $updateBo)
    {
        return $this->_apply($trasition, $bo, $updateBo);
    }

    private function _apply($trasition, OrderBO $bo, $updateBo)
    {

        $this->validate($bo, $updateBo);
        $toState = $trasition->toState();
        $bo->orderStatus = $toState->getName();
        $bo->orderStatusLabel = $toState->getLabel();
        return $bo;

    }

    public function validate(OrderBO $bo, $updateBo)
    {
        $role = JWTAuth::parseToken()->getPayload()->get('role');
        if (strtolower($role) != 'seller') {
            return False;
        }
    }

    public function isReady($trasition, OrderBO $bo)
    {
        return ($trasition->fromStateName() == $bo->orderStatus) ? true : false;
    }

}

class CROIssued extends Transition
{

    public function apply($trasition, OrderBO $bo, $updateBo)
    {
        return $this->_apply($trasition, $bo, $updateBo);
    }

    private function _apply($trasition, OrderBO $bo, $updateBo)
    {
        $this->validate($bo, $updateBo);
        if (!SaveMileStone::save($bo, $updateBo, "CRO_DOC")) {
            Log::info("Failed to update milestone");
            throw new ApplicationException([], ["Failed to update milestone"]);
        }
        $toState = $trasition->toState();
        $bo->orderStatus = $toState->getName();
        $bo->orderStatusLabel = $toState->getLabel();

        return $bo;
    }

    public function validate(OrderBO $bo, $updateBo)
    {
        $role = JWTAuth::parseToken()->getPayload()->get('role');
        if (strtolower($role) != 'seller') {
            Log::info("Buyer tried to update the status");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
    }

    public function isReady($trasition, OrderBO $bo)
    {
        return ($trasition->fromStateName() == $bo->orderStatus) ? true : false;
    }

}

class AcknowledgeCRO extends Transition
{

    public function apply($trasition, OrderBO $bo, $updateBo)
    {
        return $this->_apply($trasition, $bo, $updateBo);
    }

    private function _apply($trasition, OrderBO $bo, $updateBo)
    {
        $this->validate($bo, $updateBo);
        if (!SaveMileStone::save($bo, $updateBo)) {
            Log::info("Failed to update milestone");
            throw new ApplicationException([], ["Failed to update milestone"]);
        }
        $bo->orderStatus = $trasition->toStateName();
        return $bo;
    }

    public function validate(OrderBO $bo, $updateBo)
    {
        $role = JWTAuth::parseToken()->getPayload()->get('role');
        if (strtolower($role) != 'buyer') {
            Log::info("Seller tried to update the status");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
        return True;
    }

    public function isReady($trasition, OrderBO $bo)
    {
        return ($trasition->fromStateName() == $bo->orderStatus) ? true : false;
    }

}

class UploadSIDoc extends Transition
{

    public function apply($trasition, OrderBO $bo, $updateBo)
    {
        return $this->_apply($trasition, $bo, $updateBo);
    }

    private function _apply($trasition, OrderBO $bo, $updateBo)
    {
        $this->validate($bo, $updateBo);
        if (!SaveMileStone::save($bo, $updateBo, "SI_DOC")) {
            Log::info("Failed to update milestone");
            throw new ApplicationException([], ["Failed to update milestone"]);
        }
        $bo->orderStatus = $trasition->toStateName();
        return $bo;
    }

    public function validate(OrderBO $bo, $updateBo)
    {
        $role = JWTAuth::parseToken()->getPayload()->get('role');
        if (strtolower($role) != 'buyer') {
            Log::info("Seller tried to update the status");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
        if (!$updateBo->documentId) {
            Log::info("SI Doc is not provided");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
    }

    public function isReady($trasition, OrderBO $bo)
    {
        return ($trasition->fromStateName() == $bo->orderStatus) ? true : false;
    }

}

class PlaceVehicle extends Transition
{

    public function apply($trasition, OrderBO $bo, $updateBo)
    {
        return $this->_apply($trasition, $bo, $updateBo);
    }

    private function _apply($trasition, OrderBO $bo, $updateBo)
    {
        $this->validate($bo, $updateBo);
        if (!SaveMileStone::save($bo, $updateBo, "VP_DOC")) {
            Log::info("Failed to update milestone");
            throw new ApplicationException([], ["Failed to update milestone"]);
        }
        $bo->orderStatus = $trasition->toStateName();
        return $bo;
    }

    public function validate(OrderBO $bo, $updateBo)
    {
        $role = JWTAuth::parseToken()->getPayload()->get('role');
        if (strtolower($role) != 'seller') {
            Log::info("Buyer tried to update the status");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
    }

    public function isReady($trasition, OrderBO $bo)
    {
        return ($trasition->fromStateName() == $bo->orderStatus) ? true : false;
    }

}

class ConfirmVehicleReach extends Transition
{

    public function apply($trasition, OrderBO $bo, $updateBo)
    {
        return $this->_apply($trasition, $bo, $updateBo);
    }

    private function _apply($trasition, OrderBO $bo, $updateBo)
    {
        $this->validate($bo, $updateBo);
        if (!SaveMileStone::save($bo, $updateBo)) {
            Log::info("Failed to update milestone");
            throw new ApplicationException([], ["Failed to update milestone"]);
        }
        $bo->orderStatus = $trasition->toStateName();
        return $bo;
    }

    public function validate(OrderBO $bo, $updateBo)
    {
        $role = JWTAuth::parseToken()->getPayload()->get('role');
        if (strtolower($role) != 'buyer') {
            Log::info("Seller tried to update the status");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
    }

    public function isReady($trasition, OrderBO $bo)
    {
        return ($trasition->fromStateName() == $bo->orderStatus) ? true : false;
    }

}

class CustomClearence extends Transition
{

    public function apply($trasition, OrderBO $bo, $updateBo)
    {
        return $this->_apply($trasition, $bo, $updateBo);
    }

    private function _apply($trasition, OrderBO $bo, $updateBo)
    {
        $this->validate($bo, $updateBo);
        if (!SaveMileStone::save($bo, $updateBo)) {
            Log::info("Failed to update milestone");
            throw new ApplicationException([], ["Failed to update milestone"]);
        }
        $bo->orderStatus = $trasition->toStateName();
        return $bo;
    }

    public function validate(OrderBO $bo, $updateBo)
    {
        $role = JWTAuth::parseToken()->getPayload()->get('role');
        if (strtolower($role) != 'seller') {
            Log::info("Buyer tried to update the status");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
    }

    public function isReady($trasition, OrderBO $bo)
    {
        return ($trasition->fromStateName() == $bo->orderStatus) ? true : false;
    }

}

class IssueBLDraft extends Transition
{

    public function apply($trasition, OrderBO $bo, $updateBo)
    {
        return $this->_apply($trasition, $bo, $updateBo);
    }

    private function _apply($trasition, OrderBO $bo, $updateBo)
    {
        $this->validate($bo, $updateBo);
        if (!SaveMileStone::save($bo, $updateBo, "BL_DRAFT_DOC")) {
            Log::info("Failed to update milestone");
            throw new ApplicationException([], ["Failed to update milestone"]);
        }
        $bo->orderStatus = $trasition->toStateName();
        return $bo;
    }

    public function validate(OrderBO $bo, $updateBo)
    {
        $role = JWTAuth::parseToken()->getPayload()->get('role');
        if (strtolower($role) != 'seller') {
            Log::info("Buyer tried to update the status");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
        if (!$updateBo->documentId) {
            Log::info("Bl Draft doc is not provided");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
    }

    public function isReady($trasition, OrderBO $bo)
    {
        return ($trasition->fromStateName() == $bo->orderStatus) ? true : false;
    }

}

class ConfirmBLDraft extends Transition
{

    public function apply($trasition, OrderBO $bo, $updateBo)
    {
        return $this->_apply($trasition, $bo, $updateBo);
    }

    private function _apply($trasition, OrderBO $bo, $updateBo)
    {
        $this->validate($bo, $updateBo);
        if (!SaveMileStone::save($bo, $updateBo)) {
            Log::info("Failed to update milestone");
            throw new ApplicationException([], ["Failed to update milestone"]);
        }
        $bo->orderStatus = $trasition->toStateName();
        return $bo;
    }

    public function validate(OrderBO $bo, $updateBo)
    {
        $role = JWTAuth::parseToken()->getPayload()->get('role');
        if (strtolower($role) != 'buyer') {
            Log::info("Seller tried to update the status");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
    }

    public function isReady($trasition, OrderBO $bo)
    {
        return ($trasition->fromStateName() == $bo->orderStatus) ? true : false;
    }

}

class AmmendBlDraft extends Transition
{

    public function apply($trasition, OrderBO $bo, $updateBo)
    {
        return $this->_apply($trasition, $bo, $updateBo);
    }

    private function _apply($trasition, OrderBO $bo, $updateBo)
    {
        $this->validate($bo, $updateBo);
        if (!SaveMileStone::save($bo, $updateBo, 'AMMENED_DRAFT_BL')) {
            Log::info("Failed to update milestone");
            throw new ApplicationException([], ["Failed to update milestone"]);
        }
        $bo->orderStatus = $trasition->toStateName();
        return $bo;
    }

    public function validate(OrderBO $bo, $updateBo)
    {
        $role = JWTAuth::parseToken()->getPayload()->get('role');
        if (strtolower($role) != 'buyer') {
            Log::info("Buyer tried to update the status");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
        if (!$updateBo->documentId) {
            Log::info("Ammned Bl Draft is not provided");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
    }

    public function isReady($trasition, OrderBO $bo)
    {
        return ($trasition->fromStateName() == $bo->orderStatus) ? true : false;
    }

}

class AmmendedBl extends Transition
{

    public function apply($trasition, OrderBO $bo, $updateBo)
    {
        return $this->_apply($trasition, $bo, $updateBo);
    }

    private function _apply($trasition, OrderBO $bo, $updateBo)
    {
        $this->validate($bo, $updateBo);
        if (!SaveMileStone::save($bo, $updateBo, 'FINAL_DRAFT_BL')) {
            Log::info("Failed to update milestone");
            throw new ApplicationException([], ["Failed to update milestone"]);
        }
        $bo->orderStatus = $trasition->toStateName();
        return $bo;
    }

    public function validate(OrderBO $bo, $updateBo)
    {
        $role = JWTAuth::parseToken()->getPayload()->get('role');
        if (strtolower($role) != 'seller') {
            Log::info("Buyer tried to update the status");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
        if (!$updateBo->documentId) {
            Log::info("Ammneded Bl Draft is not provided");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
    }

    public function isReady($trasition, OrderBO $bo)
    {
        return ($trasition->fromStateName() == $bo->orderStatus) ? true : false;
    }

}

class IssueOBL extends Transition
{

    public function apply($trasition, OrderBO $bo, $updateBo)
    {
        return $this->_apply($trasition, $bo, $updateBo);
    }

    private function _apply($trasition, OrderBO $bo, $updateBo)
    {
        $this->validate($bo, $updateBo);
        if (!SaveMileStone::save($bo, $updateBo, "OBL_DOC")) {
            Log::info("Failed to update milestone");
            throw new ApplicationException([], ["Failed to update milestone"]);
        }
        $bo->orderStatus = $trasition->toStateName();
        return $bo;
    }

    public function validate(OrderBO $bo, $updateBo)
    {
        $role = JWTAuth::parseToken()->getPayload()->get('role');
        if (strtolower($role) != 'seller') {
            Log::info("Buyer tried to update the status");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
        if (!$updateBo->documentId) {
            Log::info("Buyer tried to update the status");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
    }

    public function isReady($trasition, OrderBO $bo)
    {
        return ($trasition->fromStateName() == $bo->orderStatus) ? true : false;
    }

}

class OnboardGoods extends Transition
{

    public function apply($trasition, OrderBO $bo, $updateBo)
    {
        return $this->_apply($trasition, $bo, $updateBo);
    }

    private function _apply($trasition, OrderBO $bo, $updateBo)
    {
        $this->validate($bo, $updateBo);
        if (!SaveMileStone::save($bo, $updateBo)) {
            Log::info("Failed to update milestone");
            throw new ApplicationException([], ["Failed to update milestone"]);
        }
        $bo->orderStatus = $trasition->toStateName();
        return $bo;
    }

    public function validate(OrderBO $bo, $updateBo)
    {
        $role = JWTAuth::parseToken()->getPayload()->get('role');
        if (strtolower($role) != 'seller') {
            Log::info("Buyer tried to update the status");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
    }

    public function isReady($trasition, OrderBO $bo)
    {
        return ($trasition->fromStateName() == $bo->orderStatus) ? true : false;
    }

}

class GoodsDelivered extends Transition
{

    public function apply($trasition, OrderBO $bo, $updateBo)
    {
        return $this->_apply($trasition, $bo, $updateBo);
    }

    private function _apply($trasition, OrderBO $bo, $updateBo)
    {
        $this->validate($bo, $updateBo);
        if (!SaveMileStone::save($bo, $updateBo)) {
            Log::info("Failed to update milestone");
            throw new ApplicationException([], ["Failed to update milestone"]);
        }
        $bo->orderStatus = $trasition->toStateName();
        return $bo;
    }

    public function validate(OrderBO $bo, $updateBo)
    {
        $role = JWTAuth::parseToken()->getPayload()->get('role');
        if (strtolower($role) != 'seller') {
            Log::info("Buyer tried to update the status");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
    }

    public function isReady($trasition, OrderBO $bo)
    {
        return ($trasition->fromStateName() == $bo->orderStatus) ? true : false;
    }

}

class SellerConfirmsDelivery extends Transition
{

    public function apply($trasition, OrderBO $bo, $updateBo)
    {
        return $this->_apply($trasition, $bo, $updateBo);
    }

    private function _apply($trasition, OrderBO $bo, $updateBo)
    {
        $this->validate($bo, $updateBo);
        if (!SaveMileStone::save($bo, $updateBo)) {
            Log::info("Failed to update milestone");
            throw new ApplicationException([], ["Failed to update milestone"]);
        }
        $bo->orderStatus = $trasition->toStateName();
        return $bo;
    }

    public function validate(OrderBO $bo, $updateBo)
    {
        $role = JWTAuth::parseToken()->getPayload()->get('role');
        if (strtolower($role) != 'seller') {
            Log::info("Buyer tried to update the status");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
    }

    public function isReady($trasition, OrderBO $bo)
    {
        return ($trasition->fromStateName() == $bo->orderStatus) ? true : false;
    }

}

class ConfirmDelivery extends Transition
{

    public function apply($trasition, OrderBO $bo, $updateBo)
    {
        return $this->_apply($trasition, $bo, $updateBo);
    }

    private function _apply($trasition, OrderBO $bo, $updateBo)
    {
        $this->validate($bo, $updateBo);
        if (!SaveMileStone::save($bo, $updateBo)) {
            Log::info("Failed to update milestone");
            throw new ApplicationException([], ["Failed to update milestone"]);
        }
        $bo->orderStatus = $trasition->toStateName();
        return $bo;
    }

    public function validate(OrderBO $bo, $updateBo)
    {
        $role = JWTAuth::parseToken()->getPayload()->get('role');
        if (strtolower($role) != 'buyer') {
            Log::info("Seller tried to update the status");
            throw new ApplicationException([], ["Can't perform the action"]);
        }
    }

    public function isReady($trasition, OrderBO $bo)
    {
        return ($trasition->fromStateName() == $bo->orderStatus) ? true : false;
    }

}

class SaveMileStone
{

    public static function save($bo, $updateBo, $docType = "")
    {

        $docId = 0;
        if ($updateBo->documentId) {
            $docDetails = self::saveOrderDocDetails($bo, $updateBo, $docType);
            $docId = $docDetails->id;
        }
        $orderMileStoneDetails = new OrderMilestone();
        $orderMileStoneDetails->order_id = $bo->orderId;
        $orderMileStoneDetails->milestone = $updateBo->transition;
        $orderMileStoneDetails->document_id = $docId;
        $orderMileStoneDetails->comments = $updateBo->comments;
        $orderMileStoneDetails->additional_details = json_encode($updateBo);
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $orderMileStoneDetails->created_by = $userId;
        $orderMileStoneDetails->updated_by = $userId;
        $orderMileStoneDetails->save();
        if (!$orderMileStoneDetails->id) {
            Log::info("Failed to save milestone");
            throw new ApplicationException([], ["Failed to save milestone"]);
        }
        return True;

    }

    public static function saveOrderDocDetails($bo, $updateBo, $docType)
    {

        $ordersDocModelObj = new OrderDoc();
        $ordersDocModelObj->order_id = $bo->orderId;
        $ordersDocModelObj->order_no = $bo->orderNo;
        $ordersDocModelObj->document_id = $updateBo->documentId;
        $ordersDocModelObj->document_name = $updateBo->documentName;
        $ordersDocModelObj->document_type = $docType;
        $ordersDocModelObj->created_by = JWTAuth::parseToken()->getPayload()->get('id');
        $ordersDocModelObj->save();
        if (!$ordersDocModelObj->id) {
            Log::info("Failed to save doc details");
            throw new ApplicationException([], ["Failed to save doc details"]);
        }
        return $ordersDocModelObj;

    }

}
