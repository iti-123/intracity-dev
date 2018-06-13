<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 4/3/2017
 * Time: 10:30 AM
 */

namespace ApiV2\Model;

use Illuminate\Database\Eloquent\Model;

class BuyerContract extends Model
{
    public $timestamps = false;
    protected $table = 'shp_contract';

    public function getContractsByUserId($userId)
    {

        return $this
            ->where('buyerId', $userId)
            ->where('isSellerAccepted', 1)
            ->get();

    }

    public function getContractsById($id)
    {

        return $this
            ->where('id', $id)
            ->where('isSellerAccepted', 1)
            ->with('postDetails')
            ->with('orderDetails')
            ->get()
            ->first();

    }

    public function postDetails()
    {
        return $this->hasOne('ApiV2\Model\BuyerPost', 'id', 'buyerPostId');
    }

    public function orderDetails()
    {
        return $this->hasMany('ApiV2\Model\Order', 'buyer_post_id', 'buyerPostId');
    }

    public function contractDetails()
    {
        return $this->hasMany('ApiV2\Model\ContractItems', 'contractId', 'id');
    }

}