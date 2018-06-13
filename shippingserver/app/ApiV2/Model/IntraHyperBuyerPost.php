<?php

namespace ApiV2\Model;

use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;
use DB;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Services\UserSettingsService;
use ApiV2\Controllers\UserServices;
use ApiV2\Model\IntraHyperSellerPost;
use Log;
use App\ApiV2\Events\BuyerPostCreatedEvent;

use ApiV2\Model\IntraHyperRoute;
use ApiV2\Modules\Intracity\IntraHyperBuyerSellerPostRecommender;

use ApiV2\Services\NotificationService;
use ApiV2\Services\SettingsSearchService;
use ApiV2\Utils\CommonComponents;


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

    public static function getIntraSlab($data)
    {
        try {
            $slab = DB::table('intracity_hour_distance_slabs')
                    ->where('id','=',$data->id)
                    ->select('*')
                    ->get();
            return $slab;
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

    }

    public static function postLeadsList($inputData)
    {
      $id = EncrptionTokenService::idDecrypt($inputData->ids);
      $city_id = IntraHyperRoute::where('id','=',$id)
                    ->select('city_id')
                    ->get();

      $leads = DB::table('intra_hp_sellerpost_ratecart')
               ->join('intra_hp_buyer_seller_routes as sr','intra_hp_sellerpost_ratecart.id','=','sr.fk_buyer_seller_post_id')
               ->where('intra_hp_sellerpost_ratecart.lkp_service_id',_INTRACITY_)
               ->where('sr.city_id', $city_id[0]->city_id)
               ->where('sr.is_seller_buyer', 2)
               ->where(function($query) use($inputData) {
                     if(isset($inputData['sellerType']) && !empty($inputData['sellerType'])) {
                       $query->whereIn('intra_hp_sellerpost_ratecart.posted_by',$inputData['sellerType']);
                    }
                })
                ->where(function($query) use($inputData) {
                     if(isset($inputData['vehicleType']) && !empty($inputData['vehicleType'])) {
                       $query->whereIn('sr.vehicle_type_id',$inputData['vehicleType']);
                    }
                })
                ->select('intra_hp_sellerpost_ratecart.*','sr.*',
                DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=sr.vehicle_type_id ) as vehicle"),
                DB::raw("(select city_name FROM lkp_cities WHERE id=sr.city_id ) as city_name"))
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

    public static function postHpLeadsList($inputData)
    {
      $id = EncrptionTokenService::idDecrypt($inputData->ids);
      $city_id = IntraHyperRoute::where('id','=',$id)
                    ->select('city_id')
                    ->get();
       
      $leads = DB::table('intra_hp_sellerpost_ratecart')
               ->where('intra_hp_sellerpost_ratecart.lkp_service_id',_HYPERLOCAL_)
               ->where('intra_hp_sellerpost_ratecart.city_id', $city_id[0]->city_id)
              //->where('intra_hp_sellerpost_ratecart.is_seller_buyer', 2)
               ->where(function($query) use($inputData) {
                     if(isset($inputData['sellerType']) && !empty($inputData['sellerType'])) {
                       $query->whereIn('intra_hp_sellerpost_ratecart.posted_by',$inputData['sellerType']);
                    }
                })
                ->select('intra_hp_sellerpost_ratecart.*',
                DB::raw("(select city_name FROM lkp_cities WHERE id = intra_hp_sellerpost_ratecart.city_id ) as city_name"))
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

    public static function getbuyerPostLeadDetails($request)
    {
        $id = EncrptionTokenService::idDecrypt($request->id);
        $city_id = IntraHyperRoute::where('id','=',$id)
                    ->select('fk_buyer_seller_post_id')
                    ->get();
                    
        $rs = DB::table('intra_hp_buyer_posts as sp')
            ->join('intra_hp_buyer_seller_routes as sr', 'sr.fk_buyer_seller_post_id','=','sp.id')
            ->select('sp.*','sr.*',
                DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=sr.vehicle_type_id ) as vehicle"),
                DB::raw("(select city_name FROM lkp_cities WHERE id=sr.city_id ) as city_name")
            )
            ->where('sp.lkp_service_id',_INTRACITY_)
            ->where('sr.id', $id)
            ->where('sr.is_seller_buyer',BUYER)
            ->get();
        //return $id;
        if(!empty($rs)){
             $data['dataPostDetails'] = $rs;
             $data['success'] = true;
             $data['status'] = 200;
        }else{
            $data['success'] = false;
            $data['status'] = 400;
        }
      
        return $data;
    }

    public static function postQueryBuilder($query, $input, $inKey, $dbKey, $operator){
      if(isset($input[$inKey])){
        foreach ($input[$inKey] as $key=>$value) {
          if($operator=='LIKE'){
            $value = '%'.$value.'%';
          }
          if($key==0){
            $query->where($dbKey, $operator, $value);
          }else{
            $query->orWhere($dbKey, $operator, $value);
          }
        }
      }
    }

    public function routes()
    {
        return $this->hasMany('ApiV2\Model\IntraHyperRoute', 'fk_buyer_seller_post_id', 'id')
                    ->where([
                        ['lkp_service_id', '=', _INTRACITY_],
                        ['is_active', '=', 1]                    
                    ]);
    }

    public function buyer()
    {
        return $this->hasOne('App\User', 'id', 'posted_by')->select("id","username");
    }

    public static function insertBuyerSpotData($data)
    {
        $userID = JWTAuth::parseToken()->getPayload()->get('id');

        $spotData = self::jsonDecode($data->spotData);

        $lastDateTimeForQuote = strtotime(self::has($spotData, 'last_date') . " " . self::has($spotData, 'last_time'));

        $buyer_post = new IntraHyperBuyerPost();
        $buyer_post->lkp_service_id = _INTRACITY_;//Intracity
        $buyer_post->title = self::has($spotData, 'title');;
        $buyer_post->lead_type = self::getType($spotData);
        $buyer_post->type_basis = self::getTypeBasis($spotData);
        $buyer_post->is_accept_terms_cond = self::has($spotData, 'term_condition');
        $buyer_post->is_private_public = self::isPublic(self::has($spotData, 'post_type_term'));

        $lastDate = str_replace('/','-',self::has($spotData, 'last_date')); 
        $buyer_post->last_date = date("Y-m-d", strtotime($lastDate));

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
              //  return $value;
                $validFrom = str_replace('/','-',self::getValidFrom($value));
                $validTo = str_replace('/','-',self::getValidTo($value));

                $insertdata[$key] = array(
                    'is_seller_buyer' => BUYER,// for buyer
                    'lkp_service_id' => _INTRACITY_, // Buyer Post Id
                    'fk_buyer_seller_post_id' => $buyer_post_id,
                    'type_basis' => self::getTypeBasis($value),
                    'city_id' => $value->city->id,
                    'hour_dis_slab' => self::has($value, 'hd_slab') ? $value->hd_slab->id : '',
                    'vehicle_type_id' => self::getVehicleType($value),
                    'valid_from' => date("Y-m-d", strtotime($validFrom)), 
                    'valid_to' =>   date("Y-m-d", strtotime($validTo)),
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
            //return $insertdata;
            $buyer_routes = DB::table('intra_hp_buyer_seller_routes')->insert($insertdata);

        });
        $buyer_post->visible_to_seller = self::has($spotData, 'visibleToSellers');
        $buyer_post->role = BUYER;
        
        NotificationService::createNotification($buyer_post);
        // event(new BuyerPostCreatedEvent($buyer_post));
        
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
                    'lkp_service_id' => _INTRACITY_,
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
        } else if (!empty(self::has($value, 'valid_from_dTerm'))) {
            $valid_from = self::has($value, 'valid_from_dTerm');
        }
        return $valid_from;
    }

    public static function getValidTo($value)
    {
        $valid_to = '';
        if (!empty(self::has($value, 'd_valid_to'))) {
            $valid_to = self::has($value, 'd_valid_to');
        } else if (!empty(self::has($value, 'valid_to_dTerm'))) {
            $valid_to = self::has($value, 'valid_to_dTerm');
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
            $userID = JWTAuth::parseToken()->getPayload()->get('id');
            $rs=DB::table('intra_hp_buyer_posts')
               ->select(
                DB::raw("(select count(`is_private_public`) from intra_hp_buyer_posts where is_private_public=0 AND is_active=1 AND lkp_service_id=3) as public"),
                DB::raw("(select count(`is_private_public`) from intra_hp_buyer_posts where is_private_public=1 AND is_active=1 AND lkp_service_id=3) as private"),
                DB::raw("(select count(`lead_type`) from intra_hp_buyer_posts where  lead_type=1 AND is_active=1 AND lkp_service_id=3) as spot"),
                DB::raw("(select count(`lead_type`) from intra_hp_buyer_posts where  lead_type=2 AND is_active=1 AND lkp_service_id=3) as term"),
                DB::raw("(select count(*) from intra_hp_buyer_posts where is_active=1 AND lkp_service_id=3) as outBoundCount")
                )->limit(1);

            return response()->json([
            'status'=>'success',
            'payload'=>$rs->get()
        ]);
    }

    /** Buyer List Spots Table **/
    public static function buyerlist()
    {

        $get_buyer_list = DB::table('intra_hp_buyer_posts')
            ->select('intra_hp_buyer_posts.*')
            ->where('is_active', 1)
            ->where('lkp_service_id', _INTRACITY_)
            ->orderBy('id', 'DESC')
            ->get();

        return $get_buyer_list;

    }

    public static function applyFilter($query, $input) {
        return $query->where(function($query) use ($input) {
            $leadTypeId = '';
            $isPublic = '';
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

    public static function applyFilterbuyer($query, $input) {
        
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

    public static function intraPrivateBuyerPost($request)
    {
      $ids = EncrptionTokenService::idDecrypt($request->id);

      $post_id = IntraHyperRoute::where('id','=',$ids)
                                ->select('fk_buyer_seller_post_id')
                                ->first();
      $username = DB::table('intra_hp_assigned_seller_buyer as asb')
                   ->join('users as us', 'asb.buyer_seller_id','=','us.id')
                   ->where('asb.buyer_seller_post_id','=',$post_id->fk_buyer_seller_post_id)
                   ->where('asb.lkp_service_id','=',3)
                   ->select('us.username')
                   ->get();
       if(!empty($username)){
          $data['data'] = $username;
          $data['success'] = true;
          $data['status'] = 200;
       }else{
          $data['success'] = false;
          $data['status'] = 400;
       }
      
      return $data;
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

    public static function defaultFilter($query) {
        return $query->where(function($query) {
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

    public function document()
    {
        return $this->hasMany('ApiV2\Model\IntraHyperDocumentUpload', 'buyerpost_terms_id', 'id');
    }
    

    public static function filterOutbound($request) {
        $fetchedRow = $request->totalRow + $request->offset;
        $payload  = IntraHyperRoute::with(['post' => function($query) use ($request) {
                if($request->type != 'all') {
                    static::applyFilter($query, $request);
                }
            }])
            ->with(['post.document','views.viewedBy','views'=> function($query) use ($request) {
                $query->where("role_id","=",SELLER);
            }])
            ->with(['quotes','quotes.postedto','quotes.contract','quotes.checkOrder','city','fromLocalities','toLocalities','vehicleType','notification' => function($q) {
                $q->where('service','=',_INTRACITY_)
                    ->where('role','=',1);
            }])
            ->where(function($query) use ($request) {                
                $query->where('intra_hp_buyer_seller_routes.lkp_service_id','=',_INTRACITY_);
                if(isset($request->postId) && !empty($request->postId)) {
                    $query->where('intra_hp_buyer_seller_routes.id','=',EncrptionTokenService::idDecrypt($request->postId));
                }
                if($request->type != 'all') {
                    if ($request->type == 'spot') {
                        $query->where('intra_hp_buyer_posts.lead_type', 1);
                    } else if ($request->type == 'term') {
                        $query->where('intra_hp_buyer_posts.lead_type', 2);
                    } else if ($request->type == 'public') {
                        $query->where('intra_hp_buyer_posts.is_private_public', 0);
                    } else if ($request->type == 'private') {
                        $query->where('intra_hp_buyer_posts.is_private_public', 1);
                    }
                }
                $userID = JWTAuth::parseToken()->getPayload()->get('id');
                $query->where("intra_hp_buyer_posts.posted_by","=",$userID);
            }) 
            ->leftJoin("intra_hp_buyer_posts",function($join) use($request) {
                $join->on("intra_hp_buyer_seller_routes.fk_buyer_seller_post_id","=","intra_hp_buyer_posts.id");
            }) 
            ->orderBy('intra_hp_buyer_seller_routes.id', 'DESC')     
            ->groupBy('intra_hp_buyer_seller_routes.fk_buyer_seller_post_id')
            ->limit($fetchedRow)         
            ->get(["intra_hp_buyer_seller_routes.*","intra_hp_buyer_posts.title","intra_hp_buyer_posts.posted_by"]);
        return response()->json([
            "payload" => EncrptionTokenService::encrypt($payload)
        ]);
        
    } 

    public static function buyerPostquote($request) {
        $fetchedRow = $request->totalRow + $request->offset;
        $payload  = IntraHyperRoute::with(['post' => function($query) use ($request) {
                // static::applyFilterbuyer($query, $request);
            }])
            ->leftjoin('intra_hp_buyer_posts','intra_hp_buyer_posts.id','=','intra_hp_buyer_seller_routes.fk_buyer_seller_post_id')
            ->with(['quotes.postedto', 'quotes.contract','city','fromLocalities','toLocalities','post'])
            ->where(function($query) use ($request) {      
                
                if ($request->type == 'spot') {
                    $query->where('intra_hp_buyer_posts.lead_type', 1);
                } else if ($request->type == 'term') {
                    $query->where('intra_hp_buyer_posts.lead_type', 2);
                } else if ($request->type == 'public') {
                    $query->where('intra_hp_buyer_posts.is_private_public', 0);
                } else if ($request->type == 'private') {
                    $query->where('intra_hp_buyer_posts.is_private_public', 1);
                }     
                      
                $query->where('intra_hp_buyer_seller_routes.lkp_service_id','=',_HYPERLOCAL_);
                $query->where('intra_hp_buyer_posts.lkp_service_id','=',_HYPERLOCAL_);

                $postId = EncrptionTokenService::idDecrypt($request->postId);
                $routeId = DB::table('intra_hp_buyer_seller_routes')
                    ->select('id')
                    ->where('fk_buyer_seller_post_id','=',$postId)
                    ->first();
                if(isset($request->postId) && !empty($request->postId)) {
                    $query->where('intra_hp_buyer_seller_routes.id','=',$routeId->id);
                }
            })  
            ->orderBy('intra_hp_buyer_seller_routes.id', 'DESC')     
            ->groupBy('intra_hp_buyer_posts.id')
            //->limit($fetchedRow)         
            ->get(['intra_hp_buyer_posts.lead_type','intra_hp_buyer_posts.is_private_public','intra_hp_buyer_seller_routes.*']);

        foreach($payload as $key => $val){
           $count = DB::table('intra_hp_sellerpost_ratecart')
                    ->where('lkp_service_id',_HYPERLOCAL_)
                    ->where('city_id',$val->city_id)
                    ->count();
           $payload[$key]->countOfLeads = $count;        
        }    
        return response()->json([
            "payload" => EncrptionTokenService::encrypt($payload)
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
            ->select(
                'sr.id',
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
        return $this->hasMany('ApiV2\Model\IntraHyperRoute', 'fk_buyer_seller_post_id', 'id');
    }

    public function postBy()
    {
        return $this->hasOne('ApiV2\Model\UserDetails', 'id', 'posted_by')->select('id', 'username');
    }

    public function vehicleType()
    {
        return $this->hasOne('ApiV2\Model\VehicleType', 'id', 'vehicle_type_id')->select('id', 'vehicle_type');
    }

    /// hyperlocal post list

    public function getAllRoute()
    {
        return $this->hasMany('ApiV2\Model\IntraHyperRoute','fk_buyer_seller_post_id','id')
                ->where('lkp_service_id', '=', _HYPERLOCAL_);
    }

    public function getAllBuyerRoute()
    {
        return $this->hasMany('ApiV2\Model\IntraHyperRoute','fk_buyer_seller_post_id','id')
                ->where('lkp_service_id', '=', _HYPERLOCAL_)
                ->where('is_seller_buyer', '=', 1);
    }

    public function quoteHyper() 
    {
       return $this->hasOne('ApiV2\Model\IntraHyperQuotaion', 'post_id', 'id')->where('lkp_service_id', '=', _HYPERLOCAL_);
    }

    public function getuser()
    {
        return $this->hasOne('ApiV2\Model\UserDetails', 'id', 'posted_by');
    }
    

    function makeRecommender()
    {
        return new IntraHyperBuyerSellerPostRecommender();
    }

    public function quotation() 
    {
       return $this->hasMany('ApiV2\Model\IntraHyperQuotaion','post_id','id')
                ->where('lkp_service_id', '=', _HYPERLOCAL_);
    }



    /** For Settings Notification Data */
    public static function getSettingsData($request) 
    {
        $fetchedRow = $request->totalRow + $request->offset;
         $payload  = IntraHyperRoute::with(['post' => function($query) use ($request) {
                static::applyFilterbuyer($query, $request);
            }])
            ->with(['quotes.postedto', 'quotes.contract','city','fromLocalities','toLocalities','post'])
            ->where(function($query) use ($request) {                
                $query->where('lkp_service_id','=',_HYPERLOCAL_);
                //$query->where('id','=',101);
                if(isset($request->postId) && !empty($request->postId)) {
                    $query->where('id','=',EncrptionTokenService::idDecrypt($request->postId));
                }
            })  
            ->orderBy('id', 'desc')     
            ->limit($fetchedRow)         
            ->get();
            
        return response()->json([
            "payload" => EncrptionTokenService::encrypt($payload)
        ]);
    }


    /** */
    public static function getNavData($id,$roleId)
    {

        $main = ['Domestic Logistics','International Logistics','Asset Lease',
                'Customized Logistics','Port Marine','Asset Maintenance','Blue Collar'];

                
        $services = CommonComponents::sellerBuyerServiceid($id,$roleId);        
        $s_ids = explode(",", $services[0]->Serviceid);

        for($i=0;$i < count($main); $i++) {
            $menu_list[$i]['name']=$main[$i];
            $services = DB::table('lkp_services')->select('*')->where('left_nav_group','=', $main[$i])->get();
            
            
            foreach ($services as $key => $value) {
                
                $bu = DB::table('lkp_service_urls')
                        ->select('*')->where('serviceId','=',$value->id)
                        ->where('usertype','=',$roleId)
                        ->where('type','=','search')->first(); 

                
                $menu_list[$i]['groups'][$key]['serviceId']=$value->id;
                $menu_list[$i]['groups'][$key]['name']=$value->service_name;
                $menu_list[$i]['groups'][$key]['imagePath']=$value->service_image_path;
                if($bu){ 
                    if(in_array($menu_list[$i]['groups'][$key]['serviceId'],$s_ids)) { 

                      $menu_list[$i]['groups'][$key]['url']=$bu->url;
                      
                      }else{
                          $menu_list[$i]['groups'][$key]['url']=""; 
                    }          
                }   
                else{  
                    $menu_list[$i]['groups'][$key]['url']="";
                }  
            }
        } 
        return $menu_list;
    }    

    public static function getPopupMenuData($id,$roleId,$menutype)
    {

        $main = ['Domestic Logistics','International Logistics','Asset Lease',
                'Customized Logistics','Port Marine','Asset Maintenance','Blue Collar'];

                
        $services = CommonComponents::sellerBuyerServiceid($id,$roleId);        
        $s_ids = explode(",", $services[0]->Serviceid);

        for($i=0;$i < count($main); $i++) {
            $menu_list[$i]['name']=$main[$i];
            $services = DB::table('lkp_services')->select('*')->where('left_nav_group','=', $main[$i])->get();
            
            
            foreach ($services as $key => $value) {
                
                $bu = DB::table('lkp_service_urls')
                        ->select('*')
                        ->where('usertype','=',$roleId)
                        ->where('type','=',$menutype)->first(); 

                
                $menu_list[$i]['groups'][$key]['serviceId']=$value->id;
                $menu_list[$i]['groups'][$key]['name']=$value->service_name;
                $menu_list[$i]['groups'][$key]['imagePath']=$value->service_image_path;
                if($bu){ 
                    if(in_array($menu_list[$i]['groups'][$key]['serviceId'],$s_ids)) { 

                      $menu_list[$i]['groups'][$key]['url']=$bu->url;
                      
                      }else{
                          $menu_list[$i]['groups'][$key]['url']=""; 
                    }          
                }   
                else{  
                    $menu_list[$i]['groups'][$key]['url']="";
                }  
            }
        } 
        return $menu_list;
    }

   

}
