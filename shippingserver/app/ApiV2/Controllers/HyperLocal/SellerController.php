<?php
namespace ApiV2\Controllers\HyperLocal;
use ApiV2\Services\UserSettingsService;
use ApiV2\Controllers\BaseController;
use Exception;
use Illuminate\Http\Request;
use Log;
use Response;
use DB;
use ApiV2\Services\HyperLocal\SellerRateCardService;
use ApiV2\Controllers\HyperLocal\BuyerPost;
use ApiV2\Modules\Intracity\IntracityPostSearch;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;

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
        try {
            $response = SellerRateCardService::sellerRateCardPost($request);
            return Response::json($response);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function sellerRateCardDraftsPost(Request $request)
    {       
        try {
            $response = SellerRateCardService::sellerRateCardDraftsPost($request);
            return Response::json($response);
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

    public function getHpSellerPostDetails(Request $request)
    {
      try {
          return SellerRateCardService::getHpSellerPostDetails($request);
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

    public function getSellerPostLeadDetail(Request $request)
    {
       try{
            $post = DB::table('intra_hp_sellerpost_ratecart')
                   ->where('id','=',$request->id)
                   ->get();
            return response()->json([
                'isSuccessful' => true,
                'data' => $post
            ], 200);

        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }
    
    public function postSellerLeadsList(Request $request)
    {
      $city_id = DB::table('intra_hp_sellerpost_ratecart')
                    ->where('id','=',$request->ids)
                    ->select('city_id')
                    ->get();
       
      $leads = DB::table('intra_hp_buyer_posts')
               ->join('intra_hp_buyer_seller_routes as item','item.fk_buyer_seller_post_id','=','intra_hp_buyer_posts.id')
               ->leftjoin('users as t_users','t_users.id','=','intra_hp_buyer_posts.posted_by')
               ->leftjoin('intra_hp_post_quotations as quote','item.id','=','quote.route_id')
               ->leftjoin('intracity_term_contracts as term','term.term_buyer_quote_id','=','quote.post_id')
               ->where('intra_hp_buyer_posts.lkp_service_id',_HYPERLOCAL_)
               ->where('item.city_id', $city_id[0]->city_id)
               ->where('item.is_seller_buyer', 1)
               ->where(function($query) use($request) {
                     if(isset($request->buyerType) && !empty($request->buyerType)) {
                       $query->whereIn('intra_hp_buyer_posts.posted_by',$request->buyerType);
                    }
                })




                ->select('intra_hp_buyer_posts.lkp_service_id', 
                  'intra_hp_buyer_posts.title as title',
                  'intra_hp_buyer_posts.id as postid',
                  'intra_hp_buyer_posts.posted_by as posted_by',
                  't_users.username as posted_username',
                  'intra_hp_buyer_posts.post_transaction_id',
                  'intra_hp_buyer_posts.is_private_public as isPublic',
                  'intra_hp_buyer_posts.category',
                  'intra_hp_buyer_posts.servicetype',
                  'intra_hp_buyer_posts.last_date',
                  'intra_hp_buyer_posts.last_time',
                  'intra_hp_buyer_posts.lead_type',
                  'term.contract_quantity',
                  'term.contract_price',
                  'item.weight',
                  'item.max_no_parcel',
                  'item.price_type as price_type',
                  'item.*','quote.*',
                  'item.id as item_id',
                  'quote.id as quote_id',  
                  'quote.status as status', 
                  'item.fk_buyer_seller_post_id',
                   DB::raw("(select city_name FROM lkp_cities WHERE id = item.city_id ) as city_name") ,

                    DB::raw("TIMESTAMPDIFF(MINUTE,now(),concat(intra_hp_buyer_posts.last_date,' ', intra_hp_buyer_posts.last_time)) AS exptime")


                 )
                ->get();

        $data = array();
       // return $leads;
        if(!empty($leads)){
           $data['data'] = $leads;
           $data['success'] = true;
           $data['status'] = 200;
        }else{
           $data['data'] = $leads;
           $data['success'] = false;
           $data['status'] = 400;
        }
        return $data;
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