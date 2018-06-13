<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 3/20/17
 * Time: 11:57 AM
 */

namespace Api\BusinessObjects;


class SellerPostMasterInboundResults
{
    //Associate array Group Name -> SellerPostMasterInboundGroup
    public $groups = [];

    //Global unique facets across all groups
    //  public $facets = [];

}

class SellerPostMasterInboundGroup
{

    public $groupName;

    public $postId = [];

    public $countOfPosts;

    public $countOfMessages;

    public $countOfDocuments;

}
