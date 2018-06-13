<?php

namespace Api\Model;

use Illuminate\Database\Eloquent\Model;

class SellerPostRoute extends Model
{
    protected $table = 'intra_hp_buyer_seller_routes';
    protected $fillable = ['is_seller_buyer', 'type_basis'];
}
