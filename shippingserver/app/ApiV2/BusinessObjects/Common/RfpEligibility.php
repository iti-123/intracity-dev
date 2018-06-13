<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-04-2017
 * Time: 21:48
 */

namespace ApiV2\BusinessObjects\Common;


/**
 * Class RfpEligibility
 * @package Api\BusinessObjects\Common
 * @ExclusionPolicy("none")
 */
class RfpEligibility
{

    /**
     * @Type("string")
     * @SerializedName("avgTurnOverLastThreeYears")
     */
    public $avgTurnOverLastThreeYears;

    /**
     * @Type("string")
     * @SerializedName("incometaxAssement")
     */
    public $incometaxAssement;

    /**
     * @Type("string")
     * @SerializedName("numberOfYearsInBusiness")
     */
    public $numberOfYearsInBusiness;

    /**
     * @Type("string")
     * @SerializedName("termContractWithOther")
     */
    public $termContractWithOther;


}
