<?php

namespace ApiV2\Controllers;
use DB;
use App\Exceptions\ApplicationException;
use Illuminate\Http\Request;
use Log;
use Response;
use ApiV2\Model\IntraHyperSellerPost;
use ApiV2\Model\CartItem;
use ApiV2\Model\OrderItem;
use ApiV2\Model\Order;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Model\IntraHyperOrder;
use ApiV2\Modules\Intracity\IntracityPostSearch;
use Exception;
use App\Solr;
use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use ApiV2\Services\LogistiksCommonServices\OrderServices;
use ApiV2\Model\PaymentLog;
use Illuminate\Http\RedirectResponse;

class PaymentResponseController extends BaseController
{

    
    public function GetwayResponse()
    {
         
         $reponse=serialize($_REQUEST);
         $ResponseMessage=$_REQUEST['ResponseMessage'];
         $TransactionID=$_REQUEST['TransactionID'];
         $ResponseCode=$_REQUEST['ResponseCode'];
         $orderid=$_REQUEST['MerchantRefNo'];
         $PaymentLog=PaymentLog::where('order_payment_id', $orderid)
                     ->update(['response'=>$reponse,'verified_status'=>$ResponseMessage,"order_status"=>$ResponseMessage,"transaction_id"=>$TransactionID]);

         if($ResponseCode==0)
         {
            $url=PAYMENT_SUCCESS."$orderid";
            redirect()->to($url)->send();
         }else{
             
            $url=PAYMENT_FAILED;
            redirect()->to($url)->send();
         }

         
    }

    
   
   
}