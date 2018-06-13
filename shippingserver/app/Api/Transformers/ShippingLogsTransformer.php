<?php

namespace Api\Transformers;

use App\ShippingLogs as ShippingLogs;
use League\Fractal\TransformerAbstract;

class ShippingLogsTransformer extends TransformerAbstract
{
    public function transform(ShippingLogs $shippingLogs)
    {

        $data_shippinglogs = array(
            'entity_id' => $shippingLogs->entity_id,
            'entity' => $shippingLogs->entity,
            'post_data' => $shippingLogs->post_data,
        );
        return $data_shippinglogs;
    }
}