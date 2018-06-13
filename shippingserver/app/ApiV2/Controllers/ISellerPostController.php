<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 22:02
 */

namespace ApiV2\Controllers;

use Illuminate\Http\Request;

interface ISellerPostController
{
    public function getPostById($id);

    public function saveOrUpdate(Request $request);

    public function filter(Request $request);

    public function postMasterFilter(Request $request);

    public function postMasterInbound(Request $request);

    public function getAllPosts();

    public function getAllPostsByPostPrivacy($postType);
    //public function bulkSaveOrUpdate(Request $request);
}