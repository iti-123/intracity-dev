<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 24/2/17
 * Time: 12:03 AM
 */

namespace Api\Model\BlueCollar;

use Illuminate\Database\Eloquent\Model;

class SellerRegExperience extends Model
{
    protected $table = 'bluecollar_seller_reg_experience';

    public function regSeller()
    {
        return $this->belongsTo('Api\Model\BlueCollar\SellerRegistration', 'bc_reg_id');
    }

}
