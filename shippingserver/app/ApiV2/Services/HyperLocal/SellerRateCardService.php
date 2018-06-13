<?php

namespace ApiV2\Services\HyperLocal;
use ApiV2\Services\UserSettingsService;
use ApiV2\Model\HyperLocal\SellerRateCard;
use ApiV2\Model\IntraHyperBuyerPost;
use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;
use DB;

use ApiV2\Services\NotificationService;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;

use App\ApiV2\Events\SellerPostCreatedEvent;
use ApiV2\Model\IntraHyperSellerPost;


class SellerRateCardService extends BaseServiceProvider
{

    private static $rows_fetched = 20;

    public static function productCategory($request)
    {

        $result = DB::table('product_category')
            ->select('product_category.id', 'product_category.name')
            ->where('is_active', 1)
            ->get();


        self::$data['data'] = $result;
        self::$data['success'] = true;
        self::$data['status'] = 200;
        return self::$data;

    }

    public static function getHpSellerPostDetails($request)
    {
        $rs = IntraHyperSellerPost::with(['hpSellerDiscount','hpSellercity'])
                    ->where('lkp_service_id', _HYPERLOCAL_)
                    ->where('id',$request->id);

        $data = $rs->get();
            return response()->json([
            'status'=>'success',
            'payload'=>$data
        ]);
    }

   public static function sellerRateCardPost($request)
    {
 

        $userid = JWTAuth::parseToken()->getPayload()->get('id');
        $seller = new IntraHyperSellerPost();
        $data = json_decode($request->getContent());
        $rateCartData = $data->rateCartData;
        $discountData = json_decode($data->discountData);
       // return json_decode($request->discountData);

        $seller->posted_by = $userid;
        $seller->title = $rateCartData->title;
        $seller->product_category = $rateCartData->category->id;

        $fromdate = str_replace('/','-',$rateCartData->fromdate);
        $seller->from_date = date("Y-m-d", strtotime($fromdate)); 

        $todate = str_replace('/','-',$rateCartData->todate);
        $seller->to_date = date("Y-m-d", strtotime($todate)); 
        
        $seller->city_id = $rateCartData->city->id;
        $seller->service_type = self::has($rateCartData, 'service_type');
        $seller->base_price = $rateCartData->base_price;
        $seller->additional_charges = self::has($rateCartData, 'fragile_addtnl_charges');
        $seller->dist_included_per_base = $rateCartData->distance_included;
        $seller->rate_per_extra_kms = $rateCartData->rate_per_extra_km;
        $seller->weight_included = $rateCartData->weight_included;
        $seller->rate_per_extra_kgs = $rateCartData->rate_pre_extra_kg;
        $seller->num_parcels_included = self::has($rateCartData, 'num_of_parcel');
        $seller->additional_cost_per_ext_parcel = self::has($rateCartData, 'addtn_cost_per_ext_parcel');
        $seller->time_for_selected_service = self::has($rateCartData, 'time_for_selected_services');
        $seller->extra_time_per_km = self::has($rateCartData, 'extra_time_per_km');
        $seller->pricing_service_types = self::has($rateCartData, 'pricing');
        $seller->line_items = $rateCartData->line_items;
        $seller->discount = $request->discountData;
        $seller->terms_cond = $rateCartData->terms_condition;
        $seller->is_private_public = $rateCartData->post_type;
        if(!empty($rateCartData->selectseledata)){
           $seller->assign_buyer = property_exists($rateCartData, 'selectseledata') ? $rateCartData->selectseledata : '';
        }
        
        $seller->is_active = IS_ACTIVE;
        $seller->post_status = $data->isOpen;
        $seller->post_transaction_id = NumberGeneratorServices::generateTranscationId(new IntraHyperSellerPost, _HYPERLOCAL_);
        $seller->lkp_service_id = _HYPERLOCAL_;
        
        DB::transaction(function () use($seller) {
            $seller->save();
        });

       
        
        $discountArray = array();
        foreach ($discountData as $key => $discount):
            if ($discount->discountType == 1) {
                $discountArray[$key] = array(
                    "lkp_service_id" => _HYPERLOCAL_,
                    "intra_hp_sellerpost_ratecart_id" => $seller->id,
                    "disc_type" => $discount->discount_type,
                    "disc_amt" => $discount->buyer_discount,
                    "credit_days" => $discount->credit_day,
                    "buyer_id" => '',
                    "discount_level" => $discount->discountType

                );
            } else if ($discount->discountType == 2) {
                $discountArray[$key] = array(
                    "lkp_service_id" => _HYPERLOCAL_,
                    "intra_hp_sellerpost_ratecart_id" => $seller->id,
                    "disc_type" => $discount->discount_type,
                    "disc_amt" => $discount->buyer_discount,
                    "credit_days" => $discount->credit_day,
                    "buyer_id" => $discount->buyer->id,
                    "discount_level" => $discount->discountType

                );
            } else if ($discount->discountType == 3) {
                $discountArray[$key] = array(
                    "lkp_service_id" => _HYPERLOCAL_,
                    "intra_hp_sellerpost_ratecart_id" => $seller->id,
                    "disc_type" => $discount->discount_type,
                    "disc_amt" => $discount->buyer_discount,
                    "credit_days" => $discount->credit_day,
                    "buyer_id" => '',
                    "discount_level" => $discount->discountType

                );
            }

        endforeach;

        NotificationService::createNotification($seller);
        DB::table('intra_hp_discounts')->insert($discountArray);
        return  $seller;
    }

    public static function sellerRateCardDraftsPost($request)
    {
        // return $request->id;
        $userid = JWTAuth::parseToken()->getPayload()->get('id');
        $IntraHyperSellerPost = IntraHyperSellerPost::find($request->id);
        $data = json_decode($request->getContent());
        $rateCartData = $data->rateCartData;
        $discountData = json_decode($data->discountData);
       // return $rateCartData;

        $IntraHyperSellerPost->posted_by = $userid;
        $IntraHyperSellerPost->title = $rateCartData->title;
        $IntraHyperSellerPost->product_category = $rateCartData->category;

        $fromdate = str_replace('/','-',$rateCartData->fromdate);
        $IntraHyperSellerPost->from_date = date("Y-m-d", strtotime($fromdate)); 

        $todate = str_replace('/','-',$rateCartData->todate);
        $IntraHyperSellerPost->to_date = date("Y-m-d", strtotime($todate)); 
        
        $IntraHyperSellerPost->city_id = $rateCartData->city_id;
        $IntraHyperSellerPost->service_type = self::has($rateCartData, 'service_type');
        $IntraHyperSellerPost->base_price = $rateCartData->base_price;
        $IntraHyperSellerPost->additional_charges = self::has($rateCartData, 'fragile_addtnl_charges');
        $IntraHyperSellerPost->dist_included_per_base = $rateCartData->distance_included;
        $IntraHyperSellerPost->rate_per_extra_kms = $rateCartData->rate_per_extra_km;
        $IntraHyperSellerPost->weight_included = $rateCartData->weight_included;
        $IntraHyperSellerPost->rate_per_extra_kgs = $rateCartData->rate_pre_extra_kg;
        $IntraHyperSellerPost->num_parcels_included = self::has($rateCartData, 'num_of_parcel');
        $IntraHyperSellerPost->additional_cost_per_ext_parcel = self::has($rateCartData, 'addtn_cost_per_ext_parcel');
        $IntraHyperSellerPost->time_for_selected_service = self::has($rateCartData, 'time_for_selected_services');
        $IntraHyperSellerPost->extra_time_per_km = self::has($rateCartData, 'extra_time_per_km');
        $IntraHyperSellerPost->pricing_service_types = self::has($rateCartData, 'pricing');
        $IntraHyperSellerPost->line_items = $rateCartData->line_items;
        $IntraHyperSellerPost->discount = $request->discountData;
        $IntraHyperSellerPost->terms_cond = $rateCartData->terms_condition;
        $IntraHyperSellerPost->is_private_public = $rateCartData->post_type;

        if(!empty($rateCartData->selectseledata)){
           $IntraHyperSellerPost->assign_buyer = property_exists($rateCartData, 'selectseledata') ? $rateCartData->selectseledata : '';
        }
        
        $IntraHyperSellerPost->is_active = IS_ACTIVE;
        $IntraHyperSellerPost->post_status = $data->isOpen;
        $IntraHyperSellerPost->post_transaction_id = NumberGeneratorServices::generateTranscationId(new IntraHyperSellerPost, _HYPERLOCAL_);
        $IntraHyperSellerPost->lkp_service_id = _HYPERLOCAL_;
        
        DB::transaction(function () use($IntraHyperSellerPost) {
            $IntraHyperSellerPost->save();
        });

         DB::table('intra_hp_discounts')
               ->where('intra_hp_sellerpost_ratecart_id',$request->id)
               ->where('lkp_service_id',_HYPERLOCAL_)
               ->delete();
       
        $discountArray = array();
        foreach ($discountData as $key => $discount):
            if ($discount->discountType == 1) {
                $discountArray[$key] = array(
                    "lkp_service_id" => _HYPERLOCAL_,
                    "intra_hp_sellerpost_ratecart_id" => $IntraHyperSellerPost->id,
                    "disc_type" => $discount->discount_type,
                    "disc_amt" => $discount->buyer_discount,
                    "credit_days" => $discount->credit_day,
                    "buyer_id" => '',
                    "discount_level" => $discount->discountType

                );
            } else if ($discount->discountType == 2) {
                $discountArray[$key] = array(
                    "lkp_service_id" => _HYPERLOCAL_,
                    "intra_hp_sellerpost_ratecart_id" => $IntraHyperSellerPost->id,
                    "disc_type" => $discount->discount_type,
                    "disc_amt" => $discount->buyer_discount,
                    "credit_days" => $discount->credit_day,
                    "buyer_id" => $discount->buyer->id,
                    "discount_level" => $discount->discountType

                );
            } else if ($discount->discountType == 3) {
                $discountArray[$key] = array(
                    "lkp_service_id" => _HYPERLOCAL_,
                    "intra_hp_sellerpost_ratecart_id" => $IntraHyperSellerPost->id,
                    "disc_type" => $discount->discount_type,
                    "disc_amt" => $discount->buyer_discount,
                    "credit_days" => $discount->credit_day,
                    "buyer_id" => '',
                    "discount_level" => $discount->discountType

                );
            }

        endforeach;
       // NotificationService::createNotification($IntraHyperSellerPost);
        DB::table('intra_hp_discounts')->insert($discountArray);
        return  $IntraHyperSellerPost;
    }

    public static function has($object, $property)
    {
        return property_exists($object, $property) ? $object->$property : '';
    }

    public static function sellersearchlist($request)
    {

        DB::enableQueryLog();
        $userid = JWTAuth::parseToken()->getPayload()->get('id');
        // $cityid=$request->city->id;
        // $rs = DB::table('intra_hp_buyer_posts as sb')
        //                    ->select('sb.*')

        //                    ->orderBy('id', 'desc')
        //                    ->get();

        // $rs = DB::select('SELECT * FROM intra_hp_buyer_posts WHERE JSON_EXTRACT(multiple_location, "$.ip") = "'10.0.0.1'"');

        //     //dd(DB::getQueryLog());
        //     return response()->json([
        //     'status'=>'success',
        //     'payload'=>$rs
        // ]);

    }

    public static function sellerPostList($request)
    {    
        $cities = $request->city;
        $service_type = $request->service_type;
        $type = $request->type;
        $usernames = $request->usernames;

        $from_date = str_replace('/','-',$request->from_date);
        $to_date = str_replace('/','-',$request->to_date);

        $pageLoader = $request->pageLoader; 
        $userid = JWTAuth::parseToken()->getPayload()->get('id');
        $sellerList = DB::table('intra_hp_sellerpost_ratecart as sr')
            ->select('sr.*')
            ->where('sr.lkp_service_id', _HYPERLOCAL_)
            ->where('sr.is_active', 1)
            ->where('sr.posted_by', $userid) 
            ->where(function($query)  use ($cities) {
              if(isset($cities) && !empty($cities)){
                  foreach ($cities as $key => $val) {
                      if($key == 0){
                        $query->where('sr.city_id','=',$val);
                      }else{
                        $query->orWhere('sr.city_id','=',$val);
                      }
                  }
              }
            })
            ->where(function($query)  use ($service_type) {
              if(isset($service_type) && !empty($service_type)){
                  foreach ($service_type as $key => $val) {
                      if($key == 0){
                        $query->where('sr.service_type','=',$val);
                      }else{
                        $query->orWhere('sr.service_type','=',$val);
                      }
                  }
              }
            })
            ->where(function($query)  use ($usernames) {
              if(isset($usernames) && !empty($usernames)){
                  foreach ($usernames as $key => $val) {
                      if($key == 0){
                        $query->where('sr.posted_by','=',$val);
                      }else{
                        $query->orWhere('sr.posted_by','=',$val);
                      }
                  }
              }
            })
            ->where(function($query)  use ($from_date) {
              if(isset($from_date) && !empty($from_date)){
                  $query->where('sr.from_date','=',date('Y-m-d',strtotime($from_date)));
              }
            })
            ->where(function($query)  use ($to_date) {
              if(isset($to_date) && !empty($to_date)){
                 $query->where('sr.to_date','=',date('Y-m-d',strtotime($to_date)));
              }
            })
            ->orderBy('sr.id','DESC');

            if($type == 'public')
            {
               $sellerList->where('sr.is_private_public',0); 
            }  
            if($type == 'private')
            {
              $sellerList->where('sr.is_private_public',1);  
            }

            $seller = $sellerList->paginate($pageLoader);
            foreach($seller as $key => $val){
               $count = DB::table('intra_hp_buyer_posts')
                        ->join('intra_hp_buyer_seller_routes as routes','routes.fk_buyer_seller_post_id','=','intra_hp_buyer_posts.id')
                        ->where('routes.lkp_service_id',_HYPERLOCAL_)
                        ->where('routes.is_seller_buyer',1)
                        ->where('routes.city_id',$val->city_id)
                        ->count();
               $seller[$key]->countOfLeads = $count;        
            }
            return response()->json([
                'status'=>'success',
                'payload'=>$seller
            ]);
    }

    /** Get Seller Post Counts */
    public static function sellerListCounts()
    {
        $userid = JWTAuth::parseToken()->getPayload()->get('id');
        $rs = DB::table('intra_hp_sellerpost_ratecart')
            ->select(
                DB::raw("(select count(`is_private_public`) from intra_hp_sellerpost_ratecart where is_private_public=0 AND is_active=1 AND lkp_service_id='"._HYPERLOCAL_."' AND posted_by='".$userid."') as public"),
                DB::raw("(select count(`is_private_public`) from intra_hp_sellerpost_ratecart where is_private_public=1 AND is_active=1 AND lkp_service_id='"._HYPERLOCAL_."' AND posted_by='".$userid."') as private"),
                DB::raw("(select count(`is_private_public`) from intra_hp_sellerpost_ratecart where  is_active=1 AND lkp_service_id='"._HYPERLOCAL_."' AND posted_by='".$userid."') as outbond"),
                DB::raw("(select count(`id`) from intra_hp_assigned_seller_buyer where  is_active=1 AND lkp_service_id='"._HYPERLOCAL_."' AND buyer_seller_id='".$userid."') as inbound")
            )->limit(1);

        return response()->json([
            'status' => 'success',
            'payload' => $rs->get()
        ]);
    }

    /** Get Seller Post Counts */

    public static function searchAccdngFilters($request)
    {   
        $public_private=$request->type['post_type'];
        $bound=$request->type['bound'];
        $userid = JWTAuth::parseToken()->getPayload()->get('id');
        
        $rs = DB::table('intra_hp_sellerpost_ratecart as bp')
            ->leftjoin('intra_hp_discounts as ds', 'ds.intra_hp_sellerpost_ratecart_id', '=', 'bp.id')
            ->leftjoin('intra_hp_assigned_seller_buyer as ab', 'ab.buyer_seller_post_id', '=', 'bp.id')
            ->select('bp.id','bp.title',
                'bp.city_id',
                'bp.post_status',
                'bp.is_private_public',
                'bp.from_date', 'bp.to_date', 'bp.service_type',
                DB::raw("(select username FROM users WHERE id=ds.buyer_id ) as buyer"),
                DB::raw("(select city_name FROM lkp_cities WHERE id=bp.city_id ) as city_name"),
                DB::raw("(select username FROM users WHERE id=ab.buyer_seller_id ) as assign_buyer")
            )
            ->where([
                ['bp.is_active', 1],
                ['bp.lkp_service_id', _HYPERLOCAL_],
                ['bp.posted_by', $userid],
            ]);
            if($public_private == 'public')
            {
               $rs->where('bp.is_private_public', 0); 
            }  
            if($public_private == 'private')
            {
              $rs->where('bp.is_private_public', 1);  
            }
        $results = $rs->get();       

        foreach($results as $key => $val){
           $count = DB::table('intra_hp_buyer_posts')
                    ->join('intra_hp_buyer_seller_routes as routes','routes.fk_buyer_seller_post_id','=','intra_hp_buyer_posts.id')
                    ->where('routes.lkp_service_id',_HYPERLOCAL_)
                    ->where('routes.is_seller_buyer',1)
                    ->where('routes.city_id',$val->city_id)
                    ->count();
           $results[$key]->countOfLeads = $count;        
        }
         
        return response()->json([
            'status' => 'success',
            'payload' => $results
        ]);
    }


    public static function sellerSearchResult($request)
    {


        $fromLocation = $request->from_location['id'];
        $toLocation = $request->to_location['id'];
        $cityName = $request->city['city_name'];
        $cityId = $request->city['id'];
        $routeId = $request->id;

        $rs = DB::table('intra_hp_buyer_posts as bp')
            ->select('*',
                DB::raw("(select username FROM users WHERE id=bp.posted_by ) as buyer"),
                DB::raw("(select locality_name FROM lkp_localities WHERE id=$fromLocation ) as fromName"),
                DB::raw("(select locality_name FROM lkp_localities WHERE id=$toLocation ) as toName")
            )
            ->join('intra_hp_buyer_seller_routes as sr', 'sr.fk_buyer_seller_post_id', '=', 'bp.id')
            ->where([
                    ['sr.is_seller_buyer','1'],
                    ['sr.city_id',$cityId],
                    ['sr.is_active', '1'],
                    ['bp.is_active', '1'],
                    ['bp.lkp_service_id',_HYPERLOCAL_],
                    ['sr.lkp_service_id',_HYPERLOCAL_]
                   
                    ])
                    
            ->where('sr.from_location',$fromLocation) 
            ->where('sr.to_location',$toLocation);  

            return response()->json([
            'status'=>'success',
            'payload'=>$rs->get()
        ]); 

    }

    public function postBy()
    {
        return $this->hasOne('ApiV2\Model\UserDetails', 'id', 'posted_by')->select('id', 'username');
    }

    public function getAllRoute()
    {
        return $this->hasMany('ApiV2\Model\IntraHyperRoute', 'fk_buyer_seller_post_id', 'id');
    }


    public static function getPostDetails($request)
    {

        $id = $request->id;
        $rs = DB::table('intra_hp_sellerpost_ratecart as bp')
            //  ->join('intra_hp_discounts as hd', 'hd.intra_hp_sellerpost_ratecart_id', '=', 'bp.id')
            // ->leftjoin('intra_hp_assigned_seller_buyer as sb', 'sb.buyer_seller_post_id', '=', 'bp.id')
            ->select(
                'bp.city_id',
                'bp.post_status',
                'bp.id as post_id',
                'bp.lkp_service_id',
                'bp.is_private_public',
                'bp.product_category',
                'bp.service_type',
                // 'sr.price_type',
                // 'sr.firm_price',
                // 'sr.is_active',
                // 'sr.to_location',
                // 'sr.material_type',
                'bp.post_transaction_id',


                // DB::raw("(select group_concat(username) FROM intra_hp_assigned_seller_buyer asb INNER JOIN users as u ON(u.id=buyer_seller_id) WHERE buyer_seller_post_id=bp.id AND type=2 ) as seller"),
                DB::raw("(select city_name FROM lkp_cities WHERE id=bp.city_id ) as city_name")
            )
            ->where('bp.is_active', 1)
            // ->where('sr.is_seller_buyer', 2)
            ->where('bp.id', $id)
            ->limit(self::$rows_fetched);
        $results = $rs->get();

        foreach($results as $key => $val){
           $count = DB::table('intra_hp_buyer_posts')
                    ->join('intra_hp_buyer_seller_routes as routes','routes.fk_buyer_seller_post_id','=','intra_hp_buyer_posts.id')
                    ->where('routes.lkp_service_id',_HYPERLOCAL_)
                    ->where('routes.is_seller_buyer',1)
                    ->where('routes.city_id',$val->city_id)
                    ->count();
           $results[$key]->countOfLeads = $count;        
        }
        
        return response()->json([
            'status' => 'success',
            'payload' => $results
        ]);
    } 


}
