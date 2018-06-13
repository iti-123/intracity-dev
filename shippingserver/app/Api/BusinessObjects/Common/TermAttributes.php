<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-04-2017
 * Time: 21:46
 */

namespace Api\BusinessObjects\Common;


/**
 * Class TermAttributes
 * @package Api\BusinessObjects\Common
 * @ExclusionPolicy("none")
 */
class TermAttributes
{
    /**
     * @Type("string")
     * @SerializedName("validFrom")
     */
    public $validFrom;

    /**
     * @Type("string")
     * @SerializedName("validTo")
     */
    public $validTo;

    /**
     * @Type("string")
     * @SerializedName("emdAmount")
     */
    public $emdAmount;

    /**
     * @Type("string")
     * @SerializedName("emdMode")
     */
    public $emdMode;

    /**
     * @Type("string")
     * @SerializedName("awardCriteria")
     */
    public $awardCriteria;

    /**
     * @Type("string")
     * @SerializedName("contractAllotment")
     */
    public $contractAllotment;

    /**
     * @Type("string")
     * @SerializedName("paymentTerms")
     */
    public $paymentTerms;

    /**
     * @Type("string")
     * @SerializedName("credit")
     */
    public $credit;

    /**
     * @Type("string")
     * @SerializedName("creditDays")
     */
    public $creditDays;

    /**
     * @Type("string")
     * @SerializedName("comments")
     */
    public $comments;

    /**
     * @Type("Api\BusinessObjects\Common\RfpEligibility")
     * @SerializedName("rfpEligibility")
     */
    public $rfpEligibility;

    /**
     * @Type("array<Api\BusinessObjects\Common\BidTermsAndConditions>")
     * @SerializedName("bidTermsAndConditionsDocs")
     */
    public $bidTermsAndConditions = [];

    //Service type is moved to respective subclass

}
