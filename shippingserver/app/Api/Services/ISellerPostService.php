<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 03-02-2017
 * Time: 14:05
 */

namespace Api\Services;

use Api\BusinessObjects\AbstractSearchBO;
use Api\BusinessObjects\SellerPostBO;
use Api\BusinessObjects\SellerPostSearchBO;


interface ISellerPostService
{

    public function setServiceFactory($factory);

    /*
     * Get a SellerPost
     * @id : Unique identifier of the post
     */
    public function getPostById($id);

    /*
     * Saves seller post
     */
    public function saveOrUpdate(SellerPostBO $post);


    /**
     * Retreive Posts that matches the given criteria
     * @param $criteria
     */
    public function filterPost(SellerPostSearchBO $bo);

    /**
     * Get all BuyerPosts
     */
    public function getAllPosts();

    /**
     * Retreive Posts that matches the given criteria
     * @param $criteria
     */
    public function postMasterFilters(SellerPostSearchBO $criteria);

    /**
     * Setup PostMaster that matches the given criteria
     * @param $criteria
     */
    public function postMasterInbound(AbstractSearchBO $filter);

}