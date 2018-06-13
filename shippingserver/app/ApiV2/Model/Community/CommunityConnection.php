<?php
namespace ApiV2\Model\Community;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommunityConnection extends Model 
{
    protected $table = 'connections';

    protected $fillable = ['user_id','type','connector_id','status','message','group_id'];

    public function user()
    {
        return $this->hasOne('ApiV2\Model\LogistiksUser','id','connector_id')->select(['id','username','designation']);
    }

    public function group()
    {
        return $this->hasOne('ApiV2\Model\Community\Group','id','group_id')->select(['id','name','image']);
    }

    

}