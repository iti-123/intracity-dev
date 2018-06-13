<?php

define('POSTMESSAGETYPE', 2);
define('ORDERMESSAGETYPE', 3);
define('POSTQUOTEMESSAGETYPE', 4);
define('POSTENQURYMESSAGETYPE', 5);
define('LEADSMESSAGETYPE', 6);
define('CONTRACTMESSAGETYPE', 7);

define('SELLER_CREATED_POSTS', 'Seller created Posts');
define('SELLER_UPDATED_POSTS', 'Seller updated Posts');
define('SELLER_SUBMIT_QUOTE', 'Seller submit a quote');

define('BUYER_UPDATE_QUOTE', ' Buyer update quote');
define('BUYER_ADDED_NEW_QUOTE', ' Buyer add new Quote');
define('BUYER_ADDED_NEW_Contract', ' Buyer new Contract Saved');

define('FCL_SELLER_QUOTE_UPDATE', 'FCL updated quote');
define('FCL_SELLER_ADDED_NEW_QUOTE', 'FCL Seller add new Quote');

if (isset($_SERVER['HTTP_REFERER'])) {
    define('HTTP_REFERRER', $_SERVER['HTTP_REFERER']);
} else {
    define('HTTP_REFERRER', 'No Page');
}

//define('HTTP_REFERRER', '127.0.0.1');

$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "http://localhost:8000/";
$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "index.php";

define('CURRENT_URL', 'http://' . $host . $uri);

define('SELLER', '2');
define('BUYER', '1');
define('ADMIN', '1');
define('DEALER', 3);


//Define Post Statuses
define('SAVEDASDRAFT', 1);
define('OPEN', 2);
define('CLOSED', 3);
define('BOOKED', 4);
define('CANCELLED', 5);
define('INCART', 6);
define('ORDERED', 7);
define('ABANDONED', 8);
define('SAVEDASTEMPLATE', 9);
define('SELLER_CREATED_POST_FOR_BUYERS', 6);
define('BUYER_CREATED_POST_FOR_SELLERS', 12);
define('BUYER_UPDATED_BIDCLOSE_DATE', 21);
define('SELLER_QUOTE_SUBMITTED_TERM', 22);
define('SELLER_GIVEN_INITAL_QUOTE', 7);
define('SELLER_GIVEN_FINAL_QUOTE', 8);
define('SELLER_ACCEPTED_FIRM_OFFER', 9);
define('BUYER_GIVEN_COUNTER_OFFER', 16);

define('ORDER_STAUS_EMAIL_ALERT', 54);
define('ORDER_STAUS_EMAIL_ALERT_NEEDED_RESPONSE', 55);

//Definition of various Services
define('FCL', 22);
define('LCL', 23);
define('AirFreight', 34);
define('ROR0', 25);

define('FCL_NAME', 'FCL');
define('LCL_NAME', 'LCL');
define('AirFreight_NAME', 'AIRFREIGHT');
define('ROR0_NAME', 'RORO');


define('COURIER', 21);

define('ROAD', 160);
define('ORDERSCOUNT', 170);
define('ORDERSINVUDUAL', 180);
define('AIR', 'airtotal');
define('INTERNATIONAL_TYPE_AIR', 1);
define('INTERNATIONAL_TYPE_OCEAN', 2);
define('OTP_EXPIRE', 30);
define('OTP_EXPIRE_SEC', 60);
define('MAIL_EXPIRE', 7);

define('PRICE_EXCLUSIVE', 2);
define('PRICE_INCLUSIVE', 1);

//Define public or private
define('PUBLIC', 0);
define('PRIVATE', 1);
define('IS_ACTIVE', 1);
define('IS_ACCESS_PRIVATE', 2);
define('COMPETITIVE', 2);
define('FIRM', 2);
//Define lead types
define('FTL_SPOT', 1);
define('FTL_LEAD', 2);

//Definitions of email component constants
define('FROM_EMAIL', 'info@logistiks.com');
define('APPL_TITLE', 'LOGISTIKS');

define('UPLOAD_FILE', 'User upload a file');
define('SMS_SEND_TO_USER', 'SMS sent to use.');

////////////////////////////////////////
/////////  Use Cases
////////////////////////////////////////

define('USECASE_SELLERPOST', 'uc_sellerpost');
define('USECASE_BUYERPOST', 'uc_buyerpost');
define('USECASE_ORDERS', 'uc_orders');
define('USECASE_SELLERQUOTE', 'uc_sellerquote');

////////////////////////////////////////
/////////  Business Events
////////////////////////////////////////

define('SELLER_CREATED_POST_FOR_BUYERS_SMS', 2);
define('BUYER_CREATED_POST_FOR_SELLERS_SMS', 3);
define('SELLER_SUBMITT_QOUTE_SMS', 4);
define('BUYER_BOOKS_CONSIGNMENT_SPOT_TERM', 5);
define('SELLER_SUBMITT_QOUTE_TERM_SMS', 6);
define('TRUCK_PLACEMENT', 7);
define('CONSIGNMENT_PICK_UP', 8);
define('CONSIGNMENT_DELIVERED', 9);
define('BUYER_COUNTER_OFFER_SMS', 10);
define('CONTRACT_ISSUANCE', 12);
define('CONTRACT_ACCEPTANCE_REJECTION', 13);
define('BUYER_CREATED_POST_FOR_SELLERS_TERM_SMS', 14);
define('SELLER_UPDATED_POST_FOR_BUYERS_SMS', 15);
define('INTRACITY_BOOKED_POST_SMS', 16);
define('INTRACITY_BOOKED_POST_ACKOWLEDGEMENT_SMS', 17);
define('REGISTRATION_OTP_SMS', 18);
define('SELLER_SUBMITT_QOUTE_HANDLING_SMS', 20);
define('REGISTRATION_REQUEST_SMS', 21);
define('INTRACITY_SELLERS_UPLOAD', 22);

//SMS Gateway Configurations
define('SMS_GATEWAY_ENABLED', 1);


define('SHOW_SERVICE_TAX', 1);
define('SHOW_SWACHH_BHARAT_CESS', 1);
define('SHOW_KRISHI_KALYAN_CESS', 1);
//Start : Service Tax Constants
define('SERVICE_TAX_FRIEGHT_MIN', 1500); // service tax applicable min frieght
define('SERVICE_TAX_NOT_APPLICABLE_STATES', serialize(array('Jammu_Kashmir' => 15)));
define('ABATEMENT_SERVICE_TAX', 4.2);
define('ABATEMENT_SWACHH_BHARAT_CESS', 0.15);
define('ABATEMENT_KRISHI_KALYAN_CESS', 0.15);
define('SERVICE_TAX', 14);
define('SWACHH_BHARAT_CESS', 0.5);
define('KRISHI_KALYAN_CESS', 0.5);


define('TRANSPORT', 1);
define('OTHERS', 2);
define('PERCENT40', 40);
define('PERCENT14', 14.5);
define('PERCENT4', 4);

define('ROADTFTL', 'Road FTL');
define('ROADTLTL', 'Road LTL');
define('ROADTINTRA', 'Road Intracity');

define('SHIPPING_MODULES', serialize(array(
        22 => 'FCL',
        23 => 'LCL',
        24 => 'AirFreight',
        25 => 'RoRo',
    ))
);

define('SHIPPING__SERVICE_SUBTYPES', serialize(array(
        'P2P' => 'Port to Port',
        'P2D' => 'Port to Door',
        'D2D' => 'Door to Door',
        'D2P' => 'Door to Port',
    ))
);


define('CURRENCY_COUNTRY', 'IND');
define('CURRENCY_TYPE', 'INR');


const ENTITY_SELLER_POST = "sp";

const ENTITY_BUYER_POST = "bp";

const ENTITY_SELLER_QUOTE = "sq";

const ENTITY_ORDER = "o";

const ENTITY_MESSAGE = "m";

const ENTITY_CONTRACT = "contract";

define('CLIENT_REDIRECT', env('RETURN_BACK_URL', 'http://localhost/shippingclient/index.html#/payment-success/HDFC'));

// Codded By Mindz Technology

define('_INTRACITY_', 3);
define('_HYPERLOCAL_', 47);
define('_BLUECOLLAR_', 46);

define('INTRA_HYPER_SPOT', 1);
define('INTRA_HYPER_TERM', 2);

define('INTRA_HYPER_HOURS', 1);
define('INTRA_HYPER_DISTANCE', 2);

define('_PUBLIC_', 0);
define('_PRIVATE_', 1);
define('ROUTE_WISE', 1);
define('ON_ALL_ROUTE', 2);
define('SOLR_BASE_URL', env('SOLR_BASE_URL'));

define('RECORD_PER_PAGE',10);

define('HDFC_PAYMENT_GATEWAY_ACCOUNT_ID', 20150);
define('HDFC_PAYMENT_GATEWAY_ACCOUNT_SECRET_KEY','209b9b0bffc0ddafbbd0047a892b9dd3');

define('_OPEN_',1);
define('_SAVEASDRAF_',0);

define('PAYMENT_SUCCESS','http://localhost/intracity-dev/intracity/index.html#/success/');
define('PAYMENT_FAILED','http://localhost/intracity-dev/intracity/index.html#/failed');

define('TRUCK_PLACED',2);

define('CONSIGNMENT_DETAIL_CONFIRMED',3);

define('TRANSIT_DETAIL_CONFIRMED', 4);

define('DELIVERY_DETAIL_CONFIRMED',5);

define('DELIVERY_CONFIRMED_BY_BUYER', 6);

define('PATH_TO_STORAGE','http://localhost/intracity-dev/shippingserver/storage/app/');