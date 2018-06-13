<?php
namespace Api\Controllers\HyperLocal;
use Api\Services\UserSettingsService;
use Api\Controllers\BaseController;
use Exception;
use Illuminate\Http\Request;
use Log;
use Response;
use DB;
use Api\Services\HyperLocal\SellerRateCardService;
use Api\Controllers\HyperLocal\BuyerPost;
use Api\Modules\Intracity\IntracityPostSearch;

class SellerController extends BaseController
{

    public function productCategory(Request $request)
    {

        try {
            $response = SellerRateCardService::productCategory($request);
            return Response::json($response);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function sellerRateCardPost(Request $request)
    {

        $response = SellerRateCardService::sellerRateCardPost($request);
        return Response::json($response);
        try {
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function sellerSearchList(Request $request)
    {
        // return $request;
        try {
            return SellerRateCardService::sellersearchlist($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }

    }

    public function sellerPostList(Request $request)
    {
        
        try {
            $response = SellerRateCardService::sellerPostList($request);
            return $response;
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }

    }

    public function sellerListCounts(Request $request)
    {
        try {
            $response = SellerRateCardService::sellerListCounts($request);
            return $response;
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function searchAccdngFilters(Request $request)
    {
           
        try {
             if($request->type['bound']=='Inbound')
             {
                $obj= new BuyerPost;
               $response = $obj->getRecordsInbound($request);
             }else{
                $response = SellerRateCardService::searchAccdngFilters($request);
             }
              
            return $response;
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function sellerSearchResult(Request $request)
    {
        try {
            $response = SellerRateCardService::sellerSearchResult($request);
            return $response;
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }
    public function hpBuyerPostSearch(Request $request) 
    {
      // try{
      //   $response = SellerRateCardService::sellerSearchResult($request);
      //   return $response;
      // }catch(Exception $e) {
      //   LOG::error($e->getMessage());
      //   return $this->errorResponse($e);
      // }

      $data = json_decode($request->getContent());

      $searchService = new IntracityPostSearch();
      $result = $searchService->sellerSearch($data);
      return $result;
    }

    public function getPostDetails(Request $request)
    {
        try {
            $response = SellerRateCardService::getPostDetails($request);
            return $response;
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }
    


}