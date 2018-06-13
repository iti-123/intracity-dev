<?php

namespace Api\Services\Payments;

class PaymentService
{

    public static function createPaymentObj($paymentType)
    {
        if ($paymentType == 'HDFC') {
            return new HDFCPaymentService();
        }
    }

}