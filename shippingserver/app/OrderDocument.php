<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class OrderDocument extends Model
{
    public $timestamps = false;
    protected $table = 'intra_hp_order_documents';
}
