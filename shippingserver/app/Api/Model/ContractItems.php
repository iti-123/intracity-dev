<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 4/20/2017
 * Time: 10:41 AM
 */

namespace Api\Model;

use Illuminate\Database\Eloquent\Model;

class ContractItems extends Model
{
    public $timestamps = false;
    protected $table = 'shp_contract_items';
}