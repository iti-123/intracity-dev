<?php
/**
 * Created by PhpStorm.
 * User: sainath
 * Date: 4/3/17
 * Time: 6:07 PM
 */

namespace Api\BusinessObjects;

/**
 * Class OrderUpdateBO
 * @package Api\BusinessObjects
 * @ExclusionPolicy("none")
 *
 */
class OrderUpdateBO
{

    /**
     * @Type("string")
     * @SerializedName("transition")
     */
    public $transition;

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

    /**
     * @Type("string")
     * @SerializedName("documentType")
     */
    public $documentType;

    /**
     * @Type("string")
     * @SerializedName("etd")
     */
    public $etd;

    /**
     * @Type("string")
     * @SerializedName("cycuttOffDate")
     */
    public $cycuttOffDate;

    /**
     * @Type("string")
     * @SerializedName("comments")
     */
    public $comments;

    /**
     * @Type("string")
     * @SerializedName("dateOfGoodsOnBoarded")
     */
    public $dateOfGoodsOnBoarded;

    /**
     * @Type("string")
     * @SerializedName("dateOfIssuedOBL")
     */
    public $dateOfIssuedOBL;

    /**
     * @Type("string")
     * @SerializedName("dateOfGoodsDelivered")
     */
    public $dateOfGoodsDelivered;

    /**
     * @Type("string")
     * @SerializedName("cyCutOffDate")
     */
    public $cyCutOffDate;

    /**
     * @Type("string")
     * @SerializedName("ctd")
     */
    public $ctd;

    /**
     * @Type("string")
     * @SerializedName("dateOfCustomClearance")
     */
    public $dateOfCustomClearance;

    /**
     * @Type("string")
     * @SerializedName("vehicleReachedDate")
     */
    public $vehicleReachedDate;

    /**
     * @Type("string")
     * @SerializedName("dateOfGoodsDeliveredByBuyer")
     */
    public $dateOfGoodsDeliveredByBuyer;

}