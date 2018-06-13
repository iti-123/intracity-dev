<?php

namespace ApiV2\Services\Community;
use ApiV2\Services\Community\BaseServiceProvider;
use ApiV2\Model\LogistiksUser;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Model\Community\Follower;

use Illuminate\Support\Collection;
class FollowService extends BaseServiceProvider
{
    
    public static function getFollowers($request) {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');

        // Already followed user
        $filteredId = array($userId);
        $followedUsers = static::getFollowersById($userId);
        foreach($followedUsers as $key=>$value) {
            array_push($filteredId,$value->user_id);
        }
        
        $followers = LogistiksUser::where('is_active','=',1);
        $followers->whereNotIn('id',$filteredId);
        $followers = static::getFollowerDetails($request,$followers);
        
        
        return array(
            'followers'=>$followers->get(['id','username','user_pic']),
            'totalFollowers'=>static::getFollowersCount($userId),
            'totalFollowing'=>static::getFollowingCount($userId),
        );
    }

    // Get followers details a/c to user

    private static function getFollowerDetails($request,$query) {
        if($request['role'] == 'seller') {    
            $query->with(['buyer','buyer.city']);        
            
        } else if($request['role'] == 'buyer') {
            $query->with(['seller','seller.city','seller.industry','seller.empStrength','seller.business']);
        }
        return $query;
    }

    public static function follow($request) {   

        $followerId = JWTAuth::parseToken()->getPayload()->get('id');    
        
        $checkAlreadyFollowed = static::checkIfCurrentFollowersExist($followerId,$request['userId']);
            
        if(!$checkAlreadyFollowed->count()) {
            $follower = new Follower(); 
            $follower->user_id = $request['userId'];
            $follower->follower_id = $followerId;
            $follower->status = 1;
            $follower->role = $request['role'];
            return $follower->save();
        } else if($checkAlreadyFollowed->count()) {
            $follow = $checkAlreadyFollowed->get()->first();
            $follow->status = 1;
            $follow->save();
        } else {
            return "Already followed";
        }
    }


    // Unfollow 
    public static function unFollow($request) {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');    
        $checkAlreadyFollowed = Follower::find($request['followingId']);
        if(!empty($checkAlreadyFollowed)) {
            $checkAlreadyFollowed->status = 2;
        }
        return $checkAlreadyFollowed->save();
    }



    private static function checkIfCurrentFollowersExist($followerId,$userId) {
        return Follower::where([
            ['user_id','=',$userId],
            ['follower_id','=',$followerId]                        
        ]);
    }

    private static function getFollowersCount($userId) {        
        return Follower::where([
            ['user_id','=',$userId],
            ['status','=',1]           
        ])->count();
    }

    private static function getFollowingCount($userId) {
        return Follower::where([
            ['follower_id','=',$userId],
            ['status','=',1]           
        ])->count();
    }

    public static function getFollowersById($activeUserId) {
        return $checkAlreadyFollowed = Follower::where([
            ['follower_id','=',$activeUserId],
            ['status','=',1]                       
        ])->get(['user_id']);
    }

    public static function getMyFollowers() {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        return static::getMyFollowersDetails($userId);
    }

    public static function getMyFollowing() {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        return static::getMyFollowingDetails($userId);
    }

    public static function getMyFollowersDetails($activeUserId) {
        return $checkAlreadyFollowed = Follower::where([
            ['user_id','=',$activeUserId],
            ['status','=',1]                       
        ])
        ->with('followerUser')->get(['id','follower_id']);
    }

    public static function getMyFollowingDetails($activeUserId) {
        return $checkAlreadyFollowed = Follower::where([
            ['follower_id','=',$activeUserId],
            ['status','=',1]                       
        ])
        ->with('user')->get(['id','user_id']);
    }    
    
}
