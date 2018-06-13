<?php

namespace ApiV2\Model\Community;
use Illuminate\Database\Eloquent\Model;
use ApiV2\Model\Community\ReplyModel;
use DB;
use App\User;
use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\ApiV2\Events\BuyerPostCreatedEvent;

class ShareModel extends Model 
{   

    protected $table = 'community_share';

    public static function shareInsert($request)
    {
        if($request->type=='jobs'){
            $userid=$request->commonData['posted_by']['id'];
        } else {
            $userid=$request->commonData['postedby']['id'];
        }    
            $connection_data = DB::table('connections as co')
            ->select('co.*')
            ->where('co.status', 2)
            ->where('co.connector_id', $userid)
            ->where('co.type','!=' , 3)
            ->get();
        
       
           foreach ($connection_data as $key => $value) {
                $shares = new ShareModel;
                $shares->type = $request->type;
                $shares->post_id = $request->commonData['id'];
                $shares->message = $request->commonPost;
                $shares->link = $request->url;
                $shares->shared_to = $value->user_id;
                $shares->shared_by = $userid;
                $shares->is_public = 0;
                $shares->created_ip = $_SERVER['REMOTE_ADDR'];
                $shares->is_active = 1;
                $shares->save();
           }

            
        
        return $connection_data;
    }
    
    

}