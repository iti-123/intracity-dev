<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 13:45
 */

namespace ApiV2\Framework;


interface ISellerPostTransformer
{

    public function ui2bo_save($payload);

    //  public function xls2bo_save(array $master = [],array $details = [],array $discounts = []);

    public function ui2bo_postmaster_filter($payload);

    public function ui2bo_postmasterinbound_filter($payload);

    public function ui2bo_filter($payload);

    public function bo2modelGet($bo);

    public function model2boGet($model);

    public function model2boGetAll($model);


    public function model2boSave($model);

    public function bo2modelDelete($bo);

    public function model2boDelete($model);

    public function bo2modelSave($bo);

}