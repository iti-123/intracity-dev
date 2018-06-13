<?php

namespace Api\Services\GenerateFiles;

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

}