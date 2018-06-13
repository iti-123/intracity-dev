<?php
/**
 * Created by PhpStorm.
 * User: sainath
 * Date: 3/15/17
 * Time: 12:36 PM
 */

namespace Api\Services\Payments;


use Api\BusinessObjects\HDFCPaymentBo;
use Api\Model\HDFCTransaction;
use Api\Services\EncryptionService;
use Api\Services\UserDetailsService;

class HDFCPaymentService
{

    protected $config;

    public function __construct()
    {

        $this->config = [
            'paymentUrl' => env('HDFC_PAYMENTURL'),
            'key' => env('HDFC_KEY'),
            'mode' => env('HDFC_MODE'),
            'accountId' => env('HDFC_ACCOUNTID'),
            'returnUrl' => env('HDFC_RETURNURL'),
        ];

    }


    public function getActionUrl()
    {
        return $this->config['paymentUrl'];
    }

    public function getFormFields($orderId, $amount, $userId)
    {

        $fields = [];
        $fields['account_id'] = $this->config['accountId'];
        $fields['return_url'] = $this->config['returnUrl'];
        $fields['mode'] = $this->config['mode'];
        $fields['reference_no'] = $orderId;
        $userDetails = UserDetailsService::getUserDetails($userId);
        $fields['name'] = $userDetails->username;
        $fields['email'] = $userDetails->email;
        $fields['description'] = "qwertyui";
        $fields['address'] = "Trunk Road";
        $fields['city'] = "Khammam";
        $fields['state'] = "Telangana";
        $fields['postal_code'] = "507003";
        $fields['country'] = "IND";
        $fields['phone'] = "9999999999";
        $fields['amount'] = $amount;
        $secureCode = $this->config['key']
            . "|" . $fields['account_id']
            . "|" . $fields['amount']
            . "|" . $fields['reference_no']
            . "|" . $fields['return_url']
            . "|" . $fields['mode'];
        $fields['secure_hash'] = md5($secureCode);

        return $fields;
    }

    public function rc4encrypt($key, $str)
    {
        $s = array();
        for ($i = 0; $i < 256; $i++) {
            $s[$i] = $i;
        }
        $j = 0;
        for ($i = 0; $i < 256; $i++) {
            $j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256;
            $x = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $x;
        }
        $i = 0;
        $j = 0;
        $res = '';
        for ($y = 0; $y < strlen($str); $y++) {
            $i = ($i + 1) % 256;
            $j = ($j + $s[$i]) % 256;
            $x = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $x;
            $res .= $str[$y] ^ chr($s[($s[$i] + $s[$j]) % 256]);
        }
        return $res;
    }

    public function saveTransaction($transactionDetails)
    {

        $secretKey = $this->config['key'];
        $encryptedData = preg_replace("/\s/", "+", $transactionDetails);
        $queryString = base64_decode($encryptedData);
        $rc64Data = EncryptionService::rc4($secretKey, $queryString);
        $explodedParams = explode("&", $rc64Data);
        $bo = new HDFCPaymentBo();
        foreach ($explodedParams as $explodedParam) {
            list($key, $value) = explode("=", $explodedParam, 2);
            $bo->$key = $value;
        }
        $model = $this->boToModel($bo);
        $model->save();
        $bo->paymentStatus = False;
        if ($bo->ResponseCode == 0) {
            $bo->paymentStatus = True;
        }
        $bo->orderId = $bo->MerchantRefNo;
        $bo->transactionId = $model->id;
        return $bo;

    }

    public function boToModel($bo)
    {

        $model = new HDFCTransaction();

        $model->response_message = $bo->ResponseMessage;
        $model->date_created = $bo->DateCreated;
        $model->payment_id = $bo->PaymentID;
        $model->order_id = $bo->MerchantRefNo;
        $model->amount = $bo->Amount;
        $model->mode = $bo->Mode;
        $model->billing_name = $bo->BillingName;
        $model->billing_address = $bo->BillingAddress;
        $model->billing_city = $bo->BillingCity;
        $model->billing_state = $bo->BillingState;
        $model->billing_postal_code = $bo->BillingPostalCode;
        $model->billing_country = $bo->BillingCountry;
        $model->billing_phone = $bo->BillingPhone;
        $model->billing_email = $bo->BillingEmail;
        $model->delivery_name = $bo->DeliveryName;
        $model->delivery_address = $bo->DeliveryAddress;
        $model->delivery_city = $bo->DeliveryCity;
        $model->delivery_state = $bo->DeliveryState;
        $model->delivery_postal_code = $bo->DeliveryPostalCode;
        $model->delivery_country = $bo->DeliveryCountry;
        $model->delivery_phone = $bo->DeliveryPhone;
        $model->description = $bo->Description;
        $model->is_flagged = $bo->IsFlagged;
        $model->transaction_id = $bo->TransactionID;
        $model->payment_method = $bo->PaymentMethod;

        return $model;
    }

}