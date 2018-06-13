<?php

namespace ApiV2\Model;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{

    public $timestamps = true;
    protected $table = 'lkp_email_templates';

}