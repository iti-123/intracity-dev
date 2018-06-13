<?php

namespace ApiV2\Controllers;

interface IUserServices
{
    public function getAllSeller();

    public function getAllBuyer();

    public function getUserById($id);

    public function getCurrentUserDetails();

    public function getuseremail($searchval);

    public function getBuyerPostMasterCounts();

    public function getSellerPostMasterCounts();

    public function getNameList($searchval);

    public function getAllUsers($searchval);
}