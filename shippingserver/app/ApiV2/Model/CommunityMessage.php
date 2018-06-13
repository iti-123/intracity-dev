<?php

namespace ApiV2\Model;

use Illuminate\Database\Eloquent\Model;

class CommunityMessage extends Model
{

    public $timestamps = false;
    protected $fillable = ['created_at', 'upodated_at'];
    protected $table = 'community_message';
}
