<?php

namespace ApiV2\BusinessObjects\Common;

/**
 * /**
 * Created by PhpStorm.
 * User: 10325
 * Date: 01-04-2017
 * Time: 17:40
 */

/**
 * Class Document
 * @package Api\BusinessObjects\Common
 * @ExclusionPolicy("none")
 */
class Document
{

    /**
     * @Type("string")
     * @SerializedName("documentId")
     */
    public $documentId;

    /**
     * @Type("string")
     * @SerializedName("documentName")
     */
    public $documentName;
}