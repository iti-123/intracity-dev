<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace Api\Controllers\BlueCollar;


use Api\Controllers\BaseController;
use App\Exceptions\ApplicationException;
use Illuminate\Http\Request;
use Log;
use Response;
use Exception;
use Storage;
use DB;
use Api\Services\BlueCollar\BuyerService;
use Api\Requests\BlueCollar\BuyerPostRequest;
use Api\Requests\BlueCollar\BuyerPostMasterRequest;
use Api\Requests\BlueCollar\BuyerNegotiationRequest;

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
        return "asdasd";
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
