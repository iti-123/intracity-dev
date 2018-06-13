<?php
namespace ApiV2\Model\Community;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Facades\JWTAuth;

class Group extends Model 
{
	//protected $table = 'group';
    protected $fillable = ['description','name','is_public','member_ids','image','status','created_by'];
   
    public function isJoining() {
        return $this->hasOne('ApiV2\Model\Community\CommunityConnection','group_id','id')->where('type','=',3)->select('group_id','status');
    }
    public function members() {
        return $this->hasMany('ApiV2\Model\Community\CommunityConnection','group_id','id')->where('type','=',3)->where('status','=',2)->select('group_id');
    }
    
    public function user()
    {
        return $this->hasOne('ApiV2\Model\LogistiksUser','id','created_by')->select(['id','username','designation']);
    }
   
}

class Member extends Model 
{
    protected $fillable = ['group_id','member_id','status'];
}