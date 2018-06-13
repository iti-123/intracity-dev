<?php
namespace ApiV2\Model;
use Illuminate\Database\Eloquent\Model;


class UserView extends Model
{
    protected $table = 'user_views';
    protected $fillable = ['service_id','user_id','role_id','model_id','type','created_by','view_count','updated_by','updated_ip','created_ip','created_at','updated_at'];
    
    public function viewedBy() {
        return $this->hasOne('App\User','id','user_id')->select(['id','username']);
    }
}