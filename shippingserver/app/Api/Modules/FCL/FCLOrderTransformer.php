<?php
/**
 * Created by PhpStorm.
 * User: sainath
 * Date: 2/21/17
 * Time: 7:10 PM
 */

namespace Api\Modules\FCL;


use Api\Framework\IOrderTransformer;
use Api\Framework\SerializerServiceFactory;

class FCLOrderTransformer implements IOrderTransformer
{

    public function ui2bo_save($payload)
    {
        $serializer = SerializerServiceFactory::create();
        $post = $serializer->deserialize($payload, 'Api\Modules\FCL\FCLOrderBO', 'json');
        return $post;
    }

    public function ui2bo_save_updatebo($payload)
    {
        //Convert the request JSON into a BO
        $serializer = SerializerServiceFactory::create();
        $post = $serializer->deserialize($payload, 'Api\Modules\FCL\FCLOrderUpdateBo', 'json');
        return $post;

    }
}