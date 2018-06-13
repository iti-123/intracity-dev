<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/3/17
 * Time: 4:17 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class CacheControl extends Model
{

    public $timestamps = false;
    protected $table = 'shp_cache_control';

}