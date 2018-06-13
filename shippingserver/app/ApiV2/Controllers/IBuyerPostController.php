<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 22:02
 */

namespace ApiV2\Controllers;

use Illuminate\Http\Request;

interface IBuyerPostController
{
    public function getGeneratedContractsByPostId($id);

    public function getPostById($id);

    public function saveOrUpdateTerm(Request $request);

    public function saveGenerateContract(Request $request);

    public function saveOrUpdateSpot(Request $request);

    public function filter(Request $request);

    public function postMasterFilter(Request $request);

    public function getAllPosts();

    public function getAllSpotPosts();

    public function getAllTermPosts();

    public function getAllPostsByPostPrivacy($postType);

    public function uploadSpotExcel(Request $request);

    public function uploadTermExcel(Request $request);

}