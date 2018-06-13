<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 03-02-2017
 * Time: 14:05
 */

namespace Api\Services;

use Api\BusinessObjects\BuyerPostBO;
use Api\BusinessObjects\BuyerPostSearchBO;
use Api\BusinessObjects\ContractBO;

interface IBuyerPostService
{

    /**
     * Setter for service factory
     * @param $factory
     * @return mixed
     */
    public function setServiceFactory($factory);

    /**
     * Get a BuyerPost
     * @param $id
     * @return mixed
     */
    public function getPostById($id);

    /**
     * Get all BuyerPosts
     */
    public function getAllPosts();

    /**
     * Saves Buyer post
     * @param BuyerPostBO $post
     * @return mixed
     */
    public function saveOrUpdateTerm(BuyerPostBO $post);

    /**
     * Saves one or more Buyer posts within a single unit of work
     * @param BuyerPostBO $post
     * @return mixed
     */
    public function saveOrUpdateSpots(array $post);


    /**
     * Retreive Posts that matches the given criteria
     * @param $criteria
     */
    public function filterPost(BuyerPostSearchBO $criteria);

    /**
     * Save Contract
     * @param ContractBO $bo
     */
    public function saveGenerateContract(ContractBO $bo);

    /**
     * Retreive Posts that matches the given criteria
     * @param $criteria
     */
    public function postMasterFilters(BuyerPostSearchBO $criteria);

}