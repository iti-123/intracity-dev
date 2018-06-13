<?php

namespace ApiV2\Services\GenerateFiles;
use ApiV2\Services\LogistiksCommonServices\DocumentServices;
use App;

class GeneratePDF
{

    public function pdf()
    {

        view()->addNamespace('my_views', app_path('Api'));
        $data = [
            "buyerName" => "Sreenath",
            "orderNumber" => "FTL/2017/000030",
            "vatNumber" => "000030",
            "serviceTaxRegNumber" => "000030",
            "gtaNumber" => "000030"
        ];
        $pdfHtml = view('my_views::Services.GenerateFiles.Templates.buyer_invoice', $data);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($pdfHtml);
        return $pdf->download('FCL_INVOICE.pdf');

    }

    public static function generateInvoicePdf($request)
    {
        view()->addNamespace('my_views', app_path('ApiV2'));
        
        $appliedGST = 18;

        $totalTax = ($request['price'] * $appliedGST)/100;

        $totalAmountWithTax = $request['price'] + $totalTax;

        $data = [
            "buyerName" => $request['consignee_name'],
            "orderNumber" => $request['order_no'],
            "vatNumber" => "Not Applicable",
            "serviceTaxRegNumber" => "Not Applicable",
            "gtaNumber" => "Not Applicable",
            "serviceName"=>$request['service'],
            "title"=>'Invoice',
            "orderItemId"=>$request['itemId'],
            "buyerName"=>$request['consignee_name'],
            "buyerAddress1"=>$request['consignee_address1'],
            "buyerAddress2"=>$request['consignee_address2'],
            "buyerEmail"=>$request['consignee_email'],
            "buyerMobile"=>$request['consignee_mobile'],
            "buyerPinCode"=>$request['consignee_pincode'],
            "buyerCity"=>$request['consignee_city'], 
            "billingDetail"=>$request['billingDetail'],
            "price"=>$request['price'],
            "fromLocation"=>$request['from_location'],
            "toLocation"=>$request['to_location'],
            "appliedGST"=>$appliedGST,
            "totalTax"=>$totalTax,
            "totalAmountWithTax"=>$totalAmountWithTax,
            "pickUpDate"=>$request['consignmentPickupDetails']['consignmentPickupDate'],                       
        ];

        

        $pdfHtml = view('my_views::Services.GenerateFiles.Templates.intrahyper_buyer_invoice', $data);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($pdfHtml);
        $fileName = rand().$request['consignee_name'].'.pdf';
        $filePath = strtolower($data['serviceName']).'/docs/order/'.$fileName;
        $pdf->save(storage_path().'/app/'.$filePath)->stream();        
               
        return DocumentServices::storeOrderDocumentToTable($data, $filePath);
    }

}