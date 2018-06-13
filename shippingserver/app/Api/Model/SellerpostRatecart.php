<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 12:30 PM
 */

namespace Api\Model;

use Illuminate\Database\Eloquent\Model;

class SellerpostRatecart extends Model
{
    public $timestamps = false;
    protected $table = 'intra_hp_sellerpost_ratecart';

    public function postRoute()
    {
        return $this->hasMany('Api\Model\SellerPostRoute', 'fk_buyer_seller_post_id', 'id')->where('is_seller_buyer', '=', 2);
    }
}