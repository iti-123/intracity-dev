<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 27/2/17
 * Time: 3:43 PM
 */


namespace Api\Services;

use Api\Controllers\AbstractUserServices;
use Illuminate\Support\Facades\Input;
use Tymon\JWTAuth\Facades\JWTAuth;

class PaymentService
{
    public function hdfcFields($params)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $hashData = HDFC_PAYMENT_GATEWAY_ACCOUNT_SECRET_KEY;
        $hashMethod = HDFC_HASHING_METHOD;
//        $user_det = UserDetailsService::getUserNameDetails();
        $userobj = new AbstractUserServices();
        $user_det = $userobj->getUserDetailsById($userId);
//        dd($user_det);

//        $PaymentFields = array(
//            'account_id' => HDFC_PAYMENT_GATEWAY_ACCOUNT_ID,
//            'address' => $userAddress,
//            'amount' => $params['amount'],
//            'channel' => '10',
//            'city' => $user_det->principal_place,
//            'country' => CURRENCY_COUNTRY,
//            'currency' => CURRENCY_TYPE,
//            'description' => 'Test Product',
//            'email' => $user_det->contact_email,
//            'mode' => HDFC_PAYMENT_GATEWAY_MODE,
//            'name' => $user_det->contact_firstname,
//            'phone' => $user_det->contact_mobile,
//            'postal_code' => '400069',
//            'reference_no' => $params['refference_id'],
//            'return_url' => url(HDFC_PAYMENT_GATEWAY_RETURN_URL_ACTION),
//            'payment_mode' => $payment_mode[$params['payment_mode']],
//        );
//
//        ksort($PaymentFields);
//
//        foreach ($PaymentFields as $key => $value) {
//            if (strlen($value) > 0) {
//                $hashData .= '|' . $value;
//            }
//        }
//
//        if (strlen($hashData) > 0) {
//            $PaymentFields['secure_hash'] = strtoupper(hash($hashMethod, $hashData));
//        }

        $ebsuser_name = $user_det->firstname . ' ' . $user_det->lastname;
        $ebsuser_address = $user_det->address1 . ' ' . $user_det->address2 . ' ' . $user_det->address3;
        $ebsuser_zipcode = $user_det->pincode;
        $ebsuser_city = $user_det->principal_place;
        $ebsuser_state = $user_det->state;
        $ebsuser_country = CURRENCY_COUNTRY;
        $ebsuser_phone = $user_det->mobile;
        $ebsuser_email = "chetan.padasalgi@techwave.net";
        $description = "Test product";
        $key = HDFC_PAYMENT_GATEWAY_ACCOUNT_SECRET_KEY;
        $account_id = HDFC_PAYMENT_GATEWAY_ACCOUNT_ID;
        $finalamount = $params['amount'];
        $order_no = $params['refference_id'];
        $return_url = url(HDFC_PAYMENT_GATEWAY_RETURN_URL_ACTION);
        $mode = HDFC_PAYMENT_GATEWAY_TESTMODE;
        $hashData = $key . "|" . $account_id . "|" . $finalamount . "|" . $order_no . "|" . $return_url . "|" . $mode;
        $secure_hash = md5($hashData);
//        $secure_hash = hash($hashMethod, $hashData);
        $postFields = "account_id=$account_id&return_url=$return_url&mode=$mode&reference_no=$order_no&description=$description&name=$ebsuser_name&address=$ebsuser_address&city=$ebsuser_city&state=$ebsuser_state&postal_code=$ebsuser_zipcode&country=$ebsuser_country&phone=$ebsuser_phone&email=$ebsuser_email&secure_hash=$secure_hash&amount=$finalamount";

        $url = HDFC_PAYMENT_GATEWAY_URL_NEW;

        $ch = curl_init();
        if (!$ch) {
            die("Couldn't initialize a cURL handle");
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $curlresponse = curl_exec($ch); // execute
//        dd($curlresponse);
        if (curl_errno($ch) || empty($ret)) {
            // some kind of an error happened
            return curl_error($ch);
            curl_close($ch); // close cURL handler
        } else {

            $info = curl_getinfo($ch);
            curl_close($ch); // close cURL handler
//            dd($info);
            return $curlresponse; //echo "Message Sent Succesfully" ;
        }

//        return $PaymentFields;
    }

    /**
     *   HDFC Gateway Responce
     */
    public function hdfcresponse()
    {

        $gatewayResponse = Input::all();
        dd($gatewayResponse);
        $ref_no = $gatewayResponse['MerchantRefNo'];
        $verified_status = 'Payment Failed';
        $order_status = 'Payment Failed';

        $getOrderLogDetails = LogPaymentGateway::where('order_payment_id', '=', $ref_no)->first();

        $storeData = array(
            'response' => serialize($gatewayResponse),
            'transaction_id' => $gatewayResponse['TransactionID'],
            'order_status' => $order_status,
            'verified_status' => $verified_status
        );

        //successs
        if ($gatewayResponse['ResponseCode'] == 0) {

            // Update Order Status
            CheckoutController::confirmOrderStatus($ref_no);

            if ($getOrderLogDetails->amount == $gatewayResponse['Amount']) {
                $storeData['verified_status'] = "Success";
                $storeData['order_status'] = "Success";
                $error = false;
                $order_status = '1';
            } else {//failure
                $storeData['verified_status'] = "Payment Fraud";
                $storeData['order_status'] = "Payment Fraud";
                $error = true;
                $order_status = '0';
            }

            Order::where('order_payment_id', '=', $ref_no)
                ->update(array('lkp_payment_status_id' => $order_status));

            if (!$error) {
                //return $this->_confirmOrder($ref_no,$getOrderLogDetails->amount);
                return redirect('confirmorder/' . base64_encode($ref_no));
            } else {
                return redirect('home')
                    ->with('message', 'Payment Failed');
            }
        } else {
            return redirect('home')
                ->with('message', 'Payment Failed');
        }
    }
}