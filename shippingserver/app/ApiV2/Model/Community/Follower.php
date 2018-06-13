<?php
namespace ApiV2\Model\Community;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Facades\JWTAuth;

class Follower extends Model 
{
    protected $table = 'followers';

    public function user()
    {
        return $this->hasOne('ApiV2\Model\LogistiksUser','id','user_id')->select(['id','username','designation']);
    }
    public function followerUser()
    {
        return $this->hasOne('ApiV2\Model\LogistiksUser','id','follower_id')->select(['id','username','designation']);
    }    
    
}