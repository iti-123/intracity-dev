<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 05-07-2017
 * Time: 19:37
 */

namespace Api\Controllers\Hyperlocal;
use Api\Model\IntraHyperRoute;
use DB;
use Api\Services\UserSettingsService;
use Api\Controllers\BaseController;
use Api\Model\HyperLocal\M_Buyer_post;
use Api\Model\IntraHyperBuyerPost;
use Api\Requests\Hyperlocal\BuyerPostRequest;
use Api\Services\LogistiksCommonServices\EncrptionTokenService;
use Api\Controllers\UserServices;
use Api\Controllers\AbstractUserServices;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

//use Api\Services\BlueCollar\SellerRegistrationService;

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

    public function getRecords(Request $request)
    {
          
          $finalRow = $request->totalRow + $request->offset;  
          $type = $request->type; 
          $userid = JWTAuth::parseToken()->getPayload()->get('id');
        try {
            $data = IntraHyperBuyerPost::with(['getAllRoute' => function ($query) use ($request,$userid) {
                $query->where('is_active', '=', 1);
            }])
                ->where('lkp_service_id', '=', _HYPERLOCAL_)
                ->where('is_active', '=', '1')
                ->where('posted_by', '=', $userid)
                ->where(function ($query) use ($type) {
                    if ($type != 'all' && $type != 'spot') {
                        $query->where('is_private_public', '=', $type);
                    }
                    if ($type === 'all') {
                        $query->whereIn('is_private_public', [1,0]);
                    }
                    if ($type === 'term') {
                        $query->where('lead_type', '=', 2);
                    }
                    if ($type === 'spot') {
                        $query->where('lead_type', '=', 1);
                    }
                    if ($type === 0) {
                        $query->where('is_private_public', '=', 0);
                    }
                   
                })
                ->limit($finalRow)->get();
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
        
           $post=self::AssignPost($userid);
           $inboundData=array();
        return response()->json([$setting, 200]);

        //    if($setting['se_all_private_posts']=='on')
        //    {
              
        //        $spot = IntraHyperBuyerPost::with(['getAllRoute' => function ($query) use ($request,$userid) {
        //             $query->where('is_active', '=', 1);
        //         }])
        //             ->where('lkp_service_id', '=', _HYPERLOCAL_)
        //             ->where('lead_type', '=', 1)
        //             ->whereIn('id', $post)
        //             ->where(function($query) use($request){                        
        //                 self::applyFilter($query,$request);
        //             })
        //             ->get();

        //        $term = IntraHyperBuyerPost::with(['getAllRoute' => function ($query) use ($request,$userid) {
        //             $query->where('is_active', '=', 1);
        //         }])
        //             ->where('lkp_service_id', '=', _HYPERLOCAL_)
        //             ->where('lead_type', '=', 2)
        //             ->whereIn('id', $post)
        //             ->where(function($query) use($request){                        
        //                 self::applyFilter($query,$request);
        //             })
        //             ->get();
        //            $inboundData[0]['key']='Related-Spot-Lead';
        //            $inboundData[0]['index']=0;
        //            $inboundData[0]['data']=$spot;
        //            $inboundData[1]['key']='Related-Term-Lead';
        //            $inboundData[1]['index']=1;
        //            $inboundData[1]['data']=$term;
        //    }

        //    if($setting['partly_related_leads']=='on')
        //    {
        //        if($usercityid!='')
        //         { 
        //                $spot = IntraHyperBuyerPost::with(['getAllRoute' => function ($query) use ($request,$userid,$usercityid) {
        //                     $query->where('is_active', '=', 1);
        //                     $query->where('city_id', '=', $usercityid);
        //                 }])
        //                     ->where('lkp_service_id', '=', _HYPERLOCAL_)
        //                     ->where('lead_type', '=', 1)
        //                     ->where(function($query) use($request){                        
        //                 self::applyFilter($query,$request);

        //                    })
        //                     ->get();
        //                $term = IntraHyperBuyerPost::with(['getAllRoute' => function ($query) use ($request,$userid,$usercityid) {
        //                     $query->where('is_active', '=', 1);
        //                     $query->where('city_id', '=', $usercityid);
        //                 }])
        //                     ->where('lkp_service_id', '=', _HYPERLOCAL_)
        //                     ->where('lead_type', '=', 2)
        //                            ->where(function($query) use($request){                        
        //                         self::applyFilter($query,$request);
        //                      })
        //                     ->get();
        //                    $inboundData[2]['key']='Partly-Related-Spot-Lead';
        //                    $inboundData[2]['data']=$spot;
        //                    $inboundData[2]['index']=2;
        //                    $inboundData[3]['key']='Partly-Related-Term-Lead';
        //                    $inboundData[3]['index']=3;
        //                    $inboundData[3]['data']=$term;
        //        }
        //    }

        //    if($setting['unrelated_leads']=='on')
        //    {
        //        /// required condition
        //        if($usercityid!='')
        //         { 
        //                $spot = IntraHyperBuyerPost::with(['getAllRoute' => function ($query) use ($request,$userid,$usercityid) {
        //                     $query->where('is_active', '=', 1);
        //                     $query->where('city_id', '=', $usercityid);
                          
        //                 }])
        //                     ->where('lkp_service_id', '=', _HYPERLOCAL_)
        //                     ->where('lead_type', '=', 1)
        //                     ->where(function($query) use($request){                        
        //                  self::applyFilter($query,$request);
        //                 })
        //                     ->get();
        //                $term = IntraHyperBuyerPost::with(['getAllRoute' => function ($query) use ($request,$userid,$usercityid) {
        //                      $query->where('is_active', '=', 1);
        //                      $query->where('city_id', '=', $usercityid);
                            
        //                 }])
        //                     ->where('lkp_service_id', '=', _HYPERLOCAL_)
        //                     ->where('lead_type', '=', 2)
        //                         ->where(function($query) use($request){                        
        //                 self::applyFilter($query,$request);
        //              })
        //                     ->get();
        //                    $inboundData[4]['key']='Unrelated-Related-Spot-Lead';
        //                    $inboundData[4]['data']=$spot;
        //                    $inboundData[4]['index']=4;
        //                    $inboundData[5]['key']='Unrelated-Related-Term-Lead';
        //                    $inboundData[5]['index']=5;
        //                    $inboundData[5]['data']=$term;
        //        }
        //    }
         // return $inboundData;
           
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
                          ->limit($finalRow )->get();
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
            $query->where('city_id', '=', $userdetails->lkp_city_id);
        },'getuser','quote'=>function ($query) use ($userid) {
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
          
            // "se_all_rate_card" => "on"//**spot Partly related enquiry
            // "se_all_private_posts" => "on"//privtae **spot Unrelated enquiry
            // "partly_related_leads" => "on"//city **spot Partly related lead
            //  "unrelated_leads" => "on"//public ** spot Unrelated leads
           if($setting['se_all_rate_card']=='on')
           { 
                    $row = DB::table('intra_hp_sellerpost_ratecart')
                          ->select('*',DB::raw("(SELECT username FROM users WHERE id=intra_hp_sellerpost_ratecart.posted_by LIMIT 1) as vendor"))
                          ->where('lkp_service_id',_HYPERLOCAL_)
                          ->where('city_id',$usercityid)
                           ->where('id',382)///temporary id for check
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
           if($setting['se_all_private_posts']=='on')
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
           if($setting['partly_related_leads']=='on')
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
           if($setting['unrelated_leads']=='on')
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
  /*******************save data term contract**************************/
  public function termContract(Request $request)
  {
     try{

     }catch(Exception $e)
     {
       return $this->errorResponse($e);
     }
  }

  
}