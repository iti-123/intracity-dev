<?php

namespace Api\Services\HyperLocal;
use Api\Services\UserSettingsService;
use Api\Model\HyperLocal\SellerRateCard;
use Api\Model\IntraHyperBuyerPost;
use Api\Services\LogistiksCommonServices\NumberGeneratorServices;
use DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Api\Services\LogistiksCommonServices\EncrptionTokenService;


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


    public static function sellerRateCardPost($request)
    {

        $userid = JWTAuth::parseToken()->getPayload()->get('id');
        $seller = new SellerRateCard();
        $data = json_decode($request->getContent());
        $rateCartData = $data->rateCartData;
        $discountData = $data->discountData;
        //return $data;

        $seller->posted_by = $userid;
        $seller->product_category = $rateCartData->category->id;
        $seller->from_date = $rateCartData->fromdate;
        $seller->to_date = $rateCartData->todate;
        $seller->city_id = $rateCartData->city->id;
        $seller->line_items = $rateCartData->line_items;
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
        $seller->terms_cond = $rateCartData->terms_condition;
        $seller->is_private_public = $rateCartData->post_type;
        $seller->assign_buyer = property_exists($rateCartData, 'selectseledata') ? $rateCartData->selectseledata : '';
        $seller->is_active = IS_ACTIVE;
        $seller->post_status = $data->isOpen;
        $seller->post_transaction_id = NumberGeneratorServices::generateTranscationId(new IntraHyperBuyerPost, 22);
        $seller->lkp_service_id = _HYPERLOCAL_;
        $seller->save();

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

        DB::table('intra_hp_discounts')->insert($discountArray);

        return $seller;

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
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $results = UserSettingsService::getUserSettings(_HYPERLOCAL_, $context='', $userId);
         //return $results;
        $userid = JWTAuth::parseToken()->getPayload()->get('id');
        $sellerList = DB::table('intra_hp_sellerpost_ratecart as sr')
            ->select('sr.*')
            ->where('sr.lkp_service_id', 22)
            ->where('sr.is_active', 1)
            ->where('sr.posted_by', $userid)
            
            ->orderBy('sr.id', 'DESC')
            ->get();


        return $sellerList;

    }

    /** Get Seller Post Counts */
    public static function sellerListCounts()
    {
        $userid = JWTAuth::parseToken()->getPayload()->get('id');
        $rs = DB::table('intra_hp_sellerpost_ratecart')
            ->select(
                DB::raw("(select count(`is_private_public`) from intra_hp_sellerpost_ratecart where is_private_public=0 AND is_active=1 AND lkp_service_id=22 AND posted_by='".$userid."') as public"),
                DB::raw("(select count(`is_private_public`) from intra_hp_sellerpost_ratecart where is_private_public=1 AND is_active=1 AND lkp_service_id=22 AND posted_by='".$userid."') as private"),
                DB::raw("(select count(`is_private_public`) from intra_hp_sellerpost_ratecart where  is_active=1 AND lkp_service_id=22 AND posted_by='".$userid."') as outbond"),
                DB::raw("(select count(`id`) from intra_hp_assigned_seller_buyer where  is_active=1 AND lkp_service_id=22 AND buyer_seller_id='".$userid."') as inbound")
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
            ->select('bp.id',
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
            if ($public_private == 'public')
            {
               $rs->where('bp.is_private_public', 0); 
            }
               
            if ($public_private == 'private')
            {
              $rs->where('bp.is_private_public', 1);  
            }
               

         
        

        return response()->json([
            'status' => 'success',
            'payload' => $rs->get()
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
        return $this->hasOne('Api\Model\UserDetails', 'id', 'posted_by')->select('id', 'username');
    }

    public function getAllRoute()
    {
        return $this->hasMany('Api\Model\IntraHyperRoute', 'fk_buyer_seller_post_id', 'id');
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

        
        return response()->json([
            'status' => 'success',
            'payload' => $rs->get()
        ]);
    } 


}
