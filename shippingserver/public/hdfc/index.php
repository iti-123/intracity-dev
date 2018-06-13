<html>
<form method="post" action="https://secure.ebs.in/pg/ma/sale/pay" name="frmPaymentConfirm">
    <?php
    $ebsuser_name = "GIRIVASAN";
    $ebsuser_address = "Yoga NarasimhamYoga Narasimham38th StreetTG Nagar";
    $ebsuser_zipcode = "500081";
    $ebsuser_city = "Hyderabad";
    $ebsuser_state = "Telengana";
    $ebsuser_country = "IND";
    $ebsuser_phone = "9766460575";
    $ebsuser_email = "chetan.padasalgi@techwave.net";
    $ebsuser_id = "1042";
    $modelno = "Test product";
    $key = "209b9b0bffc0ddafbbd0047a892b9dd3";
    $account_id = "20150";
    $finalamount = 1;
    $order_no = "123456";
    $return_url = "http://api.logistiks.techwave.net/hdfc/response.php?DR={DR}";
    $mode = "TEST";
    $hash = $key . "|" . $account_id . "|" . $finalamount . "|" . $order_no . "|" . $return_url . "|" . $mode;
    $secure_hash = md5($hash);
    ?>
    <input name="account_id" value="<?php echo $account_id; ?>" type="hidden">
    <input name="return_url" size="60" value="<?php echo $return_url; ?>" type="hidden">
    <input name="mode" size="60" value="TEST" type="hidden">
    <input name="reference_no" value="<?php echo $order_no; ?>" type="hidden">
    <input name="description" value="<?php echo $modelno; ?>" type="hidden">
    <input name="name" maxlength="255" value="<?php echo $ebsuser_name; ?>" type="hidden">
    <input name="address" maxlength="255" value="<?php echo $ebsuser_address; ?>" type="hidden">
    <input name="city" maxlength="255" value="<?php echo $ebsuser_city; ?>" type="hidden">
    <input name="state" maxlength="255" value="<?php echo $ebsuser_state; ?>" type="hidden">
    <input name="postal_code" maxlength="255" value="<?php echo $ebsuser_zipcode; ?>" type="hidden">
    <input name="country" maxlength="255" value="<?php echo $ebsuser_country; ?>" type="hidden">
    <input name="phone" maxlength="255" value="<?php echo $ebsuser_phone; ?>" type="hidden">
    <input name="email" size="60" value="<?php echo $ebsuser_email; ?>" type="hidden">
    <input name="secure_hash" size="60" value="<?php echo $secure_hash; ?>" type="hidden">
    <input name="amount" id="amount" readonly="" value="<?php echo $finalamount; ?>" type="hidden">
    <p>Your transaction will not be billed yet.</p>
    <input value="Place an Order" id="submit" name="submit" type="submit">
</form>
</html>
