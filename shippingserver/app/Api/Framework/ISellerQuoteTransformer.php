<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 10:55 AM
 */

namespace Api\Framework;


interface ISellerQuoteTransformer
{
    public function ui2bo_save($payload, $leadType);

    public function model2boGet($model);

    public function model2boGetBuyerPost($bpIds);
}