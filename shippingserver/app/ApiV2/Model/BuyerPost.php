<?php

namespace ApiV2\Model;

use Illuminate\Database\Eloquent\Model;

class BuyerPost extends Model
{
    public $timestamps = false;
    protected $table = 'shp_buyer_posts';

    //adding comment

    public function selectedSellers()
    {
        return $this->hasMany('ApiV2\Model\SelectedSellers', 'post_id');
    }
}
