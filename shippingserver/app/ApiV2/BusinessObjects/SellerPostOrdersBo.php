<?php

namespace ApiV2\BusinessObjects;

/**
 * Class SellerPostOrdersBo
 * @package Api\BusinessObjects
 * @ExclusionPolicy("none")
 */
class SellerPostOrdersBo
{

    /**
     * @Type("string")
     * @SerializedName("title")
     */
    public $title;

    /**
     * @Type("string")
     * @SerializedName("loadPort")
     */
    public $loadPort = "Multi";

    /**
     * @Type("string")
     * @SerializedName("loadPort")
     */
    public $dischargePort = "Multi";

    /**
     * @Type("string")
     * @SerializedName("postType")
     */
    public $postType = "Public";

    /**
     * @Type("array")
     * @SerializedName("postType")
     */
    public $containerType = [];

}