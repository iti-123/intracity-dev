<?php

namespace ApiV2\Controllers;

use ApiV2\Model\IntraHyperBuyerPost;
use ApiV2\Model\IntraHyperBuyerPostTerm;
use ApiV2\Services\LogistiksCommonServices\DocumentServices;
use ApiV2\Services\LogistiksCommonServices\MessageServices;
use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;
use Exception;
use Illuminate\Http\Request;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use DB;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use ApiV2\Services\SettingsSearchService;

class BuyerController extends BaseController
{

    /* Buyer Post Spots */
    public function buyerSpotsPost(Request $request)
    {
        try {
            return IntraHyperBuyerPost::saveBuyerSpotsPost($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }
    
    public function postLeadsList(Request $request)
    {
        try {
            return IntraHyperBuyerPost::postLeadsList($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function getIntraSlab(Request $request)
    {
        try {
            return IntraHyperBuyerPost::getIntraSlab($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function buyerTermPost(Request $request)
    {
        try {
            return IntraHyperBuyerPostTerm::saveBuyerTermPost($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }


    public function buyerSearch(Request $request)
    {
        try {
            return IntraHyperBuyerPostTerm::searchBuyer($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function getRecordsDetails(Request $request)
    {
        try {
            return IntraHyperBuyerPost::allrecords($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }
    /* Count Buyer Post Spots*/
    public function countBuyerPostSpots(Request $request)
    {
        try {
            return IntraHyperBuyerPost::countbuyerpost($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    /* For Listing */
    public function buyerlist(Request $request)
    {
        try {
            return IntraHyperBuyerPost::buyerlist($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }


    /* For Records */
    public function records(Request $request)
    {
        $payload = '';
        switch($request->isInbound) {
            case 'inbound':
                return SettingsSearchService::filterInbound($request);
                break;
            case 'outbound':
                return self::filterOutbound($request);
                break;
            default: 
                return response()->json([
                    "isSuccessfull" => false,
                    "payload" => "data not available"
                ]);
            break;
        }
    }

    public static function filterOutbound($request) {
        
        $type = $request->type; 
        $vehicleType = $request->vehicleType; 
        $sellerType = $request->sellerType; 
        $postType = $request->postType; 
        $location = $request->location;
        $fromLocation = $request->fromLocation; 
        $toLocation = $request->toLocation; 

        $fromDate = $request->fromDate; 
        $toDate = $request->toDate; 
        $loc = $request->loc; 
        $pageLoader = $request->pageLoader; 
        $userid = JWTAuth::parseToken()->getPayload()->get('id');
        try {
            $data = DB::table('intra_hp_buyer_posts as bp')
                    ->join('intra_hp_buyer_seller_routes as sr','sr.fk_buyer_seller_post_id','=','bp.id')
                    ->where('bp.lkp_service_id','=',_INTRACITY_)
                    ->where('sr.is_seller_buyer','=',BUYER)
                    ->where('bp.is_active', '=', '1')
                    ->where('bp.posted_by', '=', $userid)
                    ->where(function ($query) use ($type) {
                      if ($type === 'all') {
                          $query->whereIn('bp.is_private_public', [1,0]);
                      }
                      if ($type === 'term') {
                          $query->where('bp.lead_type','=',2);
                      }
                      if ($type === 'spot') {
                          $query->where('bp.lead_type','=',1);
                      }
                      if ($type === 'private') {
                          $query->where('bp.is_private_public', '=',1);
                      }
                      if ($type === 'public') {
                          $query->where('bp.is_private_public','=',0);
                      }
                  })
                  ->where(function($query) use($loc) {
                     if(isset($loc) && !empty($loc)) {
                        $query->where('sr.city_id','=',$loc);
                    }
                  })
                  ->where(function($query) use($fromLocation) {
                     if(isset($fromLocation) && !empty($fromLocation)) {
                       foreach($fromLocation as $key => $val){
                          if($key == 0){
                            $query->where('sr.from_location','=',$val);
                          }else{
                            $query->orWhere('sr.from_location','=',$val);
                          }
                       }
                    }
                  })
                  ->where(function($query) use($toLocation) {
                     if(isset($toLocation) && !empty($toLocation)) {
                       foreach($toLocation as $key => $val){
                          if($key == 0){
                            $query->where('sr.to_location','=',$val);
                          }else{
                            $query->orWhere('sr.to_location','=',$val);
                          }
                       }
                    }
                  })
                  ->where(function($query) use($vehicleType) {
                     if(isset($vehicleType) && !empty($vehicleType)) {
                       foreach($vehicleType as $key => $val){
                          if($key == 0){
                            $query->where('sr.vehicle_type_id','=',$val);
                          }else{
                            $query->orWhere('sr.vehicle_type_id','=',$val);
                          }
                       }
                    }
                  })
                  ->where(function($query) use($sellerType) {
                     if(isset($sellerType) && !empty($sellerType)) {
                       foreach($sellerType as $key => $val){
                          if($key == 0){
                            $query->where('bp.posted_by','=',$val);
                          }else{
                            $query->orWhere('bp.posted_by','=',$val);
                          }
                       }
                    }
                  })
                  ->where(function($query) use($postType) {
                     if(isset($postType) && !empty($postType)) {
                       foreach($postType as $key => $val){
                          if($key == 0){
                            $query->where('bp.type_basis','=',$val);
                          }else{
                            $query->orWhere('bp.type_basis','=',$val);
                          }
                       }
                    }
                  })
                  ->where(function($query) use($fromDate) {
                     if(isset($fromDate) && !empty($fromDate)) {
                        $from_date = str_replace('/','-',$fromDate);
                        $fromDates = date("Y-m-d", strtotime($from_date)); 
                        $query->where('sr.valid_from','=',$fromDates);
                    }
                  })
                 ->where(function($query) use($toDate) {
                     if(isset($toDate) && !empty($toDate)) {
                        $to_date = str_replace('/','-',$toDate);
                        $toDates = date("Y-m-d", strtotime($to_date)); 
                        $query->where('sr.valid_to','=',$toDates);
                    }
                  })
                  ->paginate($pageLoader);
                  
                  foreach($data as $key => $val){
                    $count = DB::table('intra_hp_buyer_seller_routes')
                            ->where('is_seller_buyer',SELLER)
                            ->where('lkp_service_id',_INTRACITY_)
                            ->where('city_id',$val->city_id)
                            ->count();
                    $data[$key]->countOfLeads = $count;

                    $quoteLength = DB::table('intra_hp_post_quotations')
                            ->where('lkp_service_id',_INTRACITY_)
                            ->where('post_id',$val->id)
                            ->count();
                    $data[$key]->quote = $quoteLength;
                            
                  }  
                return response()->json([
                    'isSuccessful' => true,
                    'data' => EncrptionTokenService::eloqIdEncrypt($data)
                ], 200);

        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    } 

    public function intraPrivateBuyerPost(Request $request)
    {
        try {
            return IntraHyperBuyerPost::intraPrivateBuyerPost($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }
    
 
    public function countInboundRecords(Request $request)
    {
        try {
            return IntraHyperBuyerPost::countInboundRecords($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }    

    /* For Buyer Filters */
    public function buyerfilter(Request $request)
    {
        try {
            return IntraHyperBuyerPost::buyerFilterSearch($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    /* For Buyer Listing According to filters*/
    public function accorindfilter(Request $request)
    {
        try {
            return IntraHyperBuyerPost::filterAccordingSearch($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    /* For Buyer Message Data By Id */
    public function messageById(Request $request)
    {
        try {
            return IntraHyperBuyerPost::getMessageDetails($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    /* For Buyer Discounts */
    public function getDiscount(Request $request)
    {
        try {
            return IntraHyperBuyerPost::getBuyerDiscount();
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }


    public function getBuyerRouteDetail(Request $request)
    {
        try {
            return IntraHyperBuyerPost::getRouteDetails($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }


    public function uploadFiles(Request $request)
    {
       return DocumentServices::storeDoc($request, 'intracity/docs/');
    }

    public function hpUploadFiles(Request $request)
    {
       return DocumentServices::storeDoc($request, 'hyperlocal/docs/');
    }

    public function sendMessage(Request $request)
    {
        try {
            $message = MessageServices::send($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
        return response()->json([
            'payload' => $message,
            'isSuccessful' => true
        ], 200);
    }

    public function retriveFiles($file)
    {
        return DocumentServices::getDoc($file, 'intracity/docs/');
    }

    public function buyerPostDetails(Request $request)
    {
        try {
            return IntraHyperBuyerPost::getbuyerPostDetails($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function leftNavData($id, $roleId){

        try {
            return IntraHyperBuyerPost::getNavData($id, $roleId);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
        

    }

    public function popupMenuData($id, $roleId, $menutype){

        try {
            return IntraHyperBuyerPost::getPopupMenuData($id, $roleId, $menutype);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
        

    }

    public function buyerPostLeadDetails(Request $request)
    {
        //return func_get_args();
        try {
            return IntraHyperBuyerPost::getbuyerPostLeadDetails($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function deleteBuyerPost(Request $request)
    {
        try {
            return IntraHyperBuyerPost::deleteBuyerPost($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function getPostDataById(Request $request, $postId)
    {
        try {
            return IntraHyperBuyerPost::getPostDataById($postId);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function generateTxn()
    {
        dd(NumberGeneratorServices::generateTranscationId(new IntraHyperBuyerPost, 3));
    }


    /** For Notification Settings Data */
    public function settingsData(Request $request)
    {
        try{
            return IntraHyperBuyerPost::getSettingsData($request);
        }   catch (Exception $e){
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }

    }

    private static function getUser() {
        return JWTAuth::parseToken()->getPayload();
    }


}