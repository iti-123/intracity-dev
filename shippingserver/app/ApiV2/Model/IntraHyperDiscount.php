<?php

namespace ApiV2\Model;

use Illuminate\Database\Eloquent\Model;

class IntraHyperDiscount extends Model
{

    protected $fillable = [
        'fk_rate_card_id',
        'lkp_service_id',
        'intra_hp_sellerpost_ratecart_id',
        'buyer_id',
        'disc_amt',
        'disc_type',
        'discount_basis',
        'discount_level',
        'net_price',
        'credit_days'
    ];

    protected $table = 'intra_hp_discounts';

}
