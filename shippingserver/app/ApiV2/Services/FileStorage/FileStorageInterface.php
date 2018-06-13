<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 13:48
 */

namespace ApiV2\Services\FileStorage;


interface FileStorageInterface
{
    public function upload();

    public function delete();
}