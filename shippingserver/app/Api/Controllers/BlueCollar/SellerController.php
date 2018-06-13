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
use Api\Services\BlueCollar\SellerService;
use Api\Requests\BlueCollar\SellerPostRequest;
use Api\Requests\BlueCollar\SellerPostMasterRequest;
use Api\Requests\BlueCollar\SellerNegotiationRequest;

class SellerController extends BaseController
{
    public function post(SellerPostRequest $request){
      $response = SellerService::post($request);
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }

    public function postList(SellerPostMasterRequest $request){
      $response = SellerService::postList($request);
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }
    
    public function boundCount(){
      $response = SellerService::boundCount();
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }
    
    public function inboundList(SellerPostMasterRequest $request){
      $response = SellerService::inboundList($request);
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }

    public function outboundList(SellerPostMasterRequest $request){
      $response = SellerService::outboundList($request);
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }

    public function quoteAction(SellerNegotiationRequest $request){
      switch ($request->action) {
        case 'INITIALISING':
          $response = SellerService::sellerQuoteInitialising($request);
          break;
        case 'OFFER':
          $response = SellerService::sellerQuoteOffer($request);
          break;
        case 'COUNTER':
          $response = SellerService::sellerQuoteCounter($request);
          break;
        case 'ACCEPT':
          $response = SellerService::sellerQuoteAccept($request);
          break;
        case 'DENY':
          $response = SellerService::sellerQuoteDeny($request);
          break;
      }
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }
}
