<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 2/27/2017
 * Time: 5:34 PM
 */

namespace ApiV2\Modules\Common;


use ApiV2\BusinessObjects\PrivateMessagesBO;
use Log;

class MessageValidator
{
    function validateGet()
    {
        $response = 'Messagesalidator.validateGet() called';
        LOG::info($response);
        return ((array)$response);
    }

    function validateSave(PrivateMessagesBO $bo)
    {

        $errors = [];

        // $errors = parent::validateSave($bo);

        if (sizeof($errors) > 0) {
            return $errors;
        }


        LOG::info("Performing FCL Message Validations");

        //$attributes = $bo->attributes;
        //$errors = $this->validateFCLAttributes($attributes);

        LOG::info("Finished Performing FCL Message Validations. Found " . sizeof($errors) . " error(s)");
        LOG::info($errors);

        return $errors;
    }

    function validateDelete()
    {
        LOG::info('MessagesValidator.validateDelete Called');
        return 'MessagesValidator.validateDelete() called';
    }
}