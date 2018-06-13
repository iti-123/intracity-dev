<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 05-07-2017
 * Time: 19:37
 */

namespace ApiV2\Controllers\Hyperlocal;
use ApiV2\Model\IntraHyperRoute;
use ApiV2\Model\IntraHyperQuotaion;
use DB;
use ApiV2\Services\UserSettingsService;
use ApiV2\Controllers\BaseController;
use ApiV2\Model\HyperLocal\M_Buyer_post;
use ApiV2\Model\IntraHyperBuyerPost;
use ApiV2\Model\TermContract; 
use ApiV2\Requests\Hyperlocal\BuyerPostRequest;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use ApiV2\Controllers\UserServices;
use ApiV2\Controllers\AbstractUserServices;
use ApiV2\Services\LogistiksCommonServices\DocumentServices;
use ApiV2\Model\OrderItem;
use ApiV2\Model\Order;

use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;

//use ApiV2\Services\BlueCollar\SellerRegistrationService;

class BuyerPost extends BaseController
{
    private static $rows_fetched = 10;

    public function datapost(BuyerPostRequest $request)
    {

        //return M_Buyer_post::insertdata($request);
        try {

            return M_Buyer_post::insertdata($request);

        } catch (Exception $e) {
            //LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }

    }

    public function datapostDrafts(BuyerPostRequest $request)
    {
        try {
            return M_Buyer_post::insertDraftsdata($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function postLeadsList(Request $request)
    {
        try {
            return IntraHyperBuyerPost::postHpLeadsList($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function getRecords(Request $request)
    {
        $type = $request->type; 
        $sellerName = $request->sellerName; 
        $category = $request->category; 
        $service = $request->service; 
        $lastDate = $request->lastDate; 
        $pageLoader = $request->pageLoader; 
        $userid = JWTAuth::parseToken()->getPayload()->get('id');
        try {
            $data = IntraHyperBuyerPost::with(['getAllRoute','quotation'])
                    ->where('lkp_service_id', '=', _HYPERLOCAL_)
                    ->where('is_active', '=', '1')
                    ->where('posted_by', '=', $userid)
                    ->where(function ($query) use ($type) {
                      if ($type === 'all') {
                          $query->whereIn('is_private_public', [1,0]);
                      }
                      if ($type === 'term') {
                          $query->where('lead_type','=',2);
                      }
                      if ($type === 'spot') {
                          $query->where('lead_type','=',1);
                      }
                      if ($type === 'private') {
                          $query->where('is_private_public', '=',1);
                      }
                      if ($type === 'public') {
                          $query->where('is_private_public','=',0);
                      }
                  })
                  ->where(function($query) use($sellerName) {
                     if(isset($sellerName) && !empty($sellerName)) {
                       foreach($sellerName as $key => $val){
                          if($key == 0){
                            $query->where('posted_by','=',$val);
                          }else{
                            $query->orWhere('posted_by','=',$val);
                          }
                       }
                    }
                  })
                  ->where(function($query) use($category) {
                     if(isset($category) && !empty($category)) {
                       foreach($category as $key => $val){
                          if($key == 0){
                            $query->where('category','=',$val);
                          }else{
                            $query->orWhere('category','=',$val);
                          }
                       }
                    }
                  })
                  ->where(function($query) use($service) {
                     if(isset($service) && !empty($service)) {
                       foreach($service as $key => $val){
                          if($key == 0){
                            $query->where('servicetype','=',$val);
                          }else{
                            $query->orWhere('servicetype','=',$val);
                          }
                       }
                    }
                  })
                  ->where(function($query) use($lastDate) {
                     if(isset($lastDate) && !empty($lastDate)) {
                        $last_date = str_replace('/','-',$lastDate);
                        $lastDates = date("Y-m-d", strtotime($last_date)); 
                        $query->where('last_date','=',$lastDates);
                    }
                  })
                  ->paginate($pageLoader);

                  foreach($data as $key => $val){
                    $count = DB::table('intra_hp_sellerpost_ratecart')
                            ->where('lkp_service_id',_HYPERLOCAL_)
                            ->where('city_id',$val->getAllRoute[0]->city_id)
                            ->count();
                    $data[$key]->countOfLeads = $count;        
                  }  
            return response()->json([
                'isSuccessful' => true,
                'data' => EncrptionTokenService::eloqIdEncrypt($data)
            ], 200);

        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function getPostCount(Request $request)
    {

        try {
            return M_Buyer_post::countPost($request);

        } catch (Exception $e) {
            return $this->errorResponse($e);
        }

    }
    /********************seller Inbound *********************/
    public function getRecordsInbound(Request $request)
    {      
        $userid = JWTAuth::parseToken()->getPayload()->get('id');
        //user information 
        $obj=new UserServices;
        $userdetails=$obj->getUserDetailsById($userid);
        $usercityid=$userdetails->lkp_city_id;
        $userlocationid=$userdetails->lkp_location_id;
        $userpincoe=$userdetails->pincode;
         //user setting information


        $setting = UserSettingsService::getUserSettings(_HYPERLOCAL_, $context='', $userid); 
        // return $setting;
        $post=self::AssignPost($userid);
        // return sizeof($post);
        $inboundData=array();
        if(isset($setting) && !empty($setting)) {
            
            if($setting['seller_spot_enquiries_related'])
            {
              
               $spot = IntraHyperBuyerPost::with(['getAllRoute' => function ($query) use ($request,$userid) {
                    $query->where('is_active', '=', 1);
                }])
                    ->where('lkp_service_id', '=', _HYPERLOCAL_)
                    ->where('lead_type', '=', 1)
                    // ->whereIn('id', $post)
                    ->where(function($query) use($request){                        
                        self::applyFilter($query,$request);
                    })
                    ->get();

               $term = IntraHyperBuyerPost::with(['getAllRoute' => function ($query) use ($request,$userid) {
                    $query->where('is_active', '=', 1);
                }])
                    ->where('lkp_service_id', '=', _HYPERLOCAL_)
                    ->where('lead_type', '=', 2)
                    // ->whereIn('id', $post)
                    ->where(function($query) use($request){                        
                        self::applyFilter($query,$request);
                    })
                    ->get();
                   $inboundData[0]['key']='Related-Spot-Lead';
                   $inboundData[0]['index']=0;
                   $inboundData[0]['data']=$spot;
                   $inboundData[1]['key']='Related-Term-Lead';
                   $inboundData[1]['index']=1;
                   $inboundData[1]['data']=$term;
            }

            if($setting['seller_spot_lead_partly_related'])
            {
               if($usercityid!='')
                { 
                       $spot = IntraHyperBuyerPost::with(['getAllRoute' => function ($query) use ($request,$userid,$usercityid) {
                            $query->where('is_active', '=', 1);
                            $query->where('city_id', '=', $usercityid);
                        }])
                            ->where('lkp_service_id', '=', _HYPERLOCAL_)
                            ->where('lead_type', '=', 1)
                            ->where(function($query) use($request){                        
                        self::applyFilter($query,$request);

                           })
                            ->get();
                       $term = IntraHyperBuyerPost::with(['getAllRoute' => function ($query) use ($request,$userid,$usercityid) {
                            $query->where('is_active', '=', 1);
                            $query->where('city_id', '=', $usercityid);
                        }])
                            ->where('lkp_service_id', '=', _HYPERLOCAL_)
                            ->where('lead_type', '=', 2)
                                   ->where(function($query) use($request){                        
                                self::applyFilter($query,$request);
                             })
                            ->get();
                           $inboundData[2]['key']='Partly-Related-Spot-Lead';
                           $inboundData[2]['data']=$spot;
                           $inboundData[2]['index']=2;
                           $inboundData[3]['key']='Partly-Related-Term-Lead';
                           $inboundData[3]['index']=3;
                           $inboundData[3]['data']=$term;
               }
            }

            if($setting['seller_spot_lead_un_related']=='on')
            {
               /// required condition
               if($usercityid!='')
                { 
                       $spot = IntraHyperBuyerPost::with(['getAllRoute' => function ($query) use ($request,$userid,$usercityid) {
                            $query->where('is_active', '=', 1);
                            $query->where('city_id', '=', $usercityid);
                          
                        }])
                            ->where('lkp_service_id', '=', _HYPERLOCAL_)
                            ->where('lead_type', '=', 1)
                            ->where(function($query) use($request){                        
                         self::applyFilter($query,$request);
                        })
                            ->get();
                       $term = IntraHyperBuyerPost::with(['getAllRoute' => function ($query) use ($request,$userid,$usercityid) {
                             $query->where('is_active', '=', 1);
                             $query->where('city_id', '=', $usercityid);
                            
                        }])
                            ->where('lkp_service_id', '=', _HYPERLOCAL_)
                            ->where('lead_type', '=', 2)
                                ->where(function($query) use($request){                        
                        self::applyFilter($query,$request);
                     })
                            ->get();
                           $inboundData[4]['key']='Unrelated-Related-Spot-Lead';
                           $inboundData[4]['data']=$spot;
                           $inboundData[4]['index']=4;
                           $inboundData[5]['key']='Unrelated-Related-Term-Lead';
                           $inboundData[5]['index']=5;
                           $inboundData[5]['data']=$term;
               }
            }
        } else {
            return response()->json([
                'isSuccessful' => true,
                'data'=>'Settings not found ',
            ], 200);
        }
           
        try {            
            return response()->json([
                'isSuccessful' => true,
                'data'=>$inboundData,
            ], 200);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }

    }
   public function sellerInbondDetails(Request $request)
   {
        
       //return $request;
        $title=$request->title;
        $finalRow = $request->totalRow + $request->offset;  
        try{
        //$title=$request->title['title'];
        $title = str_replace('-', ' ', $title);
        $row=array();
          
         $userid = JWTAuth::parseToken()->getPayload()->get('id');
         $obj=new UserServices;
         $userdetails=$obj->getUserDetailsById($userid);
         $usercityid=$userdetails->lkp_city_id;
         $userlocationid=$userdetails->lkp_location_id;
         $userpincoe=$userdetails->pincode;
         $post=self::AssignPost($userid);
         $inboundData=array();
         $setting = UserSettingsService::getUserSettings(_HYPERLOCAL_, $context='', $userid); 
        $curdate=date('Y-m-d');
        $bedate=date("Y-m-d", strtotime("-6 months"));
            
         switch ($title) {
                case 'Related Spot Lead':
                    $query = self::buyerPostQuery($request, $userdetails);
                        
                          $row=$query->where('lkp_service_id', '=', _HYPERLOCAL_)
                          ->where('lead_type', '=', 1)
                          ->where(function($query) use($request){                        
                              self::applyFilter($query,$request);
                          })
                          ->limit(10 )->get();
                    break;
                case 'Related Term Lead':

               $query = self::buyerPostQuery($request, $userdetails);
                        $row=$query->where('lkp_service_id', '=', _HYPERLOCAL_)
                          ->where('lead_type', '=', 2)
                          ->where(function($query) use($request){                        
                              self::applyFilter($query,$request);
                          })
                         ->limit($finalRow )->get();

                    break;
                case 'Partly Related Spot Lead':
                  // DB::enableQueryLog();
                    $query = self::buyerPostQuery($request, $userdetails);
                          $row=$query->where('lkp_service_id', '=', _HYPERLOCAL_)
                              ->where('lead_type', '=', 1)
                              ->where(function($query) use($request){                        
                              self::applyFilter($query,$request);

                           })
                          ->limit($finalRow )->get();
                      //return DB::getQueryLog();
                    break;
               case 'Partly Related Term Lead':
                   $query = self::buyerPostQuery($request, $userdetails);
                           $row =$query->where('lkp_service_id', '=', _HYPERLOCAL_)
                            ->where('lead_type', '=', 2)
                                   ->where(function($query) use($request){                        
                                self::applyFilter($query,$request);
                             })
                            ->limit($finalRow )->get();
                    break;
              case 'Unrelated Related Spot Lead':
                     $query = self::buyerPostQuery($request, $userdetails);
                            $row=$query->where('lkp_service_id', '=', _HYPERLOCAL_)
                            ->where('lead_type', '=', 1)
                            ->where(function($query) use($request){                        
                         self::applyFilter($query,$request);
                        })
                            ->limit($finalRow )->get();

                    break;
              case 'Unrelated Related Term Lead':
               $query = self::buyerPostQuery($request, $userdetails);

                            $row=$query->where('lkp_service_id', '=', _HYPERLOCAL_)
                            ->where('lead_type', '=', 2)
                                ->where(function($query) use($request){                        
                        self::applyFilter($query,$request);
                     })
                     ->limit($finalRow )->get();
                break;


              default:
              $row;

            }


        return response()->json([
                'isSuccessful' => true,
                'data'=>EncrptionTokenService::eloqIdEncrypt($row),
            ], 200);

      }catch (Exception $e) {
            return $this->errorResponse($e);
        }

   }

   public static function buyerPostQuery($request, $userdetails) {
      $userid = JWTAuth::parseToken()->getPayload()->get('id');
      $query = IntraHyperBuyerPost::with(['getAllRoute' => function ($query) use ($request,$userdetails) {
            $query->where('is_active', '=', '1');
            // $query->where('city_id', '=', $userdetails->lkp_city_id);
        },'getuser','getAllRoute.quote'=>function ($query) use ($userid) {
            $query->where('seller_id', '=', $userid)->where('lkp_service_id', '=', _HYPERLOCAL_);
        }]);
      return $query;
   }
    public function applyFilter($query, $request) {
        return $query->where(function($query) use ($request) {
            if(isset($request->category) && !empty($request->category)){
                $query->whereIn('category', $request->category);
            }

            if(isset($request->service) && !empty($request->service)){
                $query->whereIn('servicetype', $request->service);
            }

            if(isset($request->postStatus) && !empty($request->postStatus)){
                $query->whereIn('post_status', $request->postStatus);
            }
            if(isset($request->date) && !empty($request->date)){
                $query->whereIn('depart_date', $request->date);
            }
            
          if(isset($request->post_type) && !empty($request->post_type)){
                 $type=($request->post_type='private')? 1:0;
                $query->where('is_private_public', $type);
            }

            
        });        
    }
    public function applyFilterbuyer($query, $request) {
        return $query->where(function($query) use ($request) {
            if(isset($request->category) && !empty($request->category)){
                $query->whereIn('product_category', $request->category);
            }

            if(isset($request->service) && !empty($request->service)){
                $query->whereIn('service_type', $request->service);
            }

            if(isset($request->postStatus) && !empty($request->postStatus)){
                $query->whereIn('post_status', $request->postStatus);
            }
            if(isset($request->date) && !empty($request->date)){
                $query->whereIn('created_at', $request->date);
            }
            if(isset($request->is_private_public) && !empty($request->is_private_public)){
                $query->where('is_private_public', $request->is_private_public);
            }
            

         
            
        });        
    }


    
    public function AssignPost($userid)
    {
     $row = DB::table('intra_hp_assigned_seller_buyer')
             ->select(DB::raw("GROUP_CONCAT(buyer_seller_post_id) as `postid`"))
             ->where('buyer_seller_id',$userid)
             ->where('lkp_service_id',_HYPERLOCAL_)
             ->where('is_active',1)
             ->first();
             $postid=$row->postid;
             $post=explode(',',$postid);
             return $post;
     }

    public function getPostDetail(Request $request)
    {
        //DB::enableQueryLog();   
        $id = EncrptionTokenService::idDecrypt($request->id);
        $userid = JWTAuth::parseToken()->getPayload()->get('id');
        try {
            $data = IntraHyperBuyerPost::with(['getAllRoute' => function ($query) use ($request) {
                $query->where('is_active', '=', 1);
            }])
                ->where('lkp_service_id', '=', _HYPERLOCAL_)
                ->where('is_active', '=', '1')
                ->where('id', '=', $id)


                  
                ->get();
            //dd(DB::getQueryLog());
            return response()->json([
                'isSuccessful' => true,
                'data' => $data
            ], 200);

        } catch (Exception $e) {
            return $this->errorResponse($e);
        }

    }

    public function getHpPostDetails(Request $request)
    {
     // return $request->id;
      $id = EncrptionTokenService::idDecrypt($request->id);
      $rs = DB::table('intra_hp_buyer_posts as bp')
            ->join('intra_hp_buyer_seller_routes as sr', 'sr.fk_buyer_seller_post_id', '=','bp.id')
            ->select('bp.*','bp.id as buyerPostId','sr.*','sr.id as routeId',
                  DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=sr.vehicle_type_id) as vehicle"),
                  DB::raw("(select city_name FROM lkp_cities WHERE id=sr.city_id ) as city_name"))
             ->where([
                    ['sr.is_seller_buyer','1'],
                    ['bp.lkp_service_id',_HYPERLOCAL_],
                    ['bp.id',$id],
                    ]);
            
            $data = $rs->get();
            return response()->json([
            'status'=>'success',
            'payload'=>$data
      ]);
    }

    public function getDraftsLocation(Request $request)
    {
      $rs = DB::table('lkp_localities')
            ->select('*')
             ->where('lkp_city_id','=',$request->city_id);
            
           $data = $rs->get();
           return response()->json([
            'status'=>'success',
            'payload'=>$data
      ]);
    }

    public function getPostLeadDetail(Request $request)
    {
        $id = EncrptionTokenService::idDecrypt($request->id);

        $post_id = DB::table('intra_hp_buyer_seller_routes')
                   ->where('id','=',$id)
                   ->where('lkp_service_id', '=', _HYPERLOCAL_)
                   ->select('fk_buyer_seller_post_id')
                   ->get();
        
       try {
            $data = IntraHyperBuyerPost::with(['getAllRoute' => function ($query) use ($request) {
                $query->where('is_active', '=', 1);
            }])
                ->where('lkp_service_id', '=', _HYPERLOCAL_)
                ->where('is_active', '=', '1')
                ->where('id', '=', $post_id[0]->fk_buyer_seller_post_id)
                ->get();
            return response()->json([
                'isSuccessful' => true,
                'data' => $data
            ], 200);

        } catch (Exception $e) {
            return $this->errorResponse($e);
        }


    }
    /***********get buyer In bound ****************/
    public function getInboundPost(Request $request)
    {
        try{
         $userid = JWTAuth::parseToken()->getPayload()->get('id');
         $obj=new UserServices;
         $userdetails=$obj->getUserDetailsById($userid);
         $usercityid=$userdetails->lkp_city_id;
         $userlocationid=$userdetails->lkp_location_id;
         $userpincoe=$userdetails->pincode;
         $post=self::AssignPost($userid);
         $inboundData=array();
         $setting = UserSettingsService::getUserSettings(_HYPERLOCAL_, $context='', $userid); 
        $curdate=date('Y-m-d');
        $bedate=date("Y-m-d", strtotime("-6 months"));
                    
            if($setting['seller_spot_lead_related'])
            { 
                $row = DB::table('intra_hp_sellerpost_ratecart')
                    ->select('*',DB::raw("(SELECT username FROM users WHERE id=intra_hp_sellerpost_ratecart.posted_by LIMIT 1) as vendor"))
                    ->where('lkp_service_id',_HYPERLOCAL_)
                    ->where('city_id',$usercityid)
                    ->where(function($query) use($request){                        
                        self::applyFilterbuyer($query,$request);
                    })
                    ->whereBetween('created_at',[$bedate,$curdate])
                    ->where('is_active',1)
                    ->get();
                $inboundData[0]['index']=0;
                $inboundData[0]['key']='Spot Partly Related enquiry';
                $inboundData[0]['data']=EncrptionTokenService::eloqIdEncrypt($row);
            }
           if($setting['seller_spot_enquiries_related'])
           {
                    $row = DB::table('intra_hp_sellerpost_ratecart'
                            )
                          ->select('*',DB::raw("(SELECT username FROM users WHERE id=intra_hp_sellerpost_ratecart.posted_by LIMIT 1) as vendor"
                        ))
                          ->where('lkp_service_id',_HYPERLOCAL_)
                          ->where('city_id',$usercityid)
                          ->where(function($query) use($request){                        
                                self::applyFilterbuyer($query,$request);
                            })
                          ->where('is_active',1)
                          ->get();
                    $inboundData[1]['index']=1;
                    $inboundData[1]['key']='spot Unrelated enquiry';
                    $inboundData[1]['data']=$row;
           }
           if($setting['seller_spot_lead_partly_related'])
           {
                   $row = DB::table('intra_hp_sellerpost_ratecart'
                            )
                          ->select('*',DB::raw("(SELECT username FROM users WHERE id=intra_hp_sellerpost_ratecart.posted_by LIMIT 1) as vendor"
                    ))
                          ->where('lkp_service_id',_HYPERLOCAL_)
                          ->where('city_id',$usercityid)
                          ->whereBetween('created_at',[$bedate,$curdate])
                           ->where(function($query) use($request){                        
                                self::applyFilterbuyer($query,$request);
                            })
                          ->where('is_active',1)
                          ->get();
                    $inboundData[2]['index']=2;
                    $inboundData[2]['key']='Spot Partly related lead';
                    $inboundData[2]['data']=EncrptionTokenService::eloqIdEncrypt($row);
                   
           }
           if($setting['seller_spot_lead_un_related'])
           {     
                    $row = DB::table(
                        'intra_hp_sellerpost_ratecart')
                          ->select('*',DB::raw("(SELECT username FROM users WHERE id=intra_hp_sellerpost_ratecart.posted_by LIMIT 1) as vendor"
                        
                        ))
                          ->where('lkp_service_id',_HYPERLOCAL_)
                          ->where('city_id',$usercityid)
                           ->where(function($query) use($request){                        
                                self::applyFilterbuyer($query,$request);
                            })
                          ->where('is_active',1)
                          ->get();
                    $inboundData[3]['index']=3;
                    $inboundData[3]['key']='Spot Unrelated leads';
                    $inboundData[3]['data']=EncrptionTokenService::eloqIdEncrypt($row);
           }
        
         return response()->json([
                'isSuccessful' => true,
                'data'=>$inboundData,
            ], 200);
             

        }catch(Exception $e){
         return $this->errorResponse($e);   
        }
    }
     /***********get buyer In bound details****************/
     public function inboundPostDetail(Request $request)
     {
        $title=$request->title;
        $finalRow = $request->totalRow + $request->offset;  
        try{
        //$title=$request->title['title'];
        $title = str_replace('-', ' ', $title);
        $row=array();
          
         $userid = JWTAuth::parseToken()->getPayload()->get('id');
         $obj=new UserServices;
         $userdetails=$obj->getUserDetailsById($userid);
         $usercityid=$userdetails->lkp_city_id;
         $userlocationid=$userdetails->lkp_location_id;
         $userpincoe=$userdetails->pincode;
         $post=self::AssignPost($userid);
         $inboundData=array();
         $setting = UserSettingsService::getUserSettings(_HYPERLOCAL_, $context='', $userid); 
        $curdate=date('Y-m-d');
        $bedate=date("Y-m-d", strtotime("-6 months"));

          switch ($title) {
          case 'Spot Partly Related enquiry':

                 $row = DB::table('intra_hp_sellerpost_ratecart')
                          ->select('*',DB::raw("(SELECT username FROM users WHERE id=intra_hp_sellerpost_ratecart.posted_by LIMIT 1) as vendor"))
                          ->where('lkp_service_id',_HYPERLOCAL_)
                          ->where('city_id',$usercityid)
                          ->whereBetween('created_at',[$bedate,$curdate])
                           ->where(function($query) use($request){                        
                                self::applyFilterbuyer($query,$request);
                            })
                          ->where('is_active',1)
                          ->get();
              break;
          case 'spot Unrelated enquiry':
              
                $row = DB::table('intra_hp_sellerpost_ratecart')
                          ->select('*',DB::raw("(SELECT username FROM users WHERE id=intra_hp_sellerpost_ratecart.posted_by LIMIT 1) as vendor"
                        ))
                          ->where('lkp_service_id',_HYPERLOCAL_)
                          ->where('city_id',$usercityid)
                          ->where('is_active',1)
                            ->where(function($query) use($request){                        
                                self::applyFilterbuyer($query,$request);
                            })
                            ->limit($finalRow)->get();
                        
              break;
                case 'Spot Partly related lead':

                $row = DB::table('intra_hp_sellerpost_ratecart',
                            DB::raw("(SELECT username FROM users WHERE id=intra_hp_sellerpost_ratecart.posted_by LIMIT 1) as vendor"
                    ))
                          ->select('*')
                          ->where('lkp_service_id',_HYPERLOCAL_)
                          ->where('city_id',$usercityid)
                           ->where(function($query) use($request){                        
                                self::applyFilterbuyer($query,$request);
                            })
                          ->whereBetween('created_at',[$bedate,$curdate])
                          ->where('is_active',1)
                          ->get();
                break;
                case 'Spot Unrelated leads':
                    $row = DB::table(
                        'intra_hp_sellerpost_ratecart')
                          ->select('*',DB::raw("(SELECT username FROM users WHERE id=intra_hp_sellerpost_ratecart.posted_by LIMIT 1) as vendor"
                        
                        ))
                          ->where('lkp_service_id',_HYPERLOCAL_)
                          ->where('city_id',$usercityid)
                           ->where(function($query) use($request){                        
                                self::applyFilterbuyer($query,$request);
                            })
                          ->where('is_active',1)
                          ->get();
              

              break;
          
          default:
              $row;
         }
         return response()->json([
                'isSuccessful' => true,
                'data'=>EncrptionTokenService::eloqIdEncrypt($row),
            ], 200);
          
        }catch(Exception $e){
         return $this->errorResponse($e);   
        }

     }

    /********************buyer post Delete *************/
    public function buyerPostDelete(Request $request)
    {

        try {
            return M_Buyer_post::deletehyperBuyerPost($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /******data search by seller *******************/
    public function sellerSearch(Request $request)
    {

        try {
            return M_Buyer_post::sellerSearch($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

   public function uploadFiles(Request $request)
     {      
         $location = DocumentServices::storeDoc($request, 'hyperlocal/docs/');  
     }


    public function quoteRoute(Request $request)
    {
      
       
        try {  
           
            return  IntraHyperBuyerPost::buyerPostquote($request);
            
           
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

  /*******************save data term contract**************************/
  public function termContract(Request $request)
  {      
      if($request->lkp_service_id == _HYPERLOCAL_){
        $file = DocumentServices::storecontractDoc($request->uploaddocument, 'hyperlocal/docs/'); 
      }else if($request->lkp_service_id == _INTRACITY_){
       $file = DocumentServices::storecontractDoc($request->uploaddocument, 'intracity/docs/'); 
      }
      
       //return  $request->all();
     try{

        $termcontract= new TermContract;
        $termcontract->contract_price = $request->contract_price;
        $termcontract->contract_quantity = $request->contract_quantity;
        $termcontract->contract_title = $request->contract_title;
        $termcontract->seller_id = $request->seller_id;
        $termcontract->term_buyer_quote_id = $request->term_buyer_quote_id;
        $termcontract->term_buyer_quote_item_id = $request->term_buyer_quote_item_id;
        $termcontract->lkp_service_id = $request->lkp_service_id;
        $termcontract->intra_hp_post_quotations_id = $request->intra_hp_post_quotations_id;

        if($file['name']!='')
        {
          $termcontract->file_name_one = $file['name']? $file['name']:'';
          $termcontract->file_path_one = $file['path']? $file['name']:'';
          
        }

        if($request->lkp_service_id == _HYPERLOCAL_){
          $termcontract->contract_no = NumberGeneratorServices::generateTranscationId(new TermContract,_HYPERLOCAL_);
        }else if($request->lkp_service_id == _INTRACITY_){
          $termcontract->contract_no = NumberGeneratorServices::generateTranscationId(new TermContract,_INTRACITY_);
        }
        
        $termcontract->save();
        $IntraHyperQuotaion = IntraHyperQuotaion::where('route_id',$request->route_id)
          ->where('lkp_service_id',$request->lkp_service_id)
          ->update(['contract_status'=>1,'status'=>7]);
        return response()->json([
        'payload' => $termcontract,
        'isSuccessfull' => true]);
       //$termcontract;

     }catch(Exception $e)
     {
       return $this->errorResponse($e);
     }
  }


    public function updateQuoteStatus(Request $request)
      {
           //return $request->contract_price;
            try {  
                 $rowset=IntraHyperQuotaion::find($request->id);
                 $rowset->status=2;
                 $rowset->buyer_quote_price=$request->contract_price;
                 $rowset->buyer_quote_quality=$request->contract_quantity;
                 $rowset->save();
                  return response()->json([
                  'payload' => $rowset,
                  'isSuccessfull' => true
              ]);
               
            } catch (Exception $e) {
                return $this->errorResponse($e);
            }
      }

    public function cancelContract(Request $request)
      {
          try{  
               $rowset = IntraHyperQuotaion::find($request->id);
               $rowset->status = 6;
               $rowset->save();
               return response()->json([
                'payload' => $rowset,
                'isSuccessfull' => true
            ]);  
          }catch (Exception $e) {
              return $this->errorResponse($e);
          }
      }
    public function sellerFinalQuote(Request $request)
    {
       //return $request->all();
        try {  
             $rowset=IntraHyperQuotaion::find($request->id);
             $rowset->status=3;
             $rowset->seller_quote_price=$request->finalquoteprice;
            
             $rowset->save();
              return response()->json([
              'payload' => $rowset,
              'isSuccessfull' => true
          ]);
           
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

  public function sellerContractAccept(Request $request)
    {
       //return $request->all();
        try {  
             $rowset = IntraHyperQuotaion::find($request->id);
             $rowset->status = 4;
             $rowset->contract_status = 2;//contract accepted
             $rowset->save();

              
              $post = IntraHyperQuotaion::where('id','=',$request->id)
                            ->select('post_id','route_id')
                            ->get();

              $postDetails = DB::table('intra_hp_buyer_posts')
                      ->join('intra_hp_buyer_seller_routes as rk','rk.fk_buyer_seller_post_id','=','intra_hp_buyer_posts.id')              
                      ->select('intra_hp_buyer_posts.*','rk.*')
                      ->where('intra_hp_buyer_posts.id',$post[0]->post_id)
                      ->get();
              $seller_firstname = JWTAuth::parseToken()->getPayload()->get('firstname');
              $buyer_firstname = DB::table('users')
                        ->select('username')
                        ->where('id',$postDetails[0]->posted_by)
                        ->get();
 
            
              $order = new Order;

              $order->buyer_id = $postDetails[0]->posted_by;
              $order->seller_id = JWTAuth::parseToken()->getPayload()->get('id');
              $order->lkp_service_id = $request->lkp_service_id;
              $order->buyer_name = $buyer_firstname[0]->username;
              $order->city_id = $postDetails[0]->city_id;
              $order->seller_name = $seller_firstname;
              $order->valid_from = $postDetails[0]->valid_from;
              $order->valid_to = $postDetails[0]->valid_to;
              $order->lead_type = $postDetails[0]->lead_type;
              $order->is_indent = 1;
              $order->status = 0;
          
              switch($postDetails[0]->lkp_service_id) {
                case _INTRACITY_:
                    $order->order_no = NumberGeneratorServices::generateTranscationId(new Order,_INTRACITY_);
                    break;
                case _HYPERLOCAL_:
                    $order->order_no = NumberGeneratorServices::generateTranscationId(new Order,_HYPERLOCAL_);
                    break;
                default:
                    break;   
              }
              $order->save();
              $id = $order->id;
              
              if($id){
                $OrderItem = new OrderItem;
                $OrderItem->order_id = $id;
                $OrderItem->service_id = $postDetails[0]->lkp_service_id;
                $OrderItem->title = $postDetails[0]->title;
                
                switch ($request->lkp_service_id) {
                    case _INTRACITY_:
                     $OrderItem->service_name='INTRACITY';
                          break;
                    case _HYPERLOCAL_:
                $OrderItem->service_name='HYPERLOCAL';
                          break; 
                }
                
                $from_location = DB::table('lkp_localities')
                        ->select('locality_name')
                        ->where('id',$postDetails[0]->from_location)
                        ->get();

                $to_location = DB::table('lkp_localities')
                        ->select('locality_name')
                        ->where('id',$postDetails[0]->to_location)
                        ->get();

                $OrderItem->buyer_id = $postDetails[0]->posted_by;
                $OrderItem->buyer_name = $buyer_firstname[0]->username;
                $OrderItem->seller_id = JWTAuth::parseToken()->getPayload()->get('id');
                $OrderItem->seller_name = $seller_firstname;
                $OrderItem->service_id = $postDetails[0]->lkp_service_id;

                if($postDetails[0]->lead_type == 2 && $request->lkp_service_id == _INTRACITY_){
                   $OrderItem->price = $postDetails[0]->emd_amount;
                }else if($postDetails[0]->lead_type == 2 && $request->lkp_service_id == _HYPERLOCAL_){
                   $OrderItem->price = $postDetails[0]->firm_price;
                }

                
                $OrderItem->routeId = $postDetails[0]->id;
                $OrderItem->dispatch_date = $postDetails[0]->valid_from;
                $OrderItem->lead_type = $postDetails[0]->lead_type;

                if(isset($from_location) && !empty($from_location)){
                  $OrderItem->from_location = $from_location[0]->locality_name;
                  $OrderItem->to_location = $to_location[0]->locality_name;
                }
                    
                $OrderItem->save();
              }
              
              return response()->json([
              'payload' => $rowset,
              'isSuccessfull' => true
          ]);
           
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }
  
}