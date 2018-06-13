<?php

namespace Api\Model\HyperLocal;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Solr;
use Api\Services\SolrSearchService;
use Api\Model\IntraHyperRoute;
use Api\Services\LogistiksCommonServices\NumberGeneratorServices;
use Api\Services\LogistiksCommonServices\EncrptionTokenService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Api\Model\IntraHyperBuyerPost;


class M_Buyer_post extends Model 
{   


    public static function insertdata($data) {
          //return $data;
            try{
               DB::beginTransaction();
                $userID = JWTAuth::parseToken()->getPayload()->get('id');
                $hyperlocalPost = new IntraHyperBuyerPost;
                $hyperlocalPost->posted_by=$userID;
                $hyperlocalPost->lead_type=$data->type;
                $hyperlocalPost->last_time=$data->validtime;
                $hyperlocalPost->is_accept_terms_cond=$data->is_accept_terms_cond;
                $hyperlocalPost->last_date=$data->quote_date;
                $hyperlocalPost->lkp_service_id=_HYPERLOCAL_;
                $hyperlocalPost->is_active=1;
                $hyperlocalPost->post_status=$data->post_status;
                $hyperlocalPost->comments=$data->comment;
                $hyperlocalPost->income_tax_assesse=$data->income_tax_assesse;
                $hyperlocalPost->no_of_trucks=$data->no_of_trucks;
                $hyperlocalPost->average_turn_over=$data->average_turn_over;
                $hyperlocalPost->average_turn_over=$data->average_turn_over;
                $hyperlocalPost->no_of_years=$data->no_of_years;
                $hyperlocalPost->term_contract_woc=$data->term_contract_woc;
                $hyperlocalPost->is_fragile=$data->is_fragile;
                $hyperlocalPost->is_private_public=$data->is_private_public;
                $hyperlocalPost->category=$data->category['id'];
                $hyperlocalPost->servicetype=$data->service_type['id'];
                $hyperlocalPost->depart_date=$data->depart_date;
                $hyperlocalPost->multiple_location=json_encode($data->everyaddlocation);
                $hyperlocalPost->post_transaction_id=NumberGeneratorServices::generateTranscationId(new IntraHyperBuyerPost,_HYPERLOCAL_);
                $hyperlocalPost->save();
                //return $hyperlocalPost;
                $id=$hyperlocalPost->id;
                
                if($data->visibleToSellers)
                {
                 $seller_ids=explode(',',$data->visibleToSellers);
                  self::saveBuyer($seller_ids,$id);  
                }
                
               //return $data->everyaddlocation;
               foreach($data->everyaddlocation as $key=>$root)
               {
                
                 $IntraHyperRoute=new IntraHyperRoute;
                 $IntraHyperRoute->is_seller_buyer=1;
                 $IntraHyperRoute->is_active=1;
                 $IntraHyperRoute->valid_from=$root['from_date'];
                 $IntraHyperRoute->valid_to=$root['todate'];
                 if($root['price_type']!='')
                 {
                 $IntraHyperRoute->price_type=$root['price_type']['id'];
                 $IntraHyperRoute->firm_price=$root['firm_price'];
                 }
                 if($root['unit']!='')
                  {
                 $IntraHyperRoute->estimated_unit=$root['unit']['id'];
                 $IntraHyperRoute->estimated_quanity=$root['quantity'];
                  }
                 $IntraHyperRoute->fk_buyer_seller_post_id=$id;
                 $IntraHyperRoute->city_id=$root['city_id']['id']['id'];
                 $IntraHyperRoute->city_name=$root['city_id']['id']['city_name'];
                 $IntraHyperRoute->from_location=$root['from_location']['id'];
                 $IntraHyperRoute->to_location=$root['to_location']['id'];
                 $IntraHyperRoute->weight=$root['max_weight'];
                 $IntraHyperRoute->max_no_parcel=$root['max_no_parcel'];
                 $IntraHyperRoute->lkp_service_id=_HYPERLOCAL_;
                 $IntraHyperRoute->save();  
                 //return $IntraHyperRoute;  
                }


                DB::commit();  
                 return response()->json([
                'isSuccessful'=>true,
                'tran_id'=>$hyperlocalPost->post_transaction_id,
                'data'=>$hyperlocalPost,
                'postid'=>$id
                ],200);

            }catch(Exception $e) {
              DB::rollBack();
              LOG::error($e->getMessage());
             return $this->errorResponse($e);
           }
        
    }
    
    /**************************************************
   *save buyer id in case of private post*************
   ****************************************************/
   public static function saveBuyer($seller_ids,$buyer_post_id) {
        if($seller_ids) {
            $ids = array();
                foreach($seller_ids as $key => $value) {
                $ids[$key] = array(
                    'buyer_seller_post_id'=>$buyer_post_id,
                    'buyer_seller_id'=>$value,
                    'type'=>BUYER, // for buyer
                    'is_active'=>1,
                    'lkp_service_id'=>_HYPERLOCAL_
                );
            }
             DB::table('intra_hp_assigned_seller_buyer')->insert($ids);
             

        }
    } 
    
   /****************count data public private ********/ 
    public static function  countPost()
    {      
             //DB::enableQueryLog();
             $userID = JWTAuth::parseToken()->getPayload()->get('id');
             $record = DB::table('intra_hp_buyer_posts')
              ->select(DB::raw("
                count(*) as total,
                sum(CASE WHEN lead_type = 1 then 1 else 0 end) as spot,
                sum(CASE WHEN lead_type = 2 then 1 else 0 end) as term,
                sum(CASE WHEN is_private_public = 0 then 1 else 0 end) as public,
                sum(CASE WHEN is_private_public = 1 then 1 else 0 end) as private
                "))
              ->where('lkp_service_id','=',_HYPERLOCAL_)
              ->where('posted_by','=', $userID)
             
              ->first();
            return response()->json([
            'status'=>'success',
            'payload'=>$record
        ]);
               //dd(DB::getQueryLog());
    }

    public static function deletehyperBuyerPost($request)
    {
       $id=$request->id; 
        
       DB::transaction(function() use ($id) {

         DB::table('intra_hp_buyer_posts')
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
    public static function sellerSearch($request)
    {
        
        //return $request->all();
        //DB::enableQueryLog();
        $category=$request->category['id'];
        $city_id=$request->city['id'];
        $fragile=$request->fragile;
        $service_type=$request->service_type['id'];
        $date=$request->departingDate;
        
            // foreach($request->location as $key=>$value)
            // {
            //     $fromLocation[]=$value['fromLocation']['id'];
            //     $tolocation[]=$value['tolocation']['id'];
            // }    
       
          //return $fromLocation;
           
           $rs = DB::table('intra_hp_sellerpost_ratecart as sp')
            ->select('*',
                DB::raw("(select username FROM users WHERE id=sp.posted_by ) as vendor"))
            
            ->where([
                    
                    ['sp.city_id',$city_id],
                    ['sp.service_type',$service_type],
                    ['sp.product_category',$category],
                    ['sp.lkp_service_id',_HYPERLOCAL_],
                   
                    ]);
             $rs->whereDate('sp.from_date','<=',$date); 
             $rs->whereDate('sp.to_date','>=',$date) ;
             $rs->orderBy('id','Desc');
             
             // //$rs->get();
            //dd(DB::getQueryLog());
            return response()->json([
            'status'=>'success',
            'payload'=>EncrptionTokenService::idEncrypt($rs->get())
        ]);      
    }

}