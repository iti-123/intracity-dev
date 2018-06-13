<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 13/2/17
 * Time: 4:48 PM
 */

namespace Api\Services;

use App\Exceptions\ApplicationException;
use App\UploadFiles;
use DB;
use Exception;
use Log;
use Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class DocumentService
{
    const ENTITY_SELLER_POST = "sp";

    const ENTITY_CONTRACT = "c";

    const ENTITY_BUYER_POST = "bp";

    const ENTITY_SELLER_QUOTE = "sq";

    const ENTITY_ORDER = "o";

    const ENTITY_MESSAGE = "m";

    public static function upload($request)
    {
        try {

            $userId = JWTAuth::parseToken()->getPayload()->get('id');
            $file = $request->file('uploadFile');

            if (!$file) {
                throw new ApplicationException([], ["001" => "File not identified. Hint missing parameter 'uploadFile'"]);
            }

            $date = new \DateTime();

            $year = date('Y');
            $month = date('m');
            $day = date('d');

            $dirStorage = DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $month . DIRECTORY_SEPARATOR . $day;

            Storage::disk('local')->makeDirectory($dirStorage);

            $fileName = uniqid() . "-" . str_replace(" ", "-", $file->getClientOriginalName());

            $filepath = $dirStorage . DIRECTORY_SEPARATOR . $fileName;

            Storage::disk('local')->put(

                $filepath,
                file_get_contents($file->getRealPath())
            );

            LOG::info("File uploaded to path " . $dirStorage . DIRECTORY_SEPARATOR . $fileName);


            if ($request->type == 'bid_term_condition') {
                $uploadFiles = new \App\UploadIntraHyperFiles;
                $uploadFiles->buyerpost_terms_id = $request->buyerPostTermId;
            } else {
                $uploadFiles = new UploadFiles;
            }


            $now = date('Y-m-d H:i:s');
            $uploadFiles->file_name = $fileName;
            $uploadFiles->file_type = $file->getMimeType();
            $uploadFiles->file_size = $file->getSize();
            $uploadFiles->file_path = $filepath;
            $uploadFiles->created_at = $now;
            $uploadFiles->updated_at = $now;
            $uploadFiles->created_by = $userId;
            $uploadFiles->created_ip = $_SERVER['REMOTE_ADDR'];
            $uploadFiles->save();

            //LoggingServices::auditLog($uploadFiles->id, UPLOAD_FILE, $file->getClientOriginalName());

            return $uploadFiles;

        } catch (Exception $e) {

            LOG::error("File could not be stored", (array)$e);

            throw new ApplicationException([], [500 => "Failed to upload file"]);
        }
    }


    public static function link($docid, $entity, $entityId)
    {

        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $file = UploadFiles::where('id', $docid)->first();;

        if ($file != null && !empty($file)) {

            //File found

            //TODO What security checks need to be applied here ?

            $file->entity = $entity;
            $file->entity_id = $entityId;

            $file->save();

        } else {

            //File not found.
            throw new ApplicationException([], ["404" => "File not found"]);

        }

    }

    public static function getUrl($docid)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');

        $file = DB::table('shp_upload_files')
            ->where('id', '=', $docid)
            ->first();

        if ($file != null && !empty($file)) {

            //Document is found. Now check it's visibility to the logged in user

//            $entity = $file->entity;
//
//            $entityId = $file->entity_id;
//
//            if($entityId == null || $entityId < 0 || $entity == null || !isset($entity)) {
//                //This is an orphan reference. Reject the download.
//                throw new ApplicationException([], ["403" => "Not authorized to access this document"]);
//            }
//
//            $authorized = authorize($docid, $entity, $entityId);

            return Storage::disk("local")->getDriver()->getAdapter()->applyPathPrefix($file->file_path);

        } else {

            //File not found.
            throw new ApplicationException([], ["404" => "File not found"]);

        }

    }

    private function authorize($userid, $docid, $entity, $entityId)
    {

        $authorized = false;

        $authQuery = "";
        $authBindings = [];

        switch ($entity) {

            case self::ENTITY_SELLER_POST :

                $authQuery = "select 1 from shp_seller_posts where ";
                break;

            case self::ENTITY_BUYER_POST :

                $authQuery = "
                    select 1 from shp_buyer_posts where id = ? and (buyerId = ? or isPublic = true)
                    union
                    select 1 from shp_buyer_post_selected_sellers where post_id = ? and seller_id = ?";

                $authBindings = [$entityId, $userid, $entityId, $userid];

                break;

            case self::ENTITY_ORDER :

                break;

            case self::ENTITY_MESSAGE :

                break;

            default :

                LOG::alert("Unknown document entity found [" . $entity . "] for document [" . $docid . "]");

        }

        //Execute the authorization query with the bindings and check.
        $rows = DB::select($authQuery, $authBindings);

        if (count($rows) > 0) {
            //This user is authorized to see this document.
            $authorized = true;
        }

        return $authorized;

    }

}