<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 3/20/17
 * Time: 11:57 AM
 */

namespace ApiV2\BusinessObjects;


class BuyerPostMasterInboundResults
{
    //Associate array Group Name -> BuyerPostMasterInboundGroup
    public $groups = [];

    //Global unique facets across all groups
    public $facets = [];

}

class BuyerPostMasterInboundGroup
{

    public $groupName;

    public $postId = [];

    public $countOfPosts;

    public $countOfMessages;

    public $countOfDocuments;

}
