<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace ApiV2\Controllers\BlueCollar;


use ApiV2\Controllers\BaseController;
use App\Exceptions\ApplicationException;
use Illuminate\Http\Request;
use Log;
use Response;
use Exception;
use Storage;
use DB;
use ApiV2\Services\BlueCollar\BuyerService;
use ApiV2\Requests\BlueCollar\BuyerPostRequest;
use ApiV2\Requests\BlueCollar\BuyerPostMasterRequest;
use ApiV2\Requests\BlueCollar\BuyerNegotiationRequest;

class BuyerController extends BaseController
{
    public function post(Request $request,$status){
      $response = BuyerService::post($request,$status);
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }

    public function postList(BuyerPostMasterRequest $request){
      $response = BuyerService::postList($request);
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }

    public function boundCount(){
      $response = BuyerService::boundCount();
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }

    public function inboundList(BuyerPostMasterRequest $request){
      $response = BuyerService::inboundList($request);
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }

    public function outboundList(BuyerPostMasterRequest $request){
      $response = BuyerService::outboundList($request);
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }

    public function quoteAction(BuyerNegotiationRequest $request){
      if($request->action=='OFFER'){
        $response = BuyerService::quoteSubmit($request);
      }else if($request->action=='ACCEPT'){
        $response = BuyerService::quoteAccept($request);
      }else{
        $response = BuyerService::quoteDeny($request);
      }
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }

    public function sellerSearch(Request $request){
       $response = BuyerService::sellerSearch($request);
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }
}
