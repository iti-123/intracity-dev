<?php

namespace ApiV2\Model;

use Illuminate\Database\Eloquent\Model;

class IntraHyperQuotaion extends Model
{
    protected $table = 'intra_hp_post_quotations';

    public function route()
    {
        return $this->hasOne('ApiV2\Model\IntraHyperRoute', 'id', 'route_id');
    }

    public function post()
    {
        return $this->hasOne('ApiV2\Model\IntraHyperBuyerPost', 'id', 'post_id');
    }

    
    public function postedto()
    {
        return $this->hasOne('ApiV2\Model\UserDetails', 'id', 'seller_id')->select('id','username');
    }
    public function contract()
    {
        return $this->hasOne('ApiV2\Model\TermContract', 'intra_hp_post_quotations_id', 'id');
    }

    public function checkOrder() {
        return $this->hasMany('ApiV2\Model\OrderItem','routeId','route_id')->where('status','>=',1)->select('routeId','id');
    }


}
