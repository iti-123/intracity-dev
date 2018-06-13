<?php

namespace ApiV2\Services\Community;
use ApiV2\Services\Community\BaseServiceProvider;
use ApiV2\Model\LogistiksUser;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService as Encryptable;
use DB;
class ProfileService extends BaseServiceProvider
{
    
    public static function communityProfile($request) {  
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $profileQuery = LogistiksUser::where('id','=',$userId);       
        
        $profileQuery->with(['partners.user','partners'=> function($query) use ($request) {
            $query->where('type','=',2)
                ->where('status','=',2);
        }])
        ->with(['connections.user','connections'=> function($query) use ($request) {
            $query->where('type','=',1)
                ->where('status','=',2);
        }])
        ->with(['groups.user','groups','groups.group','createdBy','createdBy.user'])
        ->with(['followers.user','followers'=> function($query) use ($request,$userId) {
            $query->where('status','=',1)
            ->where('follower_id','=',$userId);
        }]);

        $profileQuery = static::getFollowerDetails($request,$profileQuery);
        
        return $profileQuery->get(['id','username','user_pic'])->first();
    }

    public static function getFollowers($request) {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $followers = LogistiksUser::where('is_active','=',1);
        
        $followers = static::getFollowerDetails($request,$followers);
        
        
        return $followers->get(['id','username','user_pic']);
    }

    // Get followers details a/c to user

    private static function getFollowerDetails($request,$query) {
        if($request['role'] == 'seller') {            
            $query->with(['seller','seller.city','seller.industry','seller.empStrength','seller.business']);
        } else if($request['role'] == 'buyer') {
            $query->with(['buyer','buyer.city']);
        }
        return $query;
    }


    public static function getAllUsers($request) 
    {
        $users = LogistiksUser::where('is_active', 1)
                                ->with('partners')
                                ->get(['id', 'username', 'logo']);

        return $users;                        
    }

    public static function getProfileDetail($request) 
    {
        $encryptedId =  explode("-",$request->q);
        
        $id = Encryptable::idDecrypt(end($encryptedId));
        
        $users = LogistiksUser::where('is_active', 1)
                                ->where('id','=',$id)
                                ->with([
                                    'partners',
                                    'partners.user',
                                    'connections',
                                    'connections.user',
                                    'followers',
                                    'seller',
                                    'seller.city',
                                    'seller.industry',
                                    'seller.empStrength',
                                    'seller.business',
                                    'buyer'
                                ])
                                ->get([
                                    'id', 
                                    'username', 
                                    'logo',
                                    'primary_role_id as isBuyer',
                                    'secondary_role_id as isSeller',
                                    'fb_identifier',
                                    'google_identifier',
                                    'linkedin_identifier'
                                ])->first();
        return array(
            'data'=>$users,
            'services'=>static::getUserServices($id,2)
        );                         
    }



    public static function getSellerDetail($request) 
    {
$roleId="";
$urlType='post';
if(JWTAuth::parseToken()->getPayload()->get('role')=='Seller'){
$roleId=2;
}
else{
$roleId=1;
}
//select seller_details.name ,(select lkp_service_urls.url from lkp_service_urls where serviceId=$request->cmtServicId and `type`='post' and usertype=$roleId) as serviceUrl from seller_details where 

//user_id in ( select user_id from  seller_services where seller_services.lkp_service_id=$request->cmtServicId)


$results = DB::select(DB::raw("select seller_details.name ,$request->cmtServicId as serviceId ,(select lkp_service_urls.url from lkp_service_urls where serviceId=$request->cmtServicId and `type`='post' and usertype=$roleId) as serviceUrl from seller_details where 

user_id in ( select user_id from  seller_services where seller_services.lkp_service_id=$request->cmtServicId)"));


        return array(
            'data'=>$results,
            'services'=>JWTAuth::parseToken()->getPayload()->get('role')
        );                        
    }




    public static function getUserServices($userid,$role_id) {
        if($role_id==2){
            ### for seller ####
        $buyerquoteid = DB::table('seller_services')
                ->where('user_id','=', $userid)
                ->join('lkp_services as s','s.id', '=', 'seller_services.lkp_service_id')
                ->where('is_service_offered','=','1')
                ->select('service_name')->get();
        

        }
        else{
        $count_n = DB::table('seller_services')->select('id')->where('user_id','=',$userid)->where('is_service_required','=','1')->get();
                            if (count($count_n) ==0){
            ##### for buyer #####                    
        $buyerquoteid = DB::table('lkp_services')->select('service_name')->where('is_active','=','1')->get();
                        }
                        else{ 
             ###### if buyer want specific service ######               
        $buyerquoteid = DB::table('seller_services')->select('service_name')->where('is_service_required','=','1')->where(
         'user_id','=',$userid)->get();
        }
        }
       
        return ($buyerquoteid);
    }
    
}
