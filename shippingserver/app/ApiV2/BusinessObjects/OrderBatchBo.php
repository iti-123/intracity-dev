<?php

namespace ApiV2\BusinessObjects;

class OrderBatchBo
{

    /**
     * @Type("string")
     * @SerializedName("orderBatchId")
     */
    public $orderBatchId;

    /**
     * @Type("string")
     * @SerializedName("buyerId")
     */
    public $buyerId;

    /**
     * @Type("string")
     * @SerializedName("amountToPay")
     */
    public $amountToPay;

    /**
     * @Type("string")
     * @SerializedName("amountReceived")
     */
    public $amountReceived;

    /**
     * @Type("string")
     * @SerializedName("paymentSuccess")
     */
    public $paymentSuccess;

    /**
     * @Type("array<Api\BusinessObjects\OrderBo>")
     * @SerializedName("orders")
     */
    public $orders;

}