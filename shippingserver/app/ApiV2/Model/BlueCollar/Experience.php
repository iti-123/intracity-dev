<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 24/2/17
 * Time: 12:03 AM
 */

namespace ApiV2\Model\BlueCollar;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $table = 'bluecollar_seller_reg_experience';

    public function regSeller()
    {
        return $this->belongsTo('ApiV2\Model\BlueCollar\SellerRegistration', 'bc_reg_id');
    }

}
