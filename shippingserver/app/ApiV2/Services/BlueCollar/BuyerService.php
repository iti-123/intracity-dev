<?php

namespace ApiV2\Services\BlueCollar;

use Log;
use ApiV2\Model\BlueCollar\SellerRegExperience;
use ApiV2\Model\BlueCollar\SellerRegQualif;
use ApiV2\Model\BlueCollar\SellerRegistration;
use ApiV2\Model\BlueCollar\CityModel;
use ApiV2\Model\BlueCollar\Post;
use ApiV2\Model\BlueCollar\Quote;
use ApiV2\Model\BlueCollar\PostVehMach;
use ApiV2\Model\BlueCollar\PostAccessList;
use ApiV2\Model\UserDetails;
use Tymon\JWTAuth\Facades\JWTAuth;
use Storage;
use DB;
use ApiV2\Services\LogistiksCommonServices\SolrServices;
use Illuminate\Support\Facades\Crypt;

use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;
use ApiV2\Services\NotificationService;

class BuyerService extends BaseServiceProvider
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

    public static function post($request,$status){
      $input = $request->all();
      $userID = JWTAuth::parseToken()->getPayload()->get('id');
      $user_data = UserDetails::where('id', '=', $userID)->first();
      //dd($input);
      foreach($input as $key => $val){
        $post = new Post();
        
        $post->posted_by = $userID;
        $post->post_type = 'BUYER_POST';
        $post->profile_type = $val['profileType'];
        $post->salary = $val['salary'];
        $post->salary_type = $val['salaryType'];
        $post->experience = $val['experience'];
        $post->city_id = $val['location']['city_id'];
        $post->district_id = $val['location']['district_id'];
        $post->state_id = $val['location']['state_id'];
        $post->employment_type = $val['employmentTypes'];
        // $post->vehicle_type = self::arrayToString($request->vehicleType);
        $post->qualification = $val['qualifications'];
        $post->privacy = $val['privacy'];
        $post->quotation_type = $val['quotationType'];

        if($status == 1){
          $post->status = 'ACTIVE';  
        }elseif($status == 0){
          $post->status = 'INACTIVE';
          self::$data['success'] = true;
          self::$data['status'] = 200;  
        }
        $post->save();
        
        if($val['profileType'] == 'DRIVER'){
            $vehMach = new PostVehMach();
            $vehMach->vm_id = $val['vehicleTypePH'];
            $post->vehMach()->save($vehMach);
        }else if($val['profileType'] == 'SKILLED'){
          $vehMach = new PostVehMach();
          $vehMach->vm_id = $val['machineTypePH'];
          $post->vehMach()->save($vehMach);
        }
         
           
        if($val['privacy'] == 'PRIVATE'){
          foreach ($val['sellers'] as $key => $value) {
            $al = new PostAccessList();
            $al->bc_reg_id = $value["id"];
            $post->accessList()->save($al);
          }
          self::$data['success'] = true;
          self::$data['status'] = 200;
        }elseif($status == 1){
          $qualification = array();
          $vehicleName = array();
          $employmentType = array();

          $qualification = $val['qualifications'];
            
          if($val['profileType'] == 'DRIVER'){
             $vehName = DB::table('bluecollar_vehicle_machine_types')
                  ->select(DB::raw('name'))
                  ->where('id','=',$val['vehicleTypePH'])
                  ->first();
             $vehicleName = $vehName->name;
          }else if($val['profileType'] == 'SKILLED'){
            $macName = DB::table('bluecollar_vehicle_machine_types')
                  ->select(DB::raw('name'))
                  ->where('id','=',$val['machineTypePH'])
                  ->first();
             $vehicleName = $macName->name;
          }
          
          $employmentType = $val['employmentTypes'];
          if(strpos($user_data->username, ' ')){
            list($first_name, $last_name) = explode(' ', $user_data->username, 2);
          }else{
            $first_name = $user_data->username;
            $last_name = '';
          }
          $solrData = array(
            "id" => $post->id,
            "buyer_first_name"=> $first_name,
            "buyer_last_name"=> $last_name,
            "buyer_profile_type"=> $post->profile_type,
            "buyer_city"=> $post->city_id,
            "buyer_state"=> $post->state_id,
            "buyer_district"=> $post->district_id,
            "buyer_available"=>"true",
            "buyer_salary"=>$post->salary,
            "buyer_experience"=>$post->experience,
            "buyer_salary_type"=>$post->salary_type,
            "buyer_quotation_type"=>$post->quotation_type
          );

          if(!empty($qualification))
            $solrData["buyer_qualification"]=$qualification;
          if(!empty($vehicleName))
            $solrData["buyer_vehicle_type"]=$vehicleName;
          if(!empty($employmentType))
            $solrData["buyer_employment_type"]=$employmentType;

          $response = SolrServices::add('bluecollar', $solrData);
          if(!isset($response->error)){
            self::$data['success'] = true;
            self::$data['status'] = 200;
          }else{
            self::$data['success'] = false;
            self::$data['status'] = 500;
          }
        }
      }
      $post->title = 'Buyer post';
      $post->service = _BLUECOLLAR_;
      $post->post_transaction_id = NumberGeneratorServices::generateTranscationId(new Post, _BLUECOLLAR_);
      NotificationService::createNotification($post);

      return self::$data;
    }

    public static function arrayToString($arr){
      if(!empty($arr)){
        $s = "";
        foreach($arr as $lKey=>$value){
          if($lKey>0){
              $s .= ",";
          }
          $s .= "{$value}";
        }
        return $s;
      }
    }

    public static function postList($request){
      $input = $request->all();
      //return $input;
      $userID = JWTAuth::parseToken()->getPayload()->get('id');
      $post = Post::with(['quote', 'quote.sellerData', 'vehMach', 'postedBy', 'city', 'district', 'state']);
      // $post = Post::where('profile_type', '=', 'CLEANER')->paginate(10);
      $post = self::applyFilters($post, $input, $userID);
      $post = $post->paginate($input['pageLoader']);

      self::$data['data'] = $post;
      self::$data['success'] = true;
      return self::$data;
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

    public static function getVehMachTypes($arr){
      if(!empty($arr)){
        $retArr = array();
        foreach ($arr as $key => $value) {
          $retArr[] = $value["name"];
        }
        return $retArr;
      }
    }

    public static function boundCount(){
      $userID = JWTAuth::parseToken()->getPayload()->get('id');
      $inbound = Quote::where('buyer_id', '=', $userID)
                  ->where('lkp_service_id', '=', _BLUECOLLAR_)
                  ->where(function($query){
                    $query->where('buyer_status', '=', null)
                          ->orWhere('buyer_status', '=', 'COUNTER');
                  })
                  ->where(function($query){
                    $query->where('seller_status', '=', 'OFFER')
                          ->orWhere('seller_status', '=', 'COUNTER');
                  })
                  ->count();
                  // ->toSql();
      // $outbound = Post::where('post_type', '=', 'BUYER_POST')
      // ->where('posted_by', '=', $userID)
      // ->count();
      //
      // $outbound = $outbound-$inbound;

     // $outbound = Quote::where('buyer_id', '=', $userID)
              //    ->where('lkp_service_id', '=', _BLUECOLLAR_)
              //    ->where(function($query){
              //      $query->where('buyer_status', '=', 'ACCEPT')
              //            ->orWhere('buyer_status', '=', 'DENY');
              //    })
              //    ->orWhere(function($query){
               //     $query->where('seller_status', '=', 'ACCEPT')
               //           ->orWhere('seller_status', '=', 'DENY');
             //     })
                //  ->count();

      $outbound = Post::with(['quote','accessList'])
                  ->whereHas('quote',function($query) use ($userID){
                       $query->where(function($q) use ($userID){
                          $q->where('buyer_id', '=', $userID)
                             ->where('lkp_service_id', '=', _BLUECOLLAR_)
                             ->where('buyer_status', '=', 'ACCEPT')
                             ->orWhere('buyer_status', '=', 'DENY');
                      });
                      $query->orWhere(function($q){
                          $q->where('seller_status', '=', 'ACCEPT')
                            ->orWhere('seller_status', '=', 'DENY');
                      });
                    })
                  ->orWhereHas('accessList',function($query) use ($userID){
                       $query->where('posted_by', '=', $userID);
                  })
                  ->count();

      $arr = array('inbound'=>$inbound, 'outbound'=>$outbound);
      self::$data['data'] = $arr;
      self::$data['status'] = 200;
      self::$data['success'] = true;
      return self::$data;
    }

    public static function inboundList($request){
      $input = $request->all();
      $userID = JWTAuth::parseToken()->getPayload()->get('id');
      $post = Post::with(['quote', 'quote.sellerData', 'vehMach', 'postedBy', 'city', 'district', 'state'])
              ->whereHas('quote', function($query) use($userID){
                  $query->where('buyer_id', '=', $userID);
                  $query->where(function($query){
                    $query->where('buyer_status', '=', null)
                          ->orWhere('buyer_status', '=', 'COUNTER');
                  });
                  $query->where(function($query){
                    $query->where('seller_status', '=', 'OFFER')
                          ->orWhere('seller_status', '=', 'COUNTER');
                  });
                });
      $post = self::applyFilters($post, $input, $userID);
      self::$data['data'] = $post;
      self::$data['success'] = true;
      return self::$data;
    }

    public static function outboundList($request){
      $input = $request->all();
      $userID = JWTAuth::parseToken()->getPayload()->get('id');
      $post = Post::with(['quote.sellerData', 'vehMach', 'postedBy', 'city', 'district', 'state'])
              ->whereHas('quote', function($query) use($userID){
                  $query->where('buyer_id', '=', $userID);
                  $query->where(function($query){
                    $query->where('buyer_status', '=', 'ACCEPT')
                          ->orWhere('buyer_status', '=', 'DENY');
                  });
                  $query->orWhere(function($query){
                    $query->where('seller_status', '=', 'ACCEPT')
                          ->orWhere('seller_status', '=', 'DENY');
                  });
                })
              ->orWhereHas('accessList',function($query) use ($userID){
                    $query->where('posted_by','=',$userID);
                });
             
      $post = self::applyFilters($post, $input, $userID);
      self::$data['data'] = $post;
      self::$data['success'] = true;
      return self::$data;
    }

    public static function applyFilters($post, $input, $userID){
      $vmType = array();
      if(isset($input['vehicleType']) && isset($input['machineType'])){
        $vmType = array_merge($input['vehicleType'], $input['machineType']);
      }

      if(isset($input['profileType']) && !empty($vmType)){
        if(in_array('DRIVER', $input['profileType'])||in_array('SKILLED', $input['profileType'])){
          $post->whereHas('vehMach', function($query) use($vmType){
            foreach ($vmType as $key => $value) {
              if($key==0){
                $query->where('vm_id', '=', $value);
              }else{
                $query->orWhere('vm_id', '=', $value);
              }
            }
          });
        }
      }
      return $post->where('posted_by', '=', $userID)
      ->where('post_type', '=', 'BUYER_POST')
      //->where('status', '=', 'ACTIVE')
      ->where(function($query) use($input){
        if(isset($input['location']) && !empty($input['location'])){
          $query->where('city_id', '=', $input['location']['city_id'])
                ->where('district_id', '=', $input['location']['district_id'])
                ->where('state_id', '=', $input['location']['state_id']);
        }
      })
      ->where(function($query) use($input){
        BuyerService::postQueryBuilder($query, $input, 'profileType', 'profile_type', '=');
      })
      ->where(function($query) use($input){
        BuyerService::postQueryBuilder($query, $input, 'employmentType', 'employment_type', 'LIKE');
      })
      ->where(function($query) use($input){
        BuyerService::postQueryBuilder($query, $input, 'salaryType', 'salary_type', 'LIKE');
      })
      ->where(function($query) use($input){
        BuyerService::postQueryBuilder($query, $input, 'qualification', 'qualification', 'LIKE');
      })
      ->where(function($query) use($input){
        BuyerService::postQueryBuilder($query, $input, 'status', 'status', '=');
      })
      ->orderBy('id', 'DESC');
     // ->paginate(self::$pageDataCount);
      // ->toSql();
    }

    public static function quoteSubmit($request){
      $userID = JWTAuth::parseToken()->getPayload()->get('id');
      $quote = Quote::where('id', '=', $request->quoteId)
                    ->where('lkp_service_id', '=', _BLUECOLLAR_)
                    ->where('buyer_id', '=', $userID)
                    ->where('post_id', '=', $request->postId)
                    ->first();

      $quote->buyer_counter_transit_days = $request->quoteDays;
      $quote->buyer_quote_price = $request->quotePrice;
      $quote->buyer_status = 'COUNTER';
      $quote->save();
      self::$data['data'] = $quote;
      self::$data['success'] = true;
      self::$data['status'] = 200;
      return self::$data;
    }

    public static function quoteAccept($request){
      $userID = JWTAuth::parseToken()->getPayload()->get('id');
      $quote = Quote::where('id', '=', $request->quoteId)
                    ->where('lkp_service_id', '=', _BLUECOLLAR_)
                    ->where('buyer_id', '=', $userID)
                    ->where('post_id', '=', $request->postId)
                    ->where(function($query){
                      $query->where('buyer_status', '=', null)
                      ->orWhere('buyer_status', '=', 'COUNTER');
                    })
                    ->where(function($query){
                      $query->where('seller_status', '=', 'OFFER')
                      ->orWhere('seller_status', '=', 'COUNTER');
                    })
                    ->first();

      $quote->buyer_status = 'ACCEPT';
      $quote->save();
      self::$data['data'] = $quote;
      self::$data['success'] = true;
      self::$data['status'] = 200;
      return self::$data;
    }

    public static function quoteDeny($request){
      $userID = JWTAuth::parseToken()->getPayload()->get('id');
      $quote = Quote::where('id', '=', $request->quoteId)
                    ->where('lkp_service_id', '=', _BLUECOLLAR_)
                    ->where('buyer_id', '=', $userID)
                    ->where('post_id', '=', $request->postId)
                    ->where(function($query){
                      $query->where('buyer_status', '=', null)
                      ->orWhere('buyer_status', '=', 'COUNTER');
                    })
                    ->where(function($query){
                      $query->where('seller_status', '=', 'OFFER')
                      ->orWhere('seller_status', '=', 'COUNTER');
                    })
                    ->first();

      $quote->buyer_status = 'DENY';
      $quote->save();
      self::$data['data'] = $quote;
      self::$data['success'] = true;
      self::$data['status'] = 200;
      return self::$data;
    }

    public static function sellerSearch($request){
      $results = SellerRegistration::
                  where('first_name', 'LIKE', '%'.$request->search.'%')
                  ->orWhere('last_name', 'LIKE', '%'.$request->search.'%')
                  ->select('id', 'first_name', 'last_name')
                  ->limit(self::$pageDataCount)
                  ->get();
                  // ->toSql();
      self::$data['data'] = $results;
      self::$data['status'] = 200;
      self::$data['success'] = true;
      return self::$data;
    }

}
