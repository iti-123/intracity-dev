<?php

namespace Api\Model;

use Illuminate\Database\Eloquent\Model;

class SelectedSellers extends Model
{
    //
    public $timestamps = false;
    protected $table = 'shp_buyer_post_selected_sellers';

    public function postId()
    {
        return $this->belongsTo('Api\Model\BuyerPost', 'postId');
    }
}
