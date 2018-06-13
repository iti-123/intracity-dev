<?php

namespace Api\Services\BlueCollar;

use Log;
use Api\Model\BlueCollar\SellerRegExperience;
use Api\Model\BlueCollar\SellerRegQualif;
use Api\Model\BlueCollar\SellerRegistration;
use Api\Model\BlueCollar\CityModel;
use Api\Model\BlueCollar\Post;
use Api\Model\BlueCollar\Quote;
use Api\Model\BlueCollar\PostVehMach;
use Api\Model\BlueCollar\PostAccessList;
use Api\Model\UserDetails;
use Tymon\JWTAuth\Facades\JWTAuth;
use Storage;
use DB;
use Api\Services\LogistiksCommonServices\SolrServices;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class SellerService extends BaseServiceProvider
{
    private static $pageDataCount = 20;
    public static function locationSearch($request){
      $searchTerm = $request->search;
      $suggestions = DB::table('bluecollar_seller_registration')
                        ->join('lkp_cities', 'lkp_cities.id', '=', 'bluecollar_seller_registration.cur_city_id')
                        ->join('lkp_districts', 'lkp_districts.id', '=', 'bluecollar_seller_registration.cur_district_id')
                        ->join('lkp_states', 'lkp_states.id', '=', 'bluecollar_seller_registration.cur_state_id')
                        ->where('bluecollar_seller_registration.verified', '=', 'YES')
                        ->orWhere('lkp_cities.city_name', 'LIKE', '%'.$searchTerm.'%')
                        ->orWhere('lkp_districts.district_name', 'LIKE', '%'.$searchTerm.'%')
                        ->orWhere('lkp_states.state_name', 'LIKE', '%'.$searchTerm.'%')
                        ->groupBy('lkp_cities.id')
                        ->select('lkp_cities.id as city_id', 'lkp_cities.city_name as city_name', 'lkp_states.id as state_id', 'lkp_states.state_name as state_name', 'lkp_districts.id as district_id', 'lkp_districts.district_name as district_name')
                        ->limit(10)
                        ->get();

        self::$data['data'] = $suggestions;
        self::$data['success'] = true;
        self::$data['status'] = 200;
        return self::$data;
    }

    public static function post($request, $status)
    {
        $input = $request->all();
        //dd($input);
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $bc_reg_id = SellerRegistration::where('created_by', '=', $userID)->first()->id;
        $user_data = UserDetails::where('id', '=', $userID)->first();

        foreach ($input as $key => $val) {
            $post = new Post();

            $post->posted_by = $userID;
            $post->post_type = 'RATE_CARD';
            $post->bc_reg_id = $bc_reg_id;
            $post->profile_type = $val['profileType'];
            $post->salary = $val['salary'];
            $post->salary_type = $val['salaryType'];
            $post->experience = $val['experience'];
            $post->city_id = $val['location']['city_id'];
            $post->district_id = $val['location']['district_id'];
            $post->state_id = $val['location']['state_id'];
            $post->employment_type = $val['employmentType'];
            // $post->vehicle_type = self::arrayToString($request->vehicleType);
            $post->qualification = $val['qualification'];

            if ($status == 1) {
                $post->status = 'ACTIVE';
            } elseif ($status == 0) {
                $post->status = 'INACTIVE';
            }

            $post->save();

            if ($val['profileType'] == 'DRIVER') {
                $vehMach = new PostVehMach();
                $vehMach->vm_id = $val['vehicleTypePH'];
                $post->vehMach()->save($vehMach);
            } else if ($val['profileType'] == 'SKILLED') {
                $vehMach = new PostVehMach();
                $vehMach->vm_id = $val['machineTypePH'];
                $post->vehMach()->save($vehMach);
            }

            if ($status == 1) {
                $qualification = array();
                $vehicleName = array();
                $employmentType = array();

                $qualification = $val['qualification'];

                if ($val['profileType'] == 'DRIVER') {
                    $vehName = DB::table('bluecollar_vehicle_machine_types')
                        ->select(DB::raw('name'))
                        ->where('id', '=', $val['vehicleTypePH'])
                        ->first();
                    $vehicleName = $vehName->name;
                } else if ($val['profileType'] == 'SKILLED') {
                    $macName = DB::table('bluecollar_vehicle_machine_types')
                        ->select(DB::raw('name'))
                        ->where('id', '=', $val['machineTypePH'])
                        ->first();
                    $vehicleName = $macName->name;
                }

                $employmentType = $val['employmentType'];

                if (strpos($user_data->username, ' ')) {
                    list($first_name, $last_name) = explode(' ', $user_data->username, 2);
                } else {
                    $first_name = $user_data->username;
                    $last_name = '';
                }
                $solrData = array(
                    "id" => $post->id,
                    "seller_first_name" => $first_name,
                    "seller_last_name" => $last_name,
                    "seller_profile_type" => $post->profile_type,
                    "seller_city" => $post->city_id,
                    "seller_state" => $post->state_id,
                    "seller_district" => $post->district_id,
                    "seller_available" => "true",
                    "seller_salary" => $post->salary,
                    "seller_experience" => $post->experience,
                    "seller_salary_type" => $post->salary_type,
                    "seller_bc_reg_id" => $bc_reg_id,
                );

                if (!empty($qualification))
                    $solrData["seller_qualification"] = $qualification;
                if (!empty($vehicleName))
                    $solrData["seller_vehicle_type"] = $vehicleName;
                if (!empty($employmentType))
                    $solrData["seller_employment_type"] = $employmentType;

                $response = SolrServices::add('bluecollar', $solrData);
                if (!isset($response->error)) {
                    self::$data['success'] = true;
                    self::$data['status'] = 200;
                } else {
                    self::$data['success'] = false;
                    self::$data['status'] = 500;
                }
            } else if ($status == 0) {
                self::$data['success'] = true;
                self::$data['status'] = 200;
            }
        }

        return self::$data;
    }

    public static function arrayToString($arr)
    {
        if (!empty($arr)) {
            $s = "";
            foreach ($arr as $lKey => $value) {
                if ($lKey > 0) {
                    $s .= ",";
                }
                $s .= "{$value}";
            }
            return $s;
        }
    }








    public static function postList($request)
    {

        //return "hello";
        $input = $request->all();
        //echo  $input['pageLoader'];
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $post = Post::with(['quote', 'quote.sellerData', 'vehMach', 'postedBy', 'city', 'district', 'state']);
        // $post = Post::where('profile_type', '=', 'CLEANER')->paginate(10);
        $post = self::applyFilters($post, $input, $userID);
        //$post = $post->paginate($input['pageLoader']);
//return  $post;
        self::$data['data'] = $post;
        self::$data['success'] = true;
        return self::$data;
    }

    public static function applyFilters($post, $input, $userID)
    {
        $vmType = array();
        if (isset($input['vehicleType']) && isset($input['machineType'])) {
            $vmType = array_merge($input['vehicleType'], $input['machineType']);
        }
        if (isset($input['profileType'])) {
            if (in_array('DRIVER', $input['profileType']) || in_array('SKILLED', $input['profileType'])) {
                $post->whereHas('vehMach', function ($query) use ($vmType) {
                    foreach ($vmType as $key => $value) {
                        if ($key == 0) {
                            $query->where('vm_id', '=', $value);
                        } else {
                            $query->orWhere('vm_id', '=', $value);
                        }
                    }
                });
            }
        }
        return $post->where('posted_by', '=', $userID)
            ->where('post_type', '=', 'RATE_CARD')
            ->where('status', '=', 'ACTIVE')
            ->where(function ($query) use ($input) {
                if (isset($input['location']) && !empty($input['location'])) {
                    $query->where('city_id', '=', $input['location']['city_id'])
                        ->where('district_id', '=', $input['location']['district_id'])
                        ->where('state_id', '=', $input['location']['state_id']);
                }
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'profileType', 'profile_type', '=');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'employmentType', 'employment_type', 'LIKE');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'salaryType', 'salary_type', 'LIKE');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'qualification', 'qualification', 'LIKE');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'status', 'status', '=');
            })
            ->orderBy('id', 'DESC')->paginate(self::$pageDataCount);
            // ->toSql();

    }

    public static function outboundList($request)
    {
        $input = $request->all();
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $post = Post::with(['quote', 'quote.sellerData', 'vehMach', 'postedBy', 'city', 'district', 'state'])
            ->whereHas('quote', function ($query) use ($userID) {
                $query->where('seller_id', '=', $userID);
            });

        $post = self::applyFiltersoutboundList($post, $input, $userID);
        self::$data['data'] = $post;
        self::$data['success'] = true;
        return self::$data;
    }

    public static function applyFiltersoutboundList($post, $input, $userID)
    {
        $vmType = array();
        if (isset($input['vehicleType']) && isset($input['machineType'])) {
            $vmType = array_merge($input['vehicleType'], $input['machineType']);
        }
        if (isset($input['profileType'])) {
            if (in_array('DRIVER', $input['profileType']) || in_array('SKILLED', $input['profileType'])) {
                $post->whereHas('vehMach', function ($query) use ($vmType) {
                    foreach ($vmType as $key => $value) {
                        if ($key == 0) {
                            $query->where('vm_id', '=', $value);
                        } else {
                            $query->orWhere('vm_id', '=', $value);
                        }
                    }
                });
            }
        }
        return $post->where('post_type', '=', 'BUYER_POST')
            ->where('status', '=', 'ACTIVE')
            ->where(function ($query) use ($input) {
                if (isset($input['location']) && !empty($input['location'])) {
                    $query->where('city_id', '=', $input['location']['city_id'])
                        ->where('district_id', '=', $input['location']['district_id'])
                        ->where('state_id', '=', $input['location']['state_id']);
                }
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'profileType', 'profile_type', '=');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'employmentType', 'employment_type', 'LIKE');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'salaryType', 'salary_type', 'LIKE');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'qualification', 'qualification', 'LIKE');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'status', 'status', '=');
            })
            ->orderBy('id', 'DESC')
            // ->toSql();
            ->paginate(self::$pageDataCount);
    }

    public static function inboundList($request)
    {
        $input = $request->all();
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $bc_reg_id = SellerRegistration::where('created_by', '=', $userID)->first()->id;

        $post = Post::with(['accessList', 'vehMach', 'postedBy', 'city', 'district', 'state'])
            ->whereHas('accessList', function ($query) use ($bc_reg_id) {
                $query->where('bc_reg_id', '=', $bc_reg_id);
            });

        $post = self::applyFiltersinboundList($post, $input, $userID);
        self::$data['data'] = $post;
        self::$data['success'] = true;
        return self::$data;
    }

    public static function applyFiltersinboundList($post, $input, $userID)
    {
        $vmType = array();
        if (isset($input['vehicleType']) && isset($input['machineType'])) {
            $vmType = array_merge($input['vehicleType'], $input['machineType']);
        }
        if (isset($input['profileType'])) {
            if (in_array('DRIVER', $input['profileType']) || in_array('SKILLED', $input['profileType'])) {
                $post->whereHas('vehMach', function ($query) use ($vmType) {
                    foreach ($vmType as $key => $value) {
                        if ($key == 0) {
                            $query->where('vm_id', '=', $value);
                        } else {
                            $query->orWhere('vm_id', '=', $value);
                        }
                    }
                });
            }
        }
        return $post->where('post_type', '=', 'BUYER_POST')
            ->where('privacy', '=', 'PRIVATE')
            ->where('status', '=', 'ACTIVE')
            ->where(function ($query) use ($input) {
                if (isset($input['location']) && !empty($input['location'])) {
                    $query->where('city_id', '=', $input['location']['city_id'])
                        ->where('district_id', '=', $input['location']['district_id'])
                        ->where('state_id', '=', $input['location']['state_id']);
                }
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'profileType', 'profile_type', '=');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'employmentType', 'employment_type', 'LIKE');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'salaryType', 'salary_type', 'LIKE');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'qualification', 'qualification', 'LIKE');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'status', 'status', '=');
            })
            ->orderBy('id', 'DESC')
            // ->toSql();
            ->paginate(self::$pageDataCount);
    }

    public static function postQueryBuilder($query, $input, $inKey, $dbKey, $operator){
      if(isset($input[$inKey])){
        foreach ($input[$inKey] as $key=>$value) {
          if($operator=='LIKE'){
            $value = '%'.$value.'%';
          }
          if($key==0){
            $query->where($dbKey, $operator,$value);
          }else{
            $query->orWhere($dbKey, $operator,$value);
          }
        }
      }
    }

    public static function getVehMachTypes($arr)
    {
        if (!empty($arr)) {
            $retArr = array();
            foreach ($arr as $key => $value) {
                $retArr[] = $value["name"];
            }
            return $retArr;
        }
    }

    public static function sellerQuoteInitialising($request)
    {
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $quote = Quote::where('post_id', '=', $request->postId)->where('seller_id', '=', $userID)->count();
        if ($quote == 0) {
            $post = Post::where('id', '=', $request->postId)->first();
            $quote = new Quote();
            $quote->lkp_service_id = 23;
            $quote->buyer_id = $post->posted_by;
            $quote->seller_id = $userID;

            if ($request->quotationType == 'COMPETITIVE') {
                $quote->quotation_type = 'COMPETITIVE';
                $quote->initial_quote_price = $request->quotePrice;
            } else {
                $quote->quotation_type = 'FIRM';
            }

            $quote->transit_day = $request->quoteDays;
            $quote->seller_status = 'OFFER';
            $quote->seller_quote_at = Carbon::now();
            $post->quote()->save($quote);
            self::$data['data'] = $quote->id;
            self::$data['status'] = 200;
            self::$data['success'] = true;
        } else {
            self::$data['data'] = 'Already Exits';
            self::$data['status'] = 500;
            self::$data['success'] = false;
        }
        return self::$data;
    }

    public static function sellerQuoteCounter($request)
    {
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $quote = Quote::where('id', '=', $request->quoteId)->first();
        self::$data['data'] = 'Cannot Counter';
        self::$data['success'] = false;
        self::$data['status'] = 'fail';
        if ($quote != null) {
            if ($quote->buyer_status = 'COUNTER') {
                $quote->seller_quote_price = $request->quotePrice;
                $quote->seller_final_transit_days = $request->quoteDays;
                $quote->seller_status = 'COUNTER';
                $quote->save();
                self::$data['data'] = $quote;
                self::$data['status'] = 'success';
                self::$data['success'] = true;
            }
        }
        return self::$data;
    }

    public static function sellerQuoteCounter1($request)
    {
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $quote = Quote::where('id', '=', $request->quoteId)->first();
        self::$data['data'] = 'Cannot Counter';
        self::$data['success'] = false;
        self::$data['status'] = 'fail';
        if ($quote != null) {
            if ($quote->buyer_status = 'COUNTER' && $quote->seller_status = 'OFFER') {
                $quote->seller_quote_price = $request->quotePrice;
                $quote->seller_final_transit_days = $request->quoteDays;
                $quote->seller_status = 'COUNTER';
                $post->quote()->save($quote);
                self::$data['data'] = $quote->id;
                self::$data['status'] = 'success';
                self::$data['success'] = true;
            }
        }
        return self::$data;
    }

    public static function sellerQuoteAccept($request)
    {
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $quote = Quote::where('id', '=', $request->quoteId)
            ->where('lkp_service_id', '=', 23)
            ->where('seller_id', '=', $userID)
            ->where('post_id', '=', $request->postId)
            ->where(function ($query) {
                $query->where('buyer_status', '=', 'COUNTER');
            })
            ->where(function ($query) {
                $query->where('seller_status', '=', 'OFFER')
                    ->orWhere('seller_status', '=', 'COUNTER');
            })
            ->first();

        $quote->seller_status = 'ACCEPT';
        $quote->save();
        self::$data['data'] = $quote;
        self::$data['success'] = true;
        self::$data['status'] = 200;
        return self::$data;
    }

    public static function sellerQuoteDeny($request)
    {
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $quote = Quote::where('id', '=', $request->quoteId)
            ->where('lkp_service_id', '=', 23)
            ->where('seller_id', '=', $userID)
            ->where('post_id', '=', $request->postId)
            ->where(function ($query) {
                $query->where('buyer_status', '=', 'COUNTER');
            })
            ->where(function ($query) {
                $query->where('seller_status', '=', 'OFFER')
                    ->orWhere('seller_status', '=', 'COUNTER');
            })
            ->first();

        $quote->seller_status = 'DENY';
        $quote->save();
        self::$data['data'] = $quote;
        self::$data['success'] = true;
        self::$data['status'] = 200;
        return self::$data;
    }

    public static function boundCount()
    {
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $bc_reg_id = SellerRegistration::where('created_by', '=', $userID)->first()->id;
        $outbound = Quote::where('seller_id', '=', $userID)
            ->where('lkp_service_id', '=', 23)
            ->count();

        $inbound = PostAccessList::where('bc_reg_id', '=', $bc_reg_id)
            ->count();
        // ->toSql();
        $arr = array('inbound' => $inbound, 'outbound' => $outbound);
        self::$data['data'] = $arr;
        self::$data['status'] = 200;
        self::$data['success'] = true;
        return self::$data;
    }

    public static function outboundList_ankit($request)
    {
        $input = $request->all();
        $pageDataCount = 20;
        $vmType = array();
        if (isset($input['vehicleType']) && isset($input['machineType'])) {
            $vmType = array_merge($input['vehicleType'], $input['machineType']);
        }
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $post = Post::
        with(['quote', 'quote.sellerData', 'vehMach', 'postedBy', 'city', 'district', 'state'])
            ->whereHas('vehMach', function ($query) use ($vmType) {
                foreach ($vmType as $key => $value) {
                    if ($key == 0) {
                        $query->where('vm_id', '=', $value);
                    } else {
                        $query->orWhere('vm_id', '=', $value);
                    }
                }
            })
            ->whereHas('quote', function ($query) use ($userID) {
                $query->where('seller_id', '=', $userID);
            })
            ->where('post_type', '=', 'BUYER_POST')
            ->where('status', '=', 'ACTIVE')
            ->where(function ($query) use ($input) {
                if (isset($input['location']) && !empty($input['location'])) {
                    $query->where('city_id', '=', $input['location']['city_id'])
                        ->where('district_id', '=', $input['location']['district_id'])
                        ->where('state_id', '=', $input['location']['state_id']);
                }
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'profileType', 'profile_type', '=');
            })
            // ->where(function($query) use($input){
            //   BuyerService::postQueryBuilder($query, $input, 'vehicleType', 'vehicle_type', 'LIKE');
            // })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'employmentType', 'employment_type', 'LIKE');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'salaryType', 'salary_type', 'LIKE');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'qualification', 'qualification', 'LIKE');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'status', 'status', '=');
            })
            ->orderBy('id', 'DESC')
            ->paginate($pageDataCount);
        self::$data['data'] = $post;
        self::$data['success'] = true;
        return self::$data;
    }

    public static function inboundList_ankit($request)
    {
        $input = $request->all();
        $pageDataCount = 20;
        $vmType = array();
        if (isset($input['vehicleType']) && isset($input['machineType'])) {
            $vmType = array_merge($input['vehicleType'], $input['machineType']);
        }
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $bc_reg_id = SellerRegistration::where('created_by', '=', $userID)->first()->id;

        $post = Post::
        with(['accessList', 'vehMach', 'postedBy', 'city', 'district', 'state'])
            ->whereHas('vehMach', function ($query) use ($vmType) {
                foreach ($vmType as $key => $value) {
                    if ($key == 0) {
                        $query->where('vm_id', '=', $value);
                    } else {
                        $query->orWhere('vm_id', '=', $value);
                    }
                }
            })
            ->whereHas('accessList', function ($query) use ($bc_reg_id) {
                $query->where('bc_reg_id', '=', $bc_reg_id);
            })
            ->where('privacy', '=', 'PRIVATE')
            ->where('post_type', '=', 'BUYER_POST')
            ->where('status', '=', 'ACTIVE')
            ->where(function ($query) use ($input) {
                if (isset($input['location']) && !empty($input['location'])) {
                    $query->where('city_id', '=', $input['location']['city_id'])
                        ->where('district_id', '=', $input['location']['district_id'])
                        ->where('state_id', '=', $input['location']['state_id']);
                }
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'profileType', 'profile_type', '=');
            })
            // ->where(function($query) use($input){
            //   BuyerService::postQueryBuilder($query, $input, 'vehicleType', 'vehicle_type', 'LIKE');
            // })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'employmentType', 'employment_type', 'LIKE');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'salaryType', 'salary_type', 'LIKE');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'qualification', 'qualification', 'LIKE');
            })
            ->where(function ($query) use ($input) {
                SellerService::postQueryBuilder($query, $input, 'status', 'status', '=');
            })
            ->orderBy('id', 'DESC')
            ->paginate($pageDataCount);
        self::$data['data'] = $post;
        self::$data['success'] = true;
        return self::$data;
    }


    public static function postList1($request)
    {
        $input = $request->all();
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $post = Post::with(['quote', 'quote.sellerData', 'vehMach', 'postedBy', 'city', 'district', 'state']);
        // $post = Post::where('profile_type', '=', 'CLEANER')->paginate(10);
        $post = self::applyFilters($post, $input, $userID);

        self::$data['data'] = $post;
        self::$data['success'] = true;
        return self::$data;
    }

    public static function postQueryBuilder1($query, $input, $inKey, $dbKey, $operator)
    {
        if (isset($input[$inKey])) {
            foreach ($input[$inKey] as $key => $value) {
                if ($operator == 'LIKE') {
                    $value = '%' . $value . '%';
                }
                if ($key == 0) {
                    $query->where($dbKey, $operator, $value);
                } else {
                    $query->orWhere($dbKey, $operator, $value);
                }
            }
        }
    }


    public static function inboundList1($request)
    {
        $input = $request->all();
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $post = Post::with(['quote', 'quote.sellerData', 'vehMach', 'postedBy', 'city', 'district', 'state'])
            ->whereHas('quote', function ($query) use ($userID) {
                $query->where('seller_id', '=', $userID);
                $query->where(function ($query) {
                    $query->where('buyer_status', '=', null)
                        ->orWhere('buyer_status', '=', 'COUNTER');
                });
                $query->where(function ($query) {
                    $query->where('seller_status', '=', 'OFFER')
                        ->orWhere('seller_status', '=', 'COUNTER');
                });
            });
        $post = self::applyFilters($post, $input, $userID);
        self::$data['data'] = $post;
        self::$data['success'] = true;
        return self::$data;
    }

    public static function outboundList1($request)
    {
        $input = $request->all();
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $post = Post::with(['quote.sellerData', 'vehMach', 'postedBy', 'city', 'district', 'state'])
            ->whereHas('quote', function ($query) use ($userID) {
                $query->where('seller_id', '=', $userID);
                $query->where(function ($query) {
                    $query->where('buyer_status', '=', 'ACCEPT')
                        ->orWhere('buyer_status', '=', 'DENY');
                });
                $query->orWhere(function ($query) {
                    $query->where('seller_status', '=', 'ACCEPT')
                        ->orWhere('seller_status', '=', 'DENY');
                });
            });
        // ->with(['quote'=>function($query){
        //   $query->where(function($query){
        //     $query->where('buyer_status', '!=', null)
        //     ->Where('buyer_status', '!=', 'COUNTER');
        //   })
        //   ->where(function($query){
        //     $query->where('seller_status', '!=', 'OFFER')
        //     ->Where('seller_status', '!=', 'COUNTER');
        //   });
        // }]);
        $post = self::applyFilters($post, $input, $userID);
        self::$data['data'] = $post;
        self::$data['success'] = true;
        return self::$data;
    }
}
