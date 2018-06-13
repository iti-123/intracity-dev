<?php

namespace ApiV2\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class LogistiksUser extends Model
{
    protected $table = 'users';

    protected $appends = ['encryptedId'];

    public function seller()
    {
        return $this->hasOne('ApiV2\Model\SellerDetail','user_id','id')->select(['user_id','name','established_in as establishedIn','current_turnover as turnover','main_markets as mainMarket','lkp_city_id','lkp_industry_id','lkp_employee_strength_id','lkp_business_type_id']);
    }

    public function buyer()
    {
        return $this->hasOne('ApiV2\Model\BuyerDetail','user_id','id')->select(['user_id','firstname as name','lastname as lastname','principal_place as principalPlace','lkp_city_id']);
    }

    public function getUsernameAttribute($value)
    {
        return ucWords(strtolower($value));
    }

    public function getEncryptedIdAttribute()
    {
        return Crypt::encrypt($this->id);
    }
  

    public function partners()
    {
        return $this->hasMany('ApiV2\Model\Community\CommunityConnection','user_id','id')->where('type','=',2)->select(['connector_id','user_id']);
    }

    public function connections()
    {
        return $this->hasMany('ApiV2\Model\Community\CommunityConnection','user_id','id')->where('type','=',1)->select(['connector_id','user_id']);
    }

    public function follower()
    {
        return $this->hasOne('ApiV2\Model\Community\Follower','user_id','id')->select(['follower_id','user_id']);
    } 
    
    public function followers()
    {
        return $this->hasMany('ApiV2\Model\Community\Follower','user_id','id')->select(['follower_id','user_id']);
    } 
    public function groups()
    {
        return $this->hasMany('ApiV2\Model\Community\CommunityConnection','user_id','id')->where('type','=',3)->where('status','=',2)->select(['connector_id','user_id','group_id']);
    }

    public function createdBy()
    {
        return $this->hasMany('ApiV2\Model\Community\Group','created_by','id');
    }
    
}

class Industry extends Model 
{
    protected $table = 'lkp_industries';
}

class EmpStrength extends Model 
{
    protected $table = 'lkp_employee_strengths';
}

class BusinessType extends Model 
{
    protected $table = 'lkp_business_types';
}
