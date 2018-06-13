<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/3/2017
 * Time: 3:35 PM
 */

namespace Api\Model;

use Illuminate\Database\Eloquent\Model;

class SelectedBuyer extends Model
{
    //
    public $timestamps = false;
    protected $table = 'shp_seller_post_selected_buyers';
}