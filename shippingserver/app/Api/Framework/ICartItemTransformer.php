<?php

namespace Api\Framework;


interface ICartItemTransformer
{

    public function ui2bo_save($payload);

    public function bo2modelGet($bo);

    public function model2boGet($model);


    public function model2boSave($model);

    public function bo2modelDelete($bo);

    public function model2boDelete($model);

}