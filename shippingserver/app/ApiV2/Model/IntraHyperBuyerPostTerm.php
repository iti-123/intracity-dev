<?php

namespace ApiV2\Model;

use ApiV2\Requests\IntraHyperBuyerPostRequest as BuyerPostRequest;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;
use DB;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\ApiV2\Events\BuyerPostCreatedEvent;
use ApiV2\Services\NotificationService;

class IntraHyperBuyerPostTerm extends Model
{

    protected $fillable = [
        'emd_amount',
        'emd_mode',
        'award_criteria',
        'contract_allotment',
        'payment_terms',
        'payment_methods',
        'no_of_trucks',
        'avg_turn_over',
        'income_tax_assesse',
        'no_of_years',
        'contract_with_other_company',
        'last_date',
        'last_time',
        'comments',
        'post_type',
        'terms_cond',
        'is_active'
    ];


    protected $table = 'intra_hp_buyer_posts';

    function __construct()
    {

    }

    public static function saveBuyerTermPost($data)
    {
        try {
            return self::insertBuyerTermData($data);
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }


    public static function insertBuyerTermData($data)
    {

        $userID = JWTAuth::parseToken()->getPayload()->get('id');

        $request = new BuyerPostRequest();
        $requestData = $request::jsonDecode($data->termData);
        $attribute = $request::jsonDecode($data->attribute);
        // Instance of Post Request Data


        $termData = new IntraHyperBuyerPostTerm();
        $termData->lead_type = INTRA_HYPER_TERM;
        $termData->lkp_service_id = _INTRACITY_;
        $termData->type_basis = $request::getTypeBasis($requestData);
        $termData->title = $request::getTermTitle($requestData);
        $termData->emd_amount = $request::has($requestData, 'emd_amount');
        $termData->emd_mode = $request::has($requestData, 'emd_mode');
        $termData->award_criteria = $request::has($requestData, 'award_criteria');
        $termData->contract_allotment = $request::has($requestData, 'contract_allotment');
        $termData->payment_term = $request::has($requestData, 'payment_term');
        $termData->no_of_days = $request::has($requestData, 'no_of_days');        
        $termData->payment_method = $request::has($requestData, 'payment_method');
        $termData->no_of_trucks = $request::has($requestData, 'no_of_own_truck');
        $termData->average_turn_over = $request::has($requestData, 'average_turn_over');
        $termData->income_tax_assesse = $request::has($requestData, 'income_tax_assesse');
        $termData->no_of_years = $request::has($requestData, 'no_of_years');
        $termData->term_contract_woc = $request::has($requestData, 'term_contract_woc');

        $termlastDate = str_replace('/','-',$request::has($requestData, 'term_last_date'));
        $termData->last_date = date("Y-m-d", strtotime($termlastDate));  
       
        $termData->last_time = $request::has($requestData, 'term_last_time');
        $termData->comments = $request::has($requestData, 'comments');
        $termData->is_private_public = $request::isPublic($request::has($requestData, 'is_private_public'));
        $termData->is_accept_terms_cond = $request::has($requestData, 'term_condition');
        $termData->is_active = IS_ACTIVE;
        $termData->posted_by = $userID;
        $termData->attribute = $data->attribute;
        $termData->post_status = $data->postStatus;
        $termData->visible_to_seller = self::has($requestData, 'visibleToSellers');
        $termData->post_transaction_id = NumberGeneratorServices::generateTranscationId(new IntraHyperBuyerPostTerm, _INTRACITY_);


        /****************** Start Transaction Code here *******************/
        DB::transaction(function () use ($termData, $requestData, $attribute, $request) {

            $termData->save();
            $id = $termData->id;
            //  dd($requestData->visibleToSellers);
            $prices = $request->priceArray($requestData, $id);


            //    Insert price_exclusive, price_inclusive
            DB::table('intra_hp_inclusive_exclusive_price')->insert($prices);

            // Insert Buyer Term Post Rote
            $insertdata = array();
            $routeAttr = $request->routeArray($attribute, $id, $termData);

            foreach($routeAttr as $key => $val){
                $validFrom = str_replace('/','-',$routeAttr[$key]['valid_from']);
                $routeAttr[$key]['valid_from'] = date("Y-m-d", strtotime($validFrom));  

                $validTo = str_replace('/','-',$routeAttr[$key]['valid_to']);
                $routeAttr[$key]['valid_to'] = date("Y-m-d", strtotime($validTo));  
            }
           

           // return $routeAttr;
            $buyer_routes = DB::table('intra_hp_buyer_seller_routes')->insert($routeAttr);

            // Insert Seller Detail

            if ($request::has($requestData, 'is_private_public') && $requestData->is_private_public == 1) {
                self::saveSeller($request::explode($requestData->visibleToSellers), $id);
            }

        });
        
        NotificationService::createNotification($termData);
        // event(new BuyerPostCreatedEvent($termData));

        return response()->json([
            'isSuccessful' => true,
            'payload' => [
                'primaryData' => $requestData,
                'attribute' => $attribute,
                'data' => $termData
            ]
        ]);
    }

    public static function has($object, $property)
    {
        return property_exists($object, $property) ? $object->$property : '';
    }

    public static function saveSeller($seller_ids, $buyer_post_id)
    {
        if ($seller_ids) {
            $ids = array();
            foreach ($seller_ids as $key => $value) {
                $ids[$key] = array(
                    'buyer_seller_post_id' => $buyer_post_id,
                    'buyer_seller_id' => $value,
                    'type' => BUYER, // for buyer
                    'is_active' => IS_ACTIVE
                );
            }
            DB::table('intra_hp_assigned_seller_buyer')->insert($ids);
        }
    }

    public static function jsonDecode($data)
    {
        return json_decode($data);
    }

// Seller search by buyer to book

    public static function searchBuyer($request)
    {
        
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        //DB::enableQueryLog();
        try {
            $searchTerms = json_decode($request->getContent());
            
           // return response()->json(self::has($searchTerms,'base_hour') ? $searchTerms->base_hour->id:''  );
            $baseHour = self::has($searchTerms,'base_hour') ? $searchTerms->base_hour->id:'';
            if ($searchTerms->type == 1) {
                $city_id = $searchTerms->hour_city->id;
                $dispatch_date = str_replace('/','-',$searchTerms->dispatchDate);
            } else {
                $city_id = $searchTerms->city->id;
                $dispatch_date = str_replace('/','-',$searchTerms->dispatchDate);
            }
            
            $dispatchDate= date("Y-m-d", strtotime($dispatch_date));
            //return $dispatchDate;
            $searchCollection = DB::table('intra_hp_sellerpost_ratecart as rc')
                ->join('intra_hp_buyer_seller_routes as sr', function ($join) {
                    $join->on('sr.fk_buyer_seller_post_id', '=', 'rc.id');
                })
                ->leftjoin('intra_hp_order_items as ot', function ($join) {
                    $join->on('ot.routeId', '=', 'sr.id');
                })
                ->leftjoin('intra_hp_discounts as dis', function ($join) {
                    $join->on('ot.routeId', '=', 'dis.intra_hp_sellerpost_ratecart_id')
                        ->where("dis.discount_basis",'=',2);
                })
                ->where([
                    ['rc.is_active', IS_ACTIVE],
                    ['rc.rate_cart_type', $searchTerms->type],
                    ['sr.is_seller_buyer', SELLER],
                    ['sr.city_id', $city_id],
                ])
                ->whereDate('sr.valid_from', '<=', $dispatchDate)
                ->whereDate('sr.valid_to', '>=', $dispatchDate)
                ->where(function($q) use($searchTerms) {
                    self::additionalFilter($q, $searchTerms);
                })
                ->select(
                    "sr.*",
                    "sr.id",
                    "rc.id as seller_post_id",
                    "rc.title",
                    "rc.posted_by as seller_id",
                    "rc.notes", "rc.created_at as posted_date",
                    "ot.status as orderStatus",
                    "ot.truck_attribute as truckAttribute",
                    "ot.transit_detail as transitDetails",
                    "ot.delivery_detail as deliveryDetials",
                    "ot.consignment_details as consignmentDetails",
                    DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=sr.vehicle_type_id ) as vehicle"),
                    DB::raw("(select group_concat(net_price) as payable_amt from intra_hp_discounts  where fk_rate_card_id = sr.id and discount_basis =1 group by fk_rate_card_id) as payable_amt"),
                    DB::raw("(select group_concat(net_price) as discount from intra_hp_discounts  where fk_rate_card_id = rc.id and discount_basis =2 group by fk_rate_card_id) as postDiscount"),
                    DB::raw("(SELECT username FROM users WHERE id=rc.posted_by LIMIT 1) as seller"),
                    DB::raw('(SELECT CONCAT(hour, "_", distance) AS distance_hour FROM intracity_hour_distance_slabs WHERE id="'.$baseHour.'") as slabTimeDrutaion'),
                    "dis.disc_amt as discount"
                )
                ->orderBy("rc.id", "DESC")->get();
            // dd(DB::getQueryLog());
            // dd($searchCollection);
            return response()->json([
                'status' => 'success',
                'payload' => EncrptionTokenService::idEncrypt($searchCollection)
            ]);
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    private static function additionalFilter($q, $searchData) {
        return $q->where(function($q) use ($searchData) {
            if(self::has($searchData, 'vehiclesType')) {
               $q->where('vehicle_type_id','=',$searchData->vehiclesType->id);
            }
        });
    }


}
