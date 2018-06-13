<?php

namespace ApiV2\Model;

use Illuminate\Database\Eloquent\Model;

class IntracityQuotaion extends Model
{
    protected $table = 'intra_hp_post_quotations';

    public function route()
    {
        return $this->hasOne('ApiV2\Model\IntraHyperBuyerPost', 'id', 'post_id');
    }

    public function post()
    {
        return $this->hasOne('ApiV2\Model\IntraHyperRoute', 'id', 'route_id');
    }

}
