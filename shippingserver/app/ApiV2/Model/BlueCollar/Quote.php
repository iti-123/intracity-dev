<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 24/2/17
 * Time: 12:03 AM
 */

namespace ApiV2\Model\BlueCollar;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $table = 'intra_hp_post_quotations';

    public function sellerData()
    {
        return $this->hasOne('ApiV2\Model\UserDetails', 'id', 'seller_id')->select('id', 'username');
    }

    public function buyerData()
    {
        return $this->hasOne('ApiV2\Model\UserDetails', 'id', 'buyer_id')->select('id', 'username');
    }

}
