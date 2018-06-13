<?php

namespace Api\Model;

use Illuminate\Database\Eloquent\Model;

class IntracityQuotaion extends Model
{
    protected $table = 'intra_hp_post_quotations';

    public function route()
    {
        return $this->hasOne('Api\Model\IntraHyperBuyerPost', 'id', 'post_id');
    }

    public function post()
    {
        return $this->hasOne('Api\Model\IntraHyperRoute', 'id', 'route_id');
    }

}
