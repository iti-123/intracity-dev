<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 05-02-2017
 * Time: 20:21
 */

namespace ApiV2\Transformers;


use ApiV2\BOs\SellerPostBO;
use League\Fractal\TransformerAbstract;

class SellerPostBOTransformer extends TransformerAbstract
{
    /**
     * @param SellerPostBO $in
     * @return array
     */
    public function transform(SellerPostBO $in)
    {
        $data_sellerpost = array(
            // 'id' => $in->
            'lkp_service_id' => $in->serviceId,
            'lkp_service_subcategory' => $in->serviceSubcategory,
            'seller_id' => $in->sellerId,
            'post_title' => $in->postTitle,
            'valid_from' => $in->validFrom,
            'valid_to' => $in->validTo,
            'lkp_post_status_id' => $in->postStatusId,
            'tracking' => $in->tracking,
            'terms_conditions' => $in->termsConditions,
            /*   'lkp_access_id' => $in->,
               'view_count' => $in->,
               'created_by' => $in->,
               'updated_by' => $in->,
               'created_ip' => $in->,
               'updated_ip' => $in->,
               'attributes' => $in->,
               'created_at' => $in->,
               'updated_at' => $in->, */
            'isTandCAccepted' => $in->isTermsAccepted,
        );
        return $data_sellerpost;

    }

}