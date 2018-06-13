<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 12:30 PM
 */

namespace Api\Model;

use Illuminate\Database\Eloquent\Model;

class SellerQuotes extends Model
{
    public $timestamps = false;
    protected $table = 'shp_seller_quotes';

    public function order()
    {
        return $this->belongsTO('Api\Model\Order', 'course_id', 'id');
    }
    //adding comment
}