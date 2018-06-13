<?php
namespace ApiV2\Controllers\CommonController;
use ApiV2\Controllers\BaseController;
use Exception;
use Illuminate\Http\Request;
use DB;
use ApiV2\Services\LogistiksCommonServices\DocumentImportServicesHyperSeller;
use ApiV2\Services\LogistiksCommonServices\DocumentImportServices;



use Log;

class DocumentImportController extends BaseController
{

    public function uploadBulkData(Request $request) {
        

        try {
            $document = new DocumentImportServices();

            return $document->bulkSaveOrUpdate($request);

        } catch(Exception $e) {
            Log::info($e);
        }
    }


    public function uploadBulkDataSellerRatecard(Request $request) {
        
        try {
            $document = new DocumentImportServicesHyperSeller();

            return $document->bulkSaveHyperlocalSeller($request);

        } catch(Exception $e) {
            Log::info($e);
        }
    }

}
