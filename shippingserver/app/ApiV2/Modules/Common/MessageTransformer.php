<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 2/27/2017
 * Time: 5:34 PM
 */

namespace ApiV2\Modules\Common;


use ApiV2\Framework\SerializerServiceFactory;

class MessageTransformer
{
    public function ui2bo_save($payload)
    {

        //Convert the request JSON into a BO
        $serializer = SerializerServiceFactory::create();
        $post = $serializer->deserialize($payload, 'Api\BusinessObjects\PrivateMessagesBO', 'json');
        return $post;

    }

    public function model2boGet($model)
    {
        // TODO: Implement model2boGet() method.
    }


    public function model2boSave($model)
    {
        $response = 'MessageTransformer.model2boSave() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function bo2modelDelete($bo)
    {
        $response = 'MessageTransformer.bo2modelDelete() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function model2boDelete($model)
    {
        $response = 'MessageTransformer.model2boDelete() called';
        LOG::info($response);
        return ((array)$response);
    }


    public function bo2modelGet($bo)
    {
        $response = 'MessageTransformer.bo2modelGet() called';
        LOG::info($response);
        return ((array)$response);
    }

}