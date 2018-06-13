<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/4/17
 * Time: 11:21 PM
 */

namespace Api\Modules\FCL;


class FCLSellerPostMasterInboundSearchResultsBO
{

    //Associate array Group Name -> SellerPostMasterInboundGroup
    public $groups = [];

    //Global unique facets across all groups
    public $facets = [];

}

class SellerPostMasterInboundGroup
{

    public $category;

    public $title;

    public $minLastDateForQuoteTime;

    public $maxLastDateForQuoteTime;

    public $countOfPosts;

    public $countOfMessages;

    public $postIds;

}