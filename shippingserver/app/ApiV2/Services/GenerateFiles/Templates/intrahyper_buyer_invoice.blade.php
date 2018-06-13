<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <style type="text/css">
           
            span.cls_002{font-family:Times,serif;font-size:12.9px;color:rgb(84,84,84);font-weight:normal;font-style:normal;text-decoration: none}
            div.cls_002{font-family:Times,serif;font-size:12.9px;color:rgb(84,84,84);font-weight:normal;font-style:normal;text-decoration: none}
            span.cls_003{font-family:Times,serif;font-size:9.9px;color:rgb(84,84,84);font-weight:normal;font-style:normal;text-decoration: none}
            div.cls_003{font-family:Times,serif;font-size:9.9px;color:rgb(84,84,84);font-weight:normal;font-style:normal;text-decoration: none}
            span.cls_004{font-family:Times,serif;font-size:9.1px;color:rgb(84,84,84);font-weight:normal;font-style:normal;text-decoration: none}
            div.cls_004{font-family:Times,serif;font-size:9.1px;color:rgb(84,84,84);font-weight:normal;font-style:normal;text-decoration: none}
            span.cls_005{font-family:Arial,serif;font-size:9.1px;color:rgb(84,84,84);font-weight:normal;font-style:normal;text-decoration: none}
            div.cls_005{font-family:Arial,serif;font-size:9.1px;color:rgb(84,84,84);font-weight:normal;font-style:normal;text-decoration: none}
            span.cls_006{font-family:Times,serif;font-size:9.9px;color:rgb(84,84,84);font-weight:bold;font-style:normal;text-decoration: none}
            div.cls_006{font-family:Times,serif;font-size:9.9px;color:rgb(84,84,84);font-weight:bold;font-style:normal;text-decoration: none}
            
        </style>
    </head>
    <body>       
        <div style="position:absolute;left:50%;margin-left:-306px;top:0px;width:612px;height:792px;border-style:outset;overflow:hidden">
            
            <div style="position:absolute;left:20px;top:12px">
                <img src="http://logistiks.com/images/logo.jpg" width="100" height="100">
            </div>


            <div style="position:absolute;left:264.93px;top:119.58px" class="cls_002">
                <span class="cls_002">Invoice for {{ $serviceName }} </span>
            </div>
            
            <div style="position:absolute;left:74.75px;top:157.38px" class="cls_003">
                <span class="cls_003">Vendor Name &amp; Address</span>
            </div>
            <div style="position:absolute;left:357.94px;top:165.38px" class="cls_003">
                <span class="cls_003">Invoice Number</span>
            </div>
            <div style="position:absolute;left:79.25px;top:177.39px" class="cls_004">
                <span class="cls_004">{{$buyerName}}</span>
            </div>
            <div style="position:absolute;left:362.44px;top:185.54px" class="cls_005">
                <span class="cls_005">{{$orderNumber}}</span>
            </div>
            <div style="position:absolute;left:74.75px;top:227.46px" class="cls_003">
                <span class="cls_003">VAT / TIN Number</span>
            </div>
            <div style="position:absolute;left:250.42px;top:227.46px" class="cls_003">
                <span class="cls_003">SERVICE TAX REG Number</span>
            </div>
            <div style="position:absolute;left:417.83px;top:227.46px" class="cls_003">
                <span class="cls_003">GTA Number</span>
            </div>
            <div style="position:absolute;left:79.25px;top:247.62px" class="cls_005">
                <span class="cls_005">{{$vatNumber}}</span>
            </div>
            <div style="position:absolute;left:254.92px;top:247.62px" class="cls_005">
                <span class="cls_005">{{$serviceTaxRegNumber}}</span>
            </div>
            <div style="position:absolute;left:422.33px;top:247.62px" class="cls_005">
                <span class="cls_005">{{$gtaNumber}}</span>
            </div>
            <div style="position:absolute;left:74.75px;top:281.53px" class="cls_003">
                <span class="cls_003">Nature of Transaction :</span>
                <span class="cls_006"> Transport - {{ $serviceName }}</span>
            </div>
            <div style="position:absolute;left:74.75px;top:311.87px" class="cls_003">
                <span class="cls_003">Reference Order Number :</span>
                <span class="cls_006"> {{$orderNumber}}</span></div>
            <div style="position:absolute;left:74.75px;top:338.45px" class="cls_003">
                <span class="cls_003">Billing Address (As Specified by Buyer)</span>
            </div>
            <div style="position:absolute;left:324.75px;top:338.45px" class="cls_003">
                <span class="cls_003">Shipping Address</span>
            </div>
            <div style="position:absolute;left:79.25px;top:358.46px" class="cls_004">
                <span class="cls_004">  {{ $billingDetail->fullname }},</span>
            </div>
             <!-- Shipping Address start -->
            <div style="position:absolute;left:329.25px;top:358.46px" class="cls_004">
                <span class="cls_004">{{ $buyerName }}</span> 
                <span class="cls_004"> <br>{{ $buyerAddress1 }}, {{ $buyerAddress2 }} </span> <br>
            </div>
            <!-- Shipping Address end -->
            <div style="position:absolute;left:81.50px;top:369.17px" class="cls_004">
                <span class="cls_004"> 
                    <br>
                    {{ $billingDetail->contact_email }},{{ $billingDetail->mobile }}
                    <br>
                    {{ $billingDetail->address1 }} <br> {{ $billingDetail->city->location }}-{{ $billingDetail->pincode }} </span>
            </div>
             <!-- Shipping Address -->
            <div style="position:absolute;left:331.50px;top:369.17px" class="cls_004">
                <span class="cls_004"> <br> {{ $buyerCity }} - {{ $buyerPinCode }}</span>
            </div>
             <!-- Shipping Address end -->
            <div style="position:absolute;left:82.25px;top:427.66px" class="cls_006">
                <span class="cls_006">Service Description</span>
            </div>
            <div style="position:absolute;left:217.25px;top:427.66px" class="cls_006">
                <span class="cls_006">Details</span>
            </div>
            <div style="position:absolute;left:405.97px;top:427.66px" class="cls_006">
                <span class="cls_006">Amount</span>
            </div>
            <div style="position:absolute;left:217.25px;top:454.71px" class="cls_003">
                <span class="cls_003"> @if(!empty($fromLocation)) {{ $fromLocation }} To {{ $toLocation }} @endif </span>
            </div>
            <div style="position:absolute;left:82.25px;top:460.47px" class="cls_003">
                <span class="cls_003">Freight Amount</span>
            </div>
            <div style="position:absolute;left:405.97px;top:460.47px" class="cls_003">
                <span class="cls_003"> {{ $price }} /- </span>
            </div>
            <div style="position:absolute;left:217.25px;top:466.32px" class="cls_003">
                <span class="cls_003">Dispatch Date: {{ $pickUpDate }}</span>
            </div>
            <div style="position:absolute;left:217.25px;top:491.40px" class="cls_003">
                <span class="cls_003">GST  {{ $appliedGST }}%</span>
            </div>
            <div style="position:absolute;left:405.97px;top:491.40px" class="cls_003">
                <span class="cls_003"> {{ $totalTax }} /- </span>
            </div>
            <div style="position:absolute;left:217.25px;top:515.45px" class="cls_003">
                <span class="cls_003">Total Amount</span>
            </div>
            <div style="position:absolute;left:405.97px;top:521.21px" class="cls_003">
                <span class="cls_003"> {{ $totalAmountWithTax }} /-</span>
            </div>
            <!-- <div style="position:absolute;left:217.25px;top:526.81px" class="cls_003">
                <span class="cls_003">(Rupees Twenty two .nine Paise)</span>
            </div> -->
            <div style="position:absolute;left:513.76px;top:574.44px" class="cls_003">
                <span class="cls_003">Hemchand</span>
            </div>
            <div style="position:absolute;left:466.35px;top:598.13px" class="cls_006">
                <span class="cls_006">Authorised Signatory</span>
            </div>
            <div style="position:absolute;left:174.03px;top:664.90px" class="cls_003">
                <span class="cls_003">Service Availed through Market Place</span>
            </div>
        </div>
    </body>
</html>