<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 13:45
 */

namespace Api\Framework;


interface IBuyerPostTransformer
{

    public function ui2bo_save($payload, $leadType);

    public function ui2bo_filter($payload);

    public function bo2modelGet($bo);

    public function model2boGet($model);

    public function model2boGetAll($model);

    public function term_xls2bo_save(array $master = [], array $details = [], array $sellers = []);

    public function spot_xls2bo_save(array $master = [], array $details = [], array $sellers = []);

    public function model2boSave($model);

    public function bo2modelDelete($bo);

    public function model2boDelete($model);

}