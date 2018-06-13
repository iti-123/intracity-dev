<?php
$secret_key = "209b9b0bffc0ddafbbd0047a892b9dd3";
if (isset($_GET['DR'])) {
    require('Rc43.php');
    $DR = preg_replace("/\s/", "+", $_GET['DR']);
    $rc4 = new Crypt_RC4($secret_key);
    $QueryString = base64_decode($DR);
    $rc4->decrypt($QueryString);
    $QueryString = explode('&', $QueryString);
    $response = array();
    foreach ($QueryString as $param) {
        $param = explode('=', $param);
        $response[$param[0]] = urldecode($param[1]);
    }
    if (($response['ResponseCode'] == 0)) {
        foreach ($response as $key => $value) {
            echo $key . ' = ';
            echo $value . '<br>';
        }
    }
    // payment failed
    if (($response['ResponseCode'] != 0)) {
        foreach ($response as $key => $value) {
            echo $key;
            echo $value;
        }
    }
}
?>
