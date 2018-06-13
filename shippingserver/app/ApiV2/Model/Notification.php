<?php

namespace ApiV2\Model;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;
    protected $table = 'user_notifications';
}
