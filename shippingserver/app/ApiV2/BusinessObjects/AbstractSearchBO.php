<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/18/17
 * Time: 3:04 PM
 */

namespace ApiV2\BusinessObjects;

class AbstractSearchBO
{

    /**
     * The entity being searched for
     * @Type("string")
     * @SerializedName("entity")
     */
    public $entity;

    /**
     * The start index of the resultset
     * @Type("integer")
     * @SerializedName("start")
     */
    public $start = 0;

    /**
     * The number of rows to return per page
     * @Type("integer")
     * @SerializedName("rows")
     */
    public $rows = 100;

    /**
     * The facets to use for filter as a name-value pair
     * @var array Associative array
     * @Type("array")
     * @SerializedName("facets")
     */
    public $facets = [];

}