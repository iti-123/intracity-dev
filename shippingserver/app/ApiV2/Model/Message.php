<?php

namespace ApiV2\Model;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    public $timestamps = false;
    protected $fillable = ['created_at', 'upodated_at'];
    protected $table = 'user_messages';
}
