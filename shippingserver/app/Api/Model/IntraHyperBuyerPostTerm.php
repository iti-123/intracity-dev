<?php

namespace Api\Model;

use Api\Requests\IntraHyperBuyerPostRequest as BuyerPostRequest;
use Api\Services\LogistiksCommonServices\EncrptionTokenService;
use Api\Services\LogistiksCommonServices\NumberGeneratorServices;
use DB;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        $termData->last_date = $request::has($requestData, 'term_last_date');
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
            $buyer_routes = DB::table('intra_hp_buyer_seller_routes')->insert($routeAttr);

            // Insert Seller Detail

            if ($request::has($requestData, 'is_private_public') && $requestData->is_private_public == 1) {
                self::saveSeller($request::explode($requestData->visibleToSellers), $id);
            }

        });

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
            $buyerPostRequest = new BuyerPostRequest();
            $searchTerms = json_decode($request->getContent());

            if ($searchTerms->type == 1) {
                $city_id = $searchTerms->hour_city->id;
                $dispatchDate = $searchTerms->date;
            } else {
                $city_id = $searchTerms->city->id;
                $dispatchDate = $searchTerms->dispatchDate;
            }
            $searchCollection = DB::table('intra_hp_sellerpost_ratecart as rc')
                ->join('intra_hp_buyer_seller_routes as sr', function ($join) {
                    $join->on('sr.fk_buyer_seller_post_id', '=', 'rc.id');
                })
                ->where([
                    ['rc.is_active', IS_ACTIVE],
                    ['rc.rate_cart_type', $searchTerms->type],
                    ['sr.is_seller_buyer', SELLER],
                    ['sr.city_id', $city_id],
                ])
                ->whereDate('sr.valid_from', '<=', $dispatchDate)
                ->whereDate('sr.valid_to', '>=', $dispatchDate)
                ->select(
                    "sr.*",
                    "rc.id as seller_post_id",
                    "rc.posted_by as seller_id",
                    "rc.notes", "rc.created_at as posted_date",
                    DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=sr.vehicle_type_id ) as vehicle"),
                    DB::raw("(select group_concat(net_price) as payable_amt from intra_hp_discounts  where fk_rate_card_id = sr.id and discount_basis =1 group by fk_rate_card_id) as payable_amt"),
                    DB::raw("(select group_concat(net_price) as discount from intra_hp_discounts  where fk_rate_card_id = rc.id and discount_basis =2 group by fk_rate_card_id) as postDiscount"),
                    DB::raw("(SELECT username FROM users WHERE id=rc.posted_by LIMIT 1) as seller")

                )
                ->orderBy("rc.id", "DESC")->get();
            // dd(DB::getQueryLog());
            //dd($searchCollection);
            return response()->json([
                'status' => 'success',
                'payload' => EncrptionTokenService::idEncrypt($searchCollection)
            ]);
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }


}
