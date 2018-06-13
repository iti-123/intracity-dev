<?php

namespace Api\Model;

use Illuminate\Database\Eloquent\Model;

class BuyerPost extends Model
{
    public $timestamps = false;
    protected $table = 'shp_buyer_posts';

    //adding comment

    public function selectedSellers()
    {
        return $this->hasMany('Api\Model\SelectedSellers', 'post_id');
    }
}
