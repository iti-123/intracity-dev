<?php

namespace Api\Model;

use Api\Services\LogistiksCommonServices\EncrptionTokenService;
use Api\Services\LogistiksCommonServices\NumberGeneratorServices;
use DB;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Facades\JWTAuth;
use Api\Services\UserSettingsService;
use Api\Controllers\UserServices;
use Api\Model\IntraHyperSellerPost;
use Log;

use  ApiV2\Modules\Intracity\IntraHyperBuyerSellerPostRecommender;

class IntraHyperBuyerPost extends Model
{
    private static $rows_fetched = 10;
    protected $fillable = ['fk_buyer_id', 'lkp_service_id', 'type_basis', 'last_date', 'last_time', 'is_private_public', 'accept_term_cond'];

    protected $table = 'intra_hp_buyer_posts';

    public static function saveBuyerSpotsPost($data)
    {
        try {
            return self::insertBuyerSpotData($data);
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

    }

    public static function insertBuyerSpotData($data)
    {
        $userID = JWTAuth::parseToken()->getPayload()->get('id');

        $spotData = self::jsonDecode($data->spotData);

        $lastDateTimeForQuote = strtotime(self::has($spotData, 'last_date') . " " . self::has($spotData, 'last_time'));

        $buyer_post = new IntraHyperBuyerPost();
        $buyer_post->lkp_service_id = _INTRACITY_;//Intracity
        $buyer_post->lead_type = self::getType($spotData);
        $buyer_post->type_basis = self::getTypeBasis($spotData);
        $buyer_post->is_accept_terms_cond = self::has($spotData, 'term_condition');
        $buyer_post->is_private_public = self::isPublic(self::has($spotData, 'post_type_term'));
        $buyer_post->last_date = self::has($spotData, 'last_date');
        $buyer_post->last_time = self::has($spotData, 'last_time');
        $buyer_post->posted_by = $userID;
        $buyer_post->is_active = IS_ACTIVE;
        $buyer_post->last_date_time_for_quote = $lastDateTimeForQuote;
        $buyer_post->attribute = $data->attribute;
        $buyer_post->json_data = $data->spotData;
        $buyer_post->visible_to_seller = json_encode(self::has($spotData, 'visibleToSellers'));
        $buyer_post->post_transaction_id = NumberGeneratorServices::generateTranscationId(new IntraHyperBuyerPost(), _INTRACITY_);
        /****************** Start Transaction Code here *******************/

        DB::transaction(function () use ($buyer_post, $data, $spotData) {
            // return response()->json($buyer_post);
            $buyer_post->save();
            // Get Inserted Id
            $buyer_post_id = $buyer_post->id;

            $BuyerRoute = self::jsonDecode($data->attribute);

            // Check if Post is Private

            if (self::has($spotData, 'post_type_term') == 1) {
                $seller_ids = self::explode(self::has($spotData, 'visibleToSellers'));
                self::saveSeller($seller_ids, $buyer_post_id);
            }
            // Lets insert in buyer route list
            $insertdata = array();
            foreach ($BuyerRoute as $key => $value) {
                $insertdata[$key] = array(
                    'is_seller_buyer' => BUYER,// for buyer
                    'lkp_service_id' => _INTRACITY_, // Buyer Post Id
                    'fk_buyer_seller_post_id' => $buyer_post_id,
                    'type_basis' => self::getTypeBasis($value),
                    'city_id' => $value->city->id,
                    'hour_dis_slab' => self::has($value, 'hd_slab') ? $value->hd_slab->id : '',
                    'vehicle_type_id' => self::getVehicleType($value),
                    'valid_from' => self::getValidFrom($value),
                    'valid_to' => self::getValidTo($value),
                    'number_of_veh_need' => self::getTotalVehicle($value),
                    'vehicle_rep_location' => self::getVehicleReportingLocation($value),
                    'vehicle_rep_time' => self::getReportingTime($value),
                    'weight' => self::getWeight($value),
                    'material_type' => self::getMaterial($value),
                    'from_location' => self::getFromLocation($value),
                    'to_location' => self::getToLocation($value),
                    'is_active' => IS_ACTIVE,
                    'loads' => json_encode(self::has($value, 'loads')),
                    'firm_price' => self::has($value, 'firm_price'),
                    'price_type' => self::getPriceType($value),
                );

                // $service = new Solr();
                // $service->add($insertdata[$key],$value,$buyer_post);
            }
            $buyer_routes = DB::table('intra_hp_buyer_seller_routes')->insert($insertdata);

        });
        return response()->json([
            'isSuccessful' => true,
            'payload' => $buyer_post,
            'data' => $data->attribute
        ], 200);

    }

    public static function jsonDecode($data)
    {
        return json_decode($data);
    }

    public static function isPublic($input) {
        if(isset($input) && !empty($input)) {
            if($input==1) {
                return $input;
            } elseif ($input==2) {
                return 0;
            }
        }        
    }

    public static function has($object, $property)
    {
        return property_exists($object, $property) ? $object->$property : '';
    }

    public static function getType($value)
    {
        $return_value = '';
        $type = self::has($value, 'type');
        if (!empty($type)) {
            if ($type == 'term') {
                $return_value = INTRA_HYPER_TERM;
            } else if ($type == 'spot') {
                $return_value = INTRA_HYPER_SPOT;
            }
        }
        return $return_value;
    }

    public static function getTypeBasis($value)
    {
        $return_value = '';
        $type_basis = self::has($value, 'type_basis');
        if (!empty($type_basis)) {
            if ($type_basis == 'hours' || $type_basis == 'term_hours') {
                $return_value = INTRA_HYPER_HOURS;
            } else if ($type_basis == 'distance_basis' || $type_basis == 'term_distance') {
                $return_value = INTRA_HYPER_DISTANCE;
            }
        }
        return $return_value;
    }

    public static function explode($value)
    {
        if (!empty($value)) {
            return explode(',', $value);
        }
        return false;
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
                    'is_active' => 0
                );
            }
            DB::table('intra_hp_assigned_seller_buyer')->insert($ids);
        }
    }

    public static function getVehicleType($value)
    {
        $vehicle_type = '';
        if (!empty(self::has($value, 'd_vehicle_type_any'))) {
            $vehicle_type = self::has($value, 'd_vehicle_type_any') ? $value->d_vehicle_type_any->id : '';
        }
        return $vehicle_type;
    }

    public static function getValidFrom($value)
    {
        $valid_from = '';
        if (!empty(self::has($value, 'd_valid_from'))) {
            $valid_from = self::has($value, 'd_valid_from');
        } else if (!empty(self::has($value, 'departure'))) {
            $valid_from = self::has($value, 'departure');
        }
        return $valid_from;
    }

    public static function getValidTo($value)
    {
        $valid_to = '';
        if (!empty(self::has($value, 'd_valid_to'))) {
            $valid_to = self::has($value, 'd_valid_to');
        }
        return $valid_to;
    }

    public static function getTotalVehicle($value)
    {
        $return_value = '';
        if (!empty(self::has($value, 'd_no_of_vehicle'))) {
            $return_value = self::has($value, 'd_no_of_vehicle');
        } else if (!empty(self::has($value, 'no_of_vehicles'))) {
            $return_value = self::has($value, 'no_of_vehicles');
        }
        return $return_value;
    }

    public static function getVehicleReportingLocation($value)
    {
        $returnData = '';
        if (!empty(self::has($value, 'vehicle_reporting_location'))) {
            $returnData = self::has($value, 'vehicle_reporting_location')->id;
        }
        return $returnData;
    }

    public static function getReportingTime($value)
    {
        $vehicle_rep_time = '';
        if (!empty(self::has($value, 'vehicle_reporting_time'))) {
            $vehicle_rep_time = self::has($value, 'vehicle_reporting_time');
        } else if (!empty(self::has($value, 'd_vehicle_reporting_time'))) {
            $vehicle_rep_time = self::has($value, 'd_vehicle_reporting_time');
        }
        return $vehicle_rep_time;
    }

    public static function getWeight($value)
    {
        $weight = '';
        if (!empty(self::has($value, 'd_weight'))) {
            $weight = self::has($value, 'd_weight');
        }
        return $weight;
    }

    public static function getMaterial($value)
    {
        $material_type = '';
        if (!empty(self::has($value, 'd_material_type'))) {
            $material_type = self::has($value, 'd_material_type') ? $value->d_material_type->id : '';
        }
        return $material_type;
    }

    public static function getFromLocation($value)
    {
        $return_value = '';
        if (!empty(self::has($value, 'd_from_location'))) {
            $return_value = self::has($value, 'd_from_location') ? $value->d_from_location->id : '';
        }
        return $return_value;
    }

    public static function getToLocation($value)
    {
        $return_value = '';
        if (!empty(self::has($value, 'd_to_location'))) {
            $return_value = self::has($value, 'd_to_location') ? $value->d_to_location->id : '';
        }
        return $return_value;
    }

    public static function getPriceType($value)
    {
        $returnData = '';
        if (!empty(self::has($value, 'price_type'))) {
            $returnData = self::has($value, 'price_type')->id;
        }
        return $returnData;
    }

    public static function saveBuyerTermPost($data)
    {
        try {
            return self::insertBuyerTermData($data);
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    public static function countbuyerpost()
    {

        /** For Total Number of Buyer Post Spots **/
        $get_total_spots = DB::table('intra_hp_buyer_posts as bp')
            ->join('intra_hp_buyer_seller_routes as sr', 'sr.fk_buyer_seller_post_id', '=', 'bp.id')
            ->leftjoin('lkp_localities as fromLoctn', 'sr.from_location', '=', 'fromLoctn.id')
            ->leftjoin('lkp_localities as toLoctn', 'sr.to_location', '=', 'toLoctn.id')
            ->select('bp.id',
                'sr.city_id',
                'bp.type_basis',
                'bp.is_private_public',
                'bp.lkp_service_id',
                'bp.last_date',
                DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=sr.vehicle_type_id AND is_active = 1) as vehicle"),
                DB::raw("(select city_name FROM lkp_cities WHERE id=sr.city_id ) as city_name"),
                DB::raw("(select locality_name FROM lkp_localities WHERE id=fromLoctn.id) as from_location"),
                DB::raw("(select locality_name FROM lkp_localities WHERE id=toLoctn.id) as to_location")
            )
            ->where('bp.is_active', 1)
            ->where('bp.lead_type', INTRA_HYPER_SPOT)
            ->where('sr.is_active', 1)
            ->where('bp.lkp_service_id','=',_INTRACITY_)
            ->where('sr.lkp_service_id','=',_INTRACITY_)
            ->where('sr.is_seller_buyer', 1)
            ->count();

        /** For Total Number of Buyer Post Terms **/
        $get_total_terms = DB::table('intra_hp_buyer_posts as bp')
            ->join('intra_hp_buyer_seller_routes as sr', 'sr.fk_buyer_seller_post_id', '=', 'bp.id')
            ->leftjoin('lkp_localities as fromLoctn', 'sr.from_location', '=', 'fromLoctn.id')
            ->leftjoin('lkp_localities as toLoctn', 'sr.to_location', '=', 'toLoctn.id')
            ->select('bp.id',
                'sr.city_id',
                'bp.type_basis',
                'bp.is_private_public',
                'bp.lkp_service_id',
                'bp.last_date',
                DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=sr.vehicle_type_id AND is_active = 1) as vehicle"),
                DB::raw("(select city_name FROM lkp_cities WHERE id=sr.city_id ) as city_name"),
                DB::raw("(select locality_name FROM lkp_localities WHERE id=fromLoctn.id) as from_location"),
                DB::raw("(select locality_name FROM lkp_localities WHERE id=toLoctn.id) as to_location")
            )
            ->where('bp.is_active', 1)
            ->where('bp.lead_type', INTRA_HYPER_TERM)
            ->orderBy('bp.id', 'DESC')
            ->where('sr.is_active', 1)
            ->where('sr.is_seller_buyer', 1)
            ->where('sr.lkp_service_id','=',_INTRACITY_)
            ->count();

        /** For Total Number Private **/
        $get_total_private = DB::table('intra_hp_buyer_posts as bp')
            ->join('intra_hp_buyer_seller_routes as sr', 'sr.fk_buyer_seller_post_id', '=', 'bp.id')
            ->leftjoin('lkp_localities as fromLoctn', 'sr.from_location', '=', 'fromLoctn.id')
            ->leftjoin('lkp_localities as toLoctn', 'sr.to_location', '=', 'toLoctn.id')
            ->select('bp.id',
                'sr.city_id',
                'bp.type_basis',
                'bp.is_private_public',
                'bp.lkp_service_id',
                'bp.last_date',
                DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=sr.vehicle_type_id AND is_active = 1) as vehicle"),
                DB::raw("(select city_name FROM lkp_cities WHERE id=sr.city_id ) as city_name"),
                DB::raw("(select locality_name FROM lkp_localities WHERE id=fromLoctn.id) as from_location"),
                DB::raw("(select locality_name FROM lkp_localities WHERE id=toLoctn.id) as to_location")
            )
            ->where('bp.is_active', 1)
            ->where('bp.is_private_public', _PRIVATE_)
            ->where('sr.is_active', 1)
            ->where('sr.is_seller_buyer', 1)
            ->where('sr.lkp_service_id','=',_INTRACITY_)
            ->count();


        /** For Total Number Public  **/
        $get_total_public = DB::table('intra_hp_buyer_posts as bp')
            ->join('intra_hp_buyer_seller_routes as sr', 'sr.fk_buyer_seller_post_id', '=', 'bp.id')
            ->leftjoin('lkp_localities as fromLoctn', 'sr.from_location', '=', 'fromLoctn.id')
            ->leftjoin('lkp_localities as toLoctn', 'sr.to_location', '=', 'toLoctn.id')
            ->select('bp.id',
                'sr.city_id',
                'bp.type_basis',
                'bp.is_private_public',
                'bp.lkp_service_id',
                'bp.last_date',
                DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=sr.vehicle_type_id AND is_active = 1) as vehicle"),
                DB::raw("(select city_name FROM lkp_cities WHERE id=sr.city_id ) as city_name"),
                DB::raw("(select locality_name FROM lkp_localities WHERE id=fromLoctn.id) as from_location"),
                DB::raw("(select locality_name FROM lkp_localities WHERE id=toLoctn.id) as to_location")
            )
            ->where('bp.is_active', 1)
            ->where('bp.is_private_public', _PUBLIC_)
            ->where('sr.is_active', 1)
            ->where('sr.is_seller_buyer', 1)
            ->where('sr.lkp_service_id','=',_INTRACITY_)
            ->count();


        return response()->json([

            'total_buyerpost_spots' => $get_total_spots,
            'total_private_post' => $get_total_private,
            'total_public_post' => $get_total_public,
            'get_total_terms' => $get_total_terms


        ]);

    }

    /** Buyer List Spots Table **/
    public static function buyerlist()
    {

        $get_buyer_list = DB::table('intra_hp_buyer_posts')
            ->select('intra_hp_buyer_posts.*')
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->get();

        return $get_buyer_list;

    }

    public static function applyFilter($query, $input) {
        return $query->where(function($query) use ($input) {
            $leadTypeId = '';
            $isPublic = '';
            if ($input->type == 'spot') {
                $query->where('bp.lead_type', 1);
            } else if ($input->type == 'term') {
                $query->where('bp.lead_type', 2);
            } else if ($input->type == 'public') {
                $query->where('bp.is_private_public', 0);
            } else if ($input->type == 'private') {
                $query->where('bp.is_private_public', 1);
            }
        });
    }
    public static function applyInboundFilter($query, $input) {
        return $query->where(function($query) use ($input) {
            if ($input->type == 'spot') {
                $query->where('lead_type', 1);
            } else if ($input->type == 'term') {
                $query->where('lead_type', 2);
            } else if ($input->type == 'public') {
                $query->where('is_private_public', 0);
            } else if ($input->type == 'private') {
                $query->where('is_private_public', 1);
            }
        });
    }    
    

    /** All Records According to Filters **/
    public static function allrecords($request)
    {
        $payload = '';
        
        switch($request->isInbound) {
            case 'inbound':
                return self::filterInbound($request);
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

    


    public static function filterQuery($query,$input,$activeUserDetails,$key,$settingValue) {
        if('se_all_rate_card' == $key) {
            
            $query->with(['routes.city','routes.fromLocalities','routes.toLocalities','routes.vehicleType', 'routes'=> function($query) use ($input) {
                
                if($input->isInbound == 'inbound') {
                    self::applyRouteFilter($query, $input);
                }
                
            }]);

        } else if('se_all_private_posts' == $key) {
            
            $query->with('routes.city','routes.fromLocalities','routes.toLocalities','routes.vehicleType')
                    ->where("is_private_public","=",1);
        
        } else if('partly_related_leads' == $key) {
         
            $query->with(['routes'=> function($query) use ($activeUserDetails) {
                    $query->where([
                        ['city_id','=', $activeUserDetails->lkp_city_id]
                    ]);
                }])
                ->where("is_private_public","=",1);
        } else if('unrelated_leads' == $key) {
        
            $query->with('routes')
                ->where("is_private_public","=",0);
        
        }

        return $query->latest()->get();
    }

    private static $userSettings = array(
        "se_all_rate_card"=>"Spot partly related enquiry",
        "se_all_private_posts"=>"Spot unrelated enquiry ",
        "partly_related_leads"=>"Spot Partly related leads",
        "unrelated_leads"=>"Spot unrelated leads"
    );

    public static function filterBySettings($query, $input ,$settings, $activeUserDetails) {
        $tempArray = array();
        foreach($settings as $key => $setting):
            if($key != '_token') {
                if($key && array_key_exists($key, self::$userSettings) && $settings[$key] == 'on') {
                    array_push($tempArray,
                        array(
                         "title" => self::$userSettings[$key],
                         "value" => self::filterQuery($query,$input,$activeUserDetails,$key, $settings[$key])
                        )
                    );
                } else {
                    return "not exist";
                }
            }
        endforeach;
        return $tempArray;
    }

    public static function filterInbound($request) {
        Log::info("filter inbound details");
        $postData = '';

        $userID = JWTAuth::parseToken()->getPayload()->get('id');  
        
        $settings = UserSettingsService::getUserSettings($request->serviceId,'', $userID);

        $activeUserDetails = (new UserServices)->getUserDetailsById($userID);
        if(sizeof($settings) && !empty($activeUserDetails)) {

        // Start filter coding 

            $query  = IntraHyperSellerPost::with('seller')
                ->where(function($query) use($request) {  
                    static::defaultFilter($query, $request);            
                    static::applyInboundFilter($query, $request);
                    static::applyPostFilter($query, $request);                      
                });

            if(array_search(ucfirst($request->title),static::$userSettings,true)) {
                
                $settingKey = array_search(ucfirst($request->title),static::$userSettings,true);
                
                $postData = self::filterQuery($query,$request, $activeUserDetails,$settingKey, 'on');
                return response()->json([
                    "payload" => EncrptionTokenService::encryptRouteId($postData),               
                ]);

            } else {

                $postData = self::filterBySettings($query,$request, $settings, $activeUserDetails);
                
                return response()->json([
                    "payload" => $postData,               
                ]);
            
            }
        } else {
            return "Invalid settings and user details";
        }

              
    } 

    public static function countInboundRecords($request) {
         $countPostPublic = IntraHyperSellerPost::
                where(function($query) {
                    static::defaultFilter($query);
                })
                ->where([
                    ['is_private_public','=',0]                    
                ])->count();
             $countPostPrivate = IntraHyperSellerPost::
                where(function($query) {
                    static::defaultFilter($query);
                })
                ->where([                    
                    ['is_private_public','=',1]                    
                ])->count();
            //dd(DB::getQueryLog());
            return response()->json([
                "payload" => array(
                    "count" => array(
                        "public"=>$countPostPublic,
                        "private"=>$countPostPrivate,
                        "spot"=>0,
                        "term"=>0
                    )
                )                 
            ]); 
    }

    public static function applyPostFilter($query, $input) {
        return $query->where(function($query) use ($input) {           

            if(isset($input->postStatus) && !empty($input->postStatus)) {
                $query->whereIn('post_status',$input->postStatus);
            }
    
            if(isset($input->postType) && !empty($input->postType)) {
                $query->whereIn('rate_cart_type',$input->postType);
            }

        });
    }

    public static function defaultFilter($query, $input) {
        return $query->where(function($query) use ($input) {
            $query->where([
                ['lkp_service_id',_INTRACITY_],
                ['is_active','=',1]
            ]);          

        });
    }


    public static function applyRouteFilter($query, $input) {
        
        if(isset($input->vehicleType) && !empty($input->vehicleType)) {
            $query->whereIn('vehicle_type_id',$input->vehicleType);
        }

        // if(isset($input->fromDate) && !empty($input->fromDate)) {
        //     $query->whereIn('valid_from',$input->fromDate);
        // }
        // if(isset($input->toDate) && !empty($input->toDate)) {
        //     $query->whereIn('valid_to',$input->toDate);
        // }
        return $query;
    }

    
    


    public static function filterOutbound($request) {
        $fetchedRow = $request->totalRow + $request->offset;
        $payload = DB::table('intra_hp_buyer_posts as bp')
                ->join('intra_hp_buyer_seller_routes as sr', 'sr.fk_buyer_seller_post_id', '=', 'bp.id')
                ->leftjoin('lkp_localities as fromLoctn', 'sr.from_location', '=', 'fromLoctn.id')
                ->leftjoin('lkp_localities as toLoctn', 'sr.to_location', '=', 'toLoctn.id')
                ->select('bp.id',
                    'sr.city_id',
                    'bp.type_basis',
                    'bp.is_private_public',
                    'bp.lkp_service_id',
                    'bp.last_date',
                    'bp.post_status',
                    'sr.valid_from',
                    'sr.valid_to',
                    'bp.last_date',
                    'bp.lead_type',
                    DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=sr.vehicle_type_id AND is_active = 1) as vehicle"),
                    DB::raw("(select city_name FROM lkp_cities WHERE id=sr.city_id ) as city_name"),
                    DB::raw("(select locality_name FROM lkp_localities WHERE id=fromLoctn.id) as from_location"),
                    DB::raw("(select locality_name FROM lkp_localities WHERE id=toLoctn.id) as to_location")
                )
                ->where('bp.is_active', 1)
                ->where('sr.is_active', 1)
                ->where('sr.lkp_service_id','=',_INTRACITY_)
                ->where(function($query) use ($request) {
                    static::applyFilter($query, $request);
                })
                ->orderBy('bp.id', 'DESC')
                ->where('sr.is_seller_buyer', 1)
                ->limit($fetchedRow)
                ->get();
        return response()->json([
            "payload" => EncrptionTokenService::idEncrypt($payload)
        ]);
    } 

    /* Count Buyer Post Spots*/

    /** All Records According to Filters **/
    public static function buyerFilterSearch()
    {

        $buyerfilters = DB::table('intra_hp_buyer_posts')
            ->select('intra_hp_buyer_posts.*')
            ->where('is_active', 1)
            ->get();

        return $buyerfilters;

    }
    /* Count Buyer Post Spots*/

    public static function filterAccordingSearch($request)
    {

        $filters = DB::table('intra_hp_buyer_posts as bp')
            ->join('intra_hp_buyer_seller_routes as sr', 'sr.fk_buyer_seller_post_id', '=', 'bp.id')
            ->leftjoin('intra_hp_assigned_seller_buyer as ab', 'ab.buyer_seller_post_id', '=', 'bp.id')
            ->leftjoin('lkp_localities as fromLoctn', 'sr.from_location', '=', 'fromLoctn.id')
            ->leftjoin('lkp_localities as toLoctn', 'sr.to_location', '=', 'toLoctn.id')
            ->select('bp.id',
                'sr.city_id',
                'bp.type_basis',
                'bp.is_private_public',
                'bp.lkp_service_id',
                'bp.last_date',
                'sr.valid_from',
                'sr.valid_to',
                DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=sr.vehicle_type_id AND is_active = 1) as vehicle"),
                DB::raw("(select city_name FROM lkp_cities WHERE id=sr.city_id ) as city_name"),
                DB::raw("(select username FROM users WHERE id=ab.buyer_seller_id ) as assign_seller"),
                DB::raw("(select locality_name FROM lkp_localities WHERE id=fromLoctn.id) as from_location"),
                DB::raw("(select locality_name FROM lkp_localities WHERE id=toLoctn.id) as to_location")
            )
            ->where('bp.is_active', 1)
            ->where('sr.is_active', 1)
            ->limit(self::$rows_fetched)
            ->get();
        return $filters;
    }
    /** Buyer List Spots Table **/

    public static function getMessageDetails($request)
    {

        $id = $request->id;
        $query = DB::table('intra_hp_buyer_posts as bp')
            ->join('intra_hp_buyer_seller_routes as sr', 'sr.fk_buyer_seller_post_id', '=', 'bp.id')
            ->select('bp.is_private_public',
                'bp.last_date',
                'bp.payment_term',
                'bp.term_contract_woc',
                'sr.tracking',
                'sr.valid_from',
                'sr.valid_to',

                DB::raw("(select count(*) FROM intra_hp_buyer_seller_routes WHERE fk_buyer_seller_post_id = $id AND is_seller_buyer = 1 AND is_active = 1) as countRoutes")
            )
            ->where('bp.is_active', 1)
            ->where('sr.is_active', 1)
            ->where('bp.id', $id)
            ->limit(self::$rows_fetched)
            ->first();

        return response()->json([
            'payload' => $query
        ], 200);
    }

    public static function getBuyerDiscount($request)
    {

        $discountQuery = DB::table('intra_hp_disounts as d')
            ->join('intra_hp_buyer_seller_routes as sr', 'sr.fk_buyer_seller_post_id', '=', 'd.id')
            ->select('d.name', 'discount_type', 'd.discount', 'd.credit_days')
            ->where('sr.is_active')
            ->get();
    }

    public static function getRouteDetails($request)
    {
        $id = $request->id;

        $routeQuery = DB::table('intra_hp_buyer_seller_routes as sr')
            ->select(
                'fk_buyer_seller_post_id',
                'from_location',
                'to_location',
                'vehicle_type_id',
                'material_type',
                'base_distance',
                'rate_base_distance',
                'is_active'
            )
            ->where('is_active', 1)
            ->get();

        return $routeQuery;
    }

    public static function getbuyerPostDetails($request)
    {

        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $id = EncrptionTokenService::idDecrypt($request->id);
        $rs = DB::table('intra_hp_buyer_posts as bp')
            ->join('intra_hp_buyer_seller_routes as sr', 'sr.fk_buyer_seller_post_id', '=', 'bp.id')
            ->leftjoin('intra_hp_assigned_seller_buyer as sb', 'sb.buyer_seller_post_id', '=', 'bp.id')
            ->select('sr.id',
                'sr.city_id',
                'bp.post_status',
                'bp.id as post_id',
                'bp.lead_type',
                'bp.lkp_service_id',
                'bp.type_basis',
                'bp.emd_amount',
                'bp.emd_mode',
                'bp.payment_term',
                'bp.payment_method',
                'bp.no_of_trucks',
                'bp.last_date',
                'bp.last_time',
                'bp.is_private_public',
                'sr.type_basis',
                'sr.valid_from',
                'sr.valid_to',
                'sr.from_location',
                'sr.price_type',
                'sr.firm_price',
                'sr.is_active',
                'sr.to_location',
                'sr.material_type',
                'bp.average_turn_over',
                'bp.income_tax_assesse',
                'bp.no_of_years',
                'sr.vehicle_rep_time',
                'sb.buyer_seller_id as seller',
                'bp.post_transaction_id',


                // DB::raw("(select group_concat(username) FROM intra_hp_assigned_seller_buyer asb INNER JOIN users as u ON(u.id=buyer_seller_id) WHERE buyer_seller_post_id=bp.id AND type=2 ) as seller"),
                DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=sr.vehicle_type_id ) as vehicle"),
                DB::raw("(select city_name FROM lkp_cities WHERE id=sr.city_id ) as city_name")
            )
            ->where('bp.is_active', 1)
            ->where('sr.is_seller_buyer', 1)
            ->where('bp.id', $id)
            ->limit(self::$rows_fetched);

        return response()->json([
            'status' => 'success',
            'payload' => $rs->get()
        ]);
    }

    public static function deleteBuyerPost($request)
    {
        echo $id = $request->id;
        DB::transaction(function () use ($id) {
            DB::table('intra_hp_buyer_posts')
                ->where('id', $id)
                ->update(['post_status' => 2]);
            DB::table('intra_hp_buyer_seller_routes')
                ->where('fk_buyer_seller_post_id', $id)
                ->where('is_seller_buyer', 2)
                ->update(['is_active' => 2]);
        });
        return response()->json([
            'payload' => $id,
            'isSuccessfull' => true,
        ], 200);

    }

    public static function getPostDataById($id)
    {
        $buyerPostData = IntraHyperBuyerPost::find($id);
        return response()->json([
            'isSuccessfull' => true,
            'payload' => $buyerPostData
        ], 200);
    }

    public function postRoute()
    {
        return $this->hasMany('Api\Model\IntraHyperRoute', 'fk_buyer_seller_post_id', 'id');
    }

    public function postBy()
    {
        return $this->hasOne('Api\Model\UserDetails', 'id', 'posted_by')->select('id', 'username');
    }

    public function vehicleType()
    {
        return $this->hasOne('Api\Model\VehicleType', 'id', 'vehicle_type_id')->select('id', 'vehicle_type');
    }

    /// hyperlocal post list

    public function getAllRoute()
    {
        return $this->hasMany('Api\Model\IntraHyperRoute', 'fk_buyer_seller_post_id', 'id')->where('lkp_service_id', '=', _HYPERLOCAL_);
    }

    public function quoteHyper() 
    {
       return $this->hasOne('Api\Model\IntraHyperQuotaion', 'post_id', 'id')->where('lkp_service_id', '=', _HYPERLOCAL_);
    }

    public function getuser()
    {
        return $this->hasOne('Api\Model\UserDetails', 'id', 'posted_by');
    }    

    public function quote()
    {
        return $this->hasOne('ApiV2\Model\IntraHyperQuotaion', 'route_id', 'id');
    }

}
