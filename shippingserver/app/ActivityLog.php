<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    //
    public $timestamps = false;
    protected $table = 'log_activity';
}
