<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShippingLogs extends Model
{
    public $timestamps = false;
    protected $table = 'shp_audit_log';
}
