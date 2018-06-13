<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/6/17
 * Time: 4:53 PM
 */

namespace ApiV2\Model;

use Illuminate\Database\Eloquent\Model;


class ServiceTax extends Model
{
    public $timestamps = false;
    protected $table = 'lkp_service_charges';

}