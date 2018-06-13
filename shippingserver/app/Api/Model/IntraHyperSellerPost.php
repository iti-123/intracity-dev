<?php
namespace Api\Model;
use DB;
use App\Solr;
use Tymon\JWTAuth\Facades\JWTAuth;
use Api\Model\SellerPostRoute;
use Illuminate\Database\Eloquent\Model;
use Api\Services\LogistiksCommonServices\NumberGeneratorServices;
use Api\Services\LogistiksCommonServices\EncrptionTokenService;
class IntraHyperSellerPost extends Model
{

    private static $rows_fetched = 20;
    protected $fillable = ['rate_cart_type', 'type', 'notes', 'terms_cond','post_type','post_status','  routedata','discount','post_transaction_id'];

    protected $table = 'intra_hp_sellerpost_ratecart';

    public function routes()
    {
        return $this->hasMany('Api\Model\IntraHyperRoute', 'fk_buyer_seller_post_id', 'id')
                    ->where([
                        ['lkp_service_id', '=', _INTRACITY_],
                        ['is_active', '=', 1]                    
                    ]);
    }

    public function seller()
    {
        return $this->hasOne('App\User', 'id', 'posted_by')->select("id","username");
    }

    public static function sellerPost($mixdata) {
      //return response()->json($mixdata);
      
  
    try {

          $userID = JWTAuth::parseToken()->getPayload()->get('id');
      /*******primary table data***********/
          $data=json_decode($mixdata->data);

          // dd($mixdata->discount);
          //dd($data->selectseledata);
          if(self::has($data,'uid')) :
              $id=EncrptionTokenService::idDecrypt($data->uid);
              $IntraHyperSellerPost= IntraHyperSellerPost::find($id);
          else :
             $data->uid='';
             $IntraHyperSellerPost= new IntraHyperSellerPost;
          endif;
          
          $type=$data->type;
          $notes=self::has($data,'notes');
          $post_type= self::has($data,'post_type');
          $type = self::has($data,'type');
          $accept=self::has($data,'accept');

          $IntraHyperSellerPost->rate_cart_type =$type;
          $IntraHyperSellerPost->notes =$notes;
          $IntraHyperSellerPost->is_private_public =$post_type;
          $IntraHyperSellerPost->terms_cond =$accept;
          $IntraHyperSellerPost->posted_by =$userID;
          $IntraHyperSellerPost->is_active =1;
          $IntraHyperSellerPost->lkp_service_id=_INTRACITY_;
          $IntraHyperSellerPost->post_status =$mixdata->status;
          $IntraHyperSellerPost->routedata =$mixdata->addroutedata;

          $IntraHyperSellerPost->discount=$mixdata->discount;

          $IntraHyperSellerPost->assign_buyer=self::has($data,'selectseledata');


        DB::transaction(function() use ($mixdata,$data,$IntraHyperSellerPost) {

           $IntraHyperSellerPost->post_transaction_id=NumberGeneratorServices::generateTranscationId(new IntraHyperSellerPost,_INTRACITY_);
          $IntraHyperSellerPost->save();

          // $ratecardmasterid = DB::table('intra_hp_sellerpost_ratecart')->insertGetId($insertarray);
          $seller_ids='';
          $ratecardmasterid=$IntraHyperSellerPost->id;
       

          if(self::has($data,'post_type') == 1) {
                    $seller_ids = explode(',',self::has($data,'selectseledata'));

                    self::saveBuyer($seller_ids,$ratecardmasterid,$data->uid);
                }


       /**********************/
       /************route and discount**********/

        $addroute=json_decode($mixdata->addroutedata);
        //return $addroute;
        // dd($addroute);
        $route=array();

        foreach($addroute as $key=>$value)
        {

            $primary_vehicle='';

            $vehicle_type=self::has($value->route,'vehicle_type');
            $vehicle_type_h=self::has($value->route,'vehicle_type_h');

            if($vehicle_type){$primary_vehicle=$vehicle_type;}
            else if($vehicle_type_h){$primary_vehicle=$vehicle_type_h;}

            $route[$key]['fk_buyer_seller_post_id']=$ratecardmasterid ;
            $route[$key]['vehicle_type_id']=$primary_vehicle;
            $route[$key]['is_seller_buyer']=SELLER;
            $route[$key]['lkp_service_id']=_INTRACITY_;
  
            $route[$key]['type_basis']= self::has($value->route,'type');
            $route[$key]['city_id']= $value->route->city->id;
            $route[$key]['valid_from']=self::has($value->route,'valid_from_date');
            $route[$key]['valid_to']=self::has($value->route,'valid_to_date');
            $route[$key]['additional_km_charge']=self::has($value->route,'km_charge');
            $route[$key]['is_active']=1;
            $route[$key]['time_from']=self::has($value->route,'from_time');
            $route[$key]['time_to']=self::has($value->route,'from_to');
            $route[$key]['base_distance']=self::has($value->route,'base_distance');
            $route[$key]['rate_base_distance']=self::has($value->route,'rate_base_distance');
            $route[$key]['base_time']=self::has($value->route,'base_time')?$value->route->base_time->id:'';
            $route[$key]['cost_base_time']=self::has($value->route,'cost_base_time');
            $route[$key]['additional_hour_charge']=self::has($value->route,'houre_charge');
            $route[$key]['cost_per_extra_hour']=self::has($value->route,'extra_hour');

            $route[$key]['wc_vehicle_type']=self::has($value->route,'vehicle_type_wait');
            $route[$key]['tracking']=self::has($value->route,'tracking_type');
            $route[$key]['wc_cost_per_extra_hr']=self::has($value->route,'extracost_wait');
            $route[$key]['odc_vehicle_type']=self::has($value->route,'vehicle_over_dimension');
            $route[$key]['odc_base_volume']=self::has($value->route,'dimension_volume')."$".self::has($value->route,'material_type');
            $route[$key]['odc_cost_extra_volume']=self::has($value->route,'volume_unit_extra');
            $route[$key]['lc_no_helpers']=self::has($value->route,'helpers');
            $route[$key]['lc_base_charge_per_labour']=self::has($value->route,'labour_charge');
            
            $route[$key]['multiple_rate']= json_encode(self::has($value,'multipleRate'));
          

            $route[$key]['lc_addtnl_chrg']=self::has($value->route,'addition_labour_charge');
            $route[$key]['lc_toll_charge']=self::has($value->route,'toll_charge');
            $route[$key]['lc_others']=self::has($value->route,'other');
            $route[$key]['transit_hour']=self::has($value->route,'transit_hour');

            //dd($route[$key]);

            if($data->uid == '') :

             $fk_rate_card_id = DB::table('intra_hp_buyer_seller_routes')->insertGetId($route[$key]);
            else :

                DB::table('intra_hp_buyer_seller_routes')
               ->where('fk_buyer_seller_post_id', $data->uid)
               ->where('is_seller_buyer', SELLER)
               ->delete();
               $fk_rate_card_id = DB::table('intra_hp_buyer_seller_routes')->insertGetId($route[$key]);

            endif;


           if(!empty($value->dis))
           {
            $discount=array();
             foreach($value->dis as $kk=>$val)
             {
              $discount[$kk]=array(
                'fk_rate_card_id'=>$fk_rate_card_id,
                'buyer_id'=>$val->buyer->id,
                'disc_type'=>$val->discount_type,
                'disc_amt'=>$val->buyer_discount,
                'credit_days'=>$val->credit_day,
                'net_price'=>$val->netprice,
                'discount_basis'=>ROUTE_WISE,
                'intra_hp_sellerpost_ratecart_id'=>$ratecardmasterid
              );

             }
             if($data->uid == '') :
               DB::table('intra_hp_discounts')->insert($discount);
              else:
               DB::table('intra_hp_discounts')
               ->where('intra_hp_sellerpost_ratecart_id', $data->uid)
               ->where('fk_rate_card_id', $fk_rate_card_id)
               ->delete();
              DB::table('intra_hp_discounts')->insert($discount);
              endif;
           }
               ///data save solr
              // $solr = new Solr();
              //$solr->solarSaveRatecard($IntraHyperSellerPost,$route[$key],$seller_ids);
         }

       /***************route and discount end here*****************/
        $finaldiscount=json_decode($mixdata->discount);

        /****************final discount add *****************/
        if(!empty($finaldiscount))
           {
             foreach($finaldiscount as $k=>$discount)
             {

                 $discounts[$k]=array(
                  'fk_rate_card_id'=>$ratecardmasterid,
                  'buyer_id'=>$discount->buyer->id,
                  'disc_type'=>$discount->discount_type,
                  'disc_amt'=>$discount->buyer_discount,
                  'credit_days'=>$discount->credit_day,
                  'discount_basis'=>ON_ALL_ROUTE,
                  'intra_hp_sellerpost_ratecart_id'=>$ratecardmasterid

                );

             }
            if($data->uid == '') :
            DB::table('intra_hp_discounts')->insert($discounts);
            else:
            DB::table('intra_hp_discounts')
               ->where('intra_hp_sellerpost_ratecart_id', $data->uid)
               ->where('fk_rate_card_id', $fk_rate_card_id)
               ->delete();
            DB::table('intra_hp_discounts')->insert($discounts);

            endif;
           }

        });//transection close
        if($data->uid != '') : 
            $transctionid=$data->uid;
        else:
           $transctionid=$IntraHyperSellerPost;
        endif;

        return response()->json([
            'status'=>'success',
            'payload'=>[
                'primaryData'=>'',
                'attribute'=>'',
                'data'=>$IntraHyperSellerPost
            ]
        ]);


    } catch (Exception $e) {

        echo 'Caught exception: ', $e->getMessage(), "\n";

    }

}

   public static function has($object,$property) {

     return property_exists($object,$property)?$object->$property:'';

   }
   /**************************************************
   *save buyer id in case of private post*************
   ****************************************************/
   public static function saveBuyer($seller_ids,$buyer_post_id,$uid) {
        if($seller_ids) {
            $ids = array();
                foreach($seller_ids as $key => $value) {
                $ids[$key] = array(
                    'buyer_seller_post_id'=>$buyer_post_id,
                    'buyer_seller_id'=>$value,
                    'type'=>SELLER, // for buyer
                    'is_active'=>1,
                    'lkp_service_id'=>3

                );
            }
             if($uid == '') :
               DB::table('intra_hp_assigned_seller_buyer')->insert($ids);
              else:
                DB::table('intra_hp_assigned_seller_buyer')
               ->where('buyer_seller_post_id', $uid)
               ->where('type', SELLER)
               ->delete();
              endif;

        }
    }

    public static function buyerPostSearch($request)
    {
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $data=json_decode($request->getcontent());
        $type=$data->type;
        $city_id=$data->city->id;
        $from_location=$data->fromLocation->id;
        $to_location=$data->toLocation->id;
        $valid_to_date=$data->dispatchDate;
        $vehicle_type=$data->vehicle_type->id;
        $termPost=$data->termPost;

       // DB::enableQueryLog();
        $rs = DB::table('intra_hp_buyer_posts as bp')
            ->select('bp.id as buyer_post_id','bp.last_date','sr.*',
              DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=sr.vehicle_type_id ) as vehicle"),
              DB::raw("(select username FROM users WHERE id=bp.posted_by ) as buyer"),
              DB::raw("(select city_name FROM lkp_cities WHERE id=sr.city_id ) as city_name")
              )
            ->join('intra_hp_buyer_seller_routes as sr', 'sr.fk_buyer_seller_post_id', '=','bp.id' )
            ->where([
                    ['sr.is_seller_buyer','1'],
                    ['sr.city_id',$city_id],

                    ]);

              if($vehicle_type!='')
              $rs->where('sr.vehicle_type_id','=',$vehicle_type);
              if($from_location!='')
              $rs->where('sr.from_location','=',$from_location);
              if($from_location!='')
              $rs->where('sr.to_location','=',$to_location);
              if($termPost!='')
              $rs->where('bp.lead_type','=',$termPost)  ;
              if($type!='')
              $rs->where('bp.type_basis','=',$type) ;
              if($valid_to_date!='')
              $rs->whereDate('sr.valid_from','<=',$valid_to_date) ;
              $rs->whereDate('bp.last_date','>=',$valid_to_date) ;

           // ->toSql();
           $returdata= $rs->limit(self::$rows_fetched)->get();
         //dd(DB::getQueryLog());
            //dd($rs->get());
           return response()->json([
            'status'=>'success',
            'payload'=>$returdata
        ]);


    }
    public static function sellerPostCount()
    {
            $userID = JWTAuth::parseToken()->getPayload()->get('id');
            $rs=DB::table('intra_hp_sellerpost_ratecart')
               ->select(
                DB::raw("(select count(`is_private_public`) from intra_hp_sellerpost_ratecart where is_private_public=0 AND is_active=1) as public"),
                DB::raw("(select count(`is_private_public`) from intra_hp_sellerpost_ratecart where is_private_public=1 AND is_active=1) as private"),
                DB::raw("(select count(`rate_cart_type`) from intra_hp_sellerpost_ratecart where  rate_cart_type=1 AND is_active=1) as hour"),
                DB::raw("(select count(`rate_cart_type`) from intra_hp_sellerpost_ratecart where  rate_cart_type=2 AND is_active=1) as distance")
                )->limit(1);

            return response()->json([
            'status'=>'success',
            'payload'=>$rs->get()
        ]);
    }
    public static function sellerPostLists($request)
    {
         $userID = JWTAuth::parseToken()->getPayload()->get('id');
         $filterdata=$request->type;
        // DB::enableQueryLog();
        $rs = DB::table('intra_hp_sellerpost_ratecart as bp')
            ->join('intra_hp_buyer_seller_routes as sr', 'sr.fk_buyer_seller_post_id', '=','bp.id' )
            ->leftjoin('intra_hp_discounts as ds','ds.intra_hp_sellerpost_ratecart_id','=','bp.id')
            ->leftjoin('intra_hp_assigned_seller_buyer as ab','ab.buyer_seller_post_id','=','bp.id')
            // ->select('bp.id as pid','sr.city_id','sr.type_basis','bp.rate_cart_type','bp.is_private_public','sr.valid_from',DATE_FORMAT('sr.valid_to',"%Y-%m-%d")

            //           )
            ->select('bp.id' ,
                        'sr.city_id',
                        'bp.post_status',
                        'sr.type_basis',
                        'bp.rate_cart_type','bp.is_private_public',
                        'sr.valid_from','sr.valid_to',
                        DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=sr.vehicle_type_id ) as vehicle"),
                        DB::raw("(select username FROM users WHERE id=ds.buyer_id ) as buyer"),
                        DB::raw("(select city_name FROM lkp_cities WHERE id=sr.city_id ) as city_name"),
                        DB::raw("(select username FROM users WHERE id=ab.buyer_seller_id ) as assign_buyer")

                      )

             ->where([
                    ['sr.is_seller_buyer','2'],
                    ['bp.is_active',1],
                    ]);
             if($filterdata=='distance')
              $rs->where('sr.type_basis','=','2');
             if($filterdata=='hour')
              $rs->where('sr.type_basis','=','1');
             if($filterdata=='public')
              $rs->where('bp.is_private_public','=','0');
            if($filterdata=='private')
              $rs->where('bp.is_private_public','=','1');
                //dd($rs->tosql());
            return response()->json([
            'status'=>'success',
            'payload'=>EncrptionTokenService::idEncrypt($rs->limit(self::$rows_fetched)->orderBy('bp.id','DESC')->get())
        ]);

    }
/*********seller post Details get post id and return post detais************************/
public static function getPostDetails($request)
    {

         $userID = JWTAuth::parseToken()->getPayload()->get('id');
         $id=EncrptionTokenService::idDecrypt($request->id);

        // DB::enableQueryLog();
        $rs = DB::table('intra_hp_sellerpost_ratecart as bp')
            ->join('intra_hp_buyer_seller_routes as sr', 'sr.fk_buyer_seller_post_id', '=','bp.id' )



            ->select('bp.id' ,
                        'bp.post_transaction_id',
                        'sr.city_id',
                        'bp.post_status',
                        'sr.type_basis',
                        'bp.rate_cart_type','bp.is_private_public',
                        'sr.valid_from','sr.valid_to','sr.tracking',
                        'sr.base_distance','sr.rate_base_distance','sr.base_time',
                        'sr.cost_base_time','sr.cost_per_extra_hour','sr.is_active',
                        'sr.transit_hour',

                        DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=sr.vehicle_type_id ) as vehicle"),
                        DB::raw("(select city_name FROM lkp_cities WHERE id=sr.city_id ) as city_name")
                      )

             ->where([
                    ['sr.is_seller_buyer','2'],

                     ['bp.id',$id],
                    ]);

                //dd($rs->tosql());
              //dd(DB::getQueryLog());
            return response()->json([
            'status'=>'success',
            'payload'=>EncrptionTokenService::idEncrypt($rs->get())
        ]);

    }
    /****end here******/





    public static function getdetailsById($seller_id) {

       $detailsById = DB::table('seller_details')
                           ->select('name', 'contact_mobile','contact_landline','gta','tin','service_tax_number','established_in','principal_place','contact_email', 'address1', 'address2', 'address3', 'pincode', 'ls.state_name')
                           ->join('lkp_states as ls','ls.id', '=','seller_details.lkp_state_id')
                           ->where('user_id', $seller_id)
                           ->first();
        if(sizeof($detailsById)){
             return response()->json(['payload'=>$detailsById]);
        } else {
             return response()->json(['payload'=>0]);
        }

    }


    public static function getMessageById($request) {

        $id = $request->id;
        $query = DB::table('intra_hp_sellerpost_ratecart as sr')
                    ->join('intra_hp_buyer_seller_routes as r', 'r.fk_buyer_seller_post_id', '=', 'sr.id')
                    ->select('sr.is_private_public',
                             'sr.terms_cond',
                             'r.tracking',
                             'r.valid_from',
                             'r.valid_to',

                             DB::raw("(select count(*) FROM intra_hp_buyer_seller_routes WHERE fk_buyer_seller_post_id = $id AND is_seller_buyer = 2 AND is_active = 1) as countRoutes"),
                             DB::raw("(select disc_type FROM intra_hp_discounts WHERE fk_rate_card_id = r.fk_buyer_seller_post_id) as discounts")
                        )
                    ->where('sr.is_active', 1)
                    ->where('r.is_active', 1)
                    ->where('sr.id', $id)
                    ->first();

           return response()->json([
                'payload'=>$query
           ], 200);
    }

 // dd($request->id);
    public static function deleteSellerPost($request)
    {
      // $id=$request->id;
      // $ratecard = SellerpostRatecart::with('postRoute')->where('id', '=', $id)->first();
      // $ratecard->is_active = 2;
      // foreach ($ratecard->postRoute as $key => $value) {
      //   $value->is_active = 2;
      // }
      // $ratecard->save();
      // return response()->json($ratecard);
      /*******************************/
      // DB::enableQueryLog();

       $id=EncrptionTokenService::idDecrypt($request->id);
       DB::transaction(function() use ($id) {

         DB::table('intra_hp_sellerpost_ratecart')
            ->where('id', $id)
            ->update(['post_status' => 2]);

         DB::table('intra_hp_buyer_seller_routes')
            ->where('fk_buyer_seller_post_id', $id)
            ->where('is_seller_buyer', 2)
            ->update(['is_active' => 2]);


       });

       return response()->json([
                'payload'=>$id
           ], 200);


    }


    public static function getDiscountDetails($request)
    {
        //DB::enableQueryLog();
         $userID = JWTAuth::parseToken()->getPayload()->get('id');
         $id=$request->id;
         $rs=DB::table('intra_hp_discounts')
                ->leftjoin('users','users.id','=','intra_hp_discounts.buyer_id')
                ->select('intra_hp_discounts.*','users.username as buyer')
                ->where('intra_hp_sellerpost_ratecart_id', '=',$id);
               // $rs->get();
            //dd(DB::getQueryLog());
            return response()->json([
            'status'=>'success',
            'payload'=>$rs->get()
        ]);

    }

    public static function getPreDetails($request)
    {

         $userID = JWTAuth::parseToken()->getPayload()->get('id');
         $id=EncrptionTokenService::idDecrypt($request->id);
         $rs=DB::table('intra_hp_sellerpost_ratecart')
                  ->select('*'
                  )
                ->where('id', '=',$id);


            return response()->json([
            'status'=>'success',
            'payload'=>$rs->first()
        ]);

    }

    //  public static function getFinalDetails($request)
    // {

    //      $userID = JWTAuth::parseToken()->getPayload()->get('id');
    //      $id=$request->id;
    //      $rs=DB::table('intra_hp_discounts')
    //             ->leftjoin('users','users.id','=','intra_hp_discounts.buyer_id')
    //             ->select('intra_hp_discounts.*','users.username as buyer')
    //             ->where('intra_hp_sellerpost_ratecart_id', '=',$id)
    //             ->where('intra_hp_discounts.discount_basis', '=',2);

    //         return response()->json([
    //         'status'=>'success',
    //         'payload'=>$rs->get()
    //     ]);

    // }


}
