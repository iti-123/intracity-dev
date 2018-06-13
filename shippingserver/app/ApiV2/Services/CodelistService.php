<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/6/17
 * Time: 11:46 PM
 */

namespace ApiV2\Services;

use ApiV2\Requests\Attribute;
use ApiV2\Requests\CodeListBO;
use ApiV2\Requests\Value;
use App\CodeList;
use App\Exceptions\ServiceException;
use App\Exceptions\ValidationBuilder;
use Log;

class CodelistService extends BaseService implements ICodelistService
{

    public function getCodelists()
    {
        $codelists = CodeList::orderBy('entity')->orderBy('value')->orderBy('field')->get();

        return $this->formatCodelists($codelists);
    }

    private function formatCodelists($codelists)
    {

        try {

            $respCodelists = [];

            $prevEntity = null;
            $prevValue = null;

            $i = 0;

            $currEntity = null;
            $currValue = null;

            foreach ($codelists as $elem) {

                if ($i == 0) {

                    $prevEntity = $elem->entity;
                    $prevValue = $elem->value;

                    $currEntity = new CodeListBO();
                    $currEntity->values = array();
                    $currEntity->codeListType = $prevEntity;

                    $currValue = new Value();

                }

                if ($prevEntity != $elem->entity) {

                    array_push($currEntity->values, $currValue);
                    array_push($respCodelists, $currEntity);

                    $currEntity = new CodeListBO();
                    $currEntity->values = array();
                    $currEntity->codeListType = $elem->entity;

                    $currValue = new Value();
                    $currValue->attributes = array();

                    $prevValue = $elem->value;
                }

                if ($prevValue != $elem->value) {

                    array_push($currEntity->values, $currValue);

                    $currValue = new Value();
                    $currValue->attributes = array();

                }

                if ($elem->field == 'code') {

                    $currValue->key = $elem->value;
                    $currValue->value = $elem->description;
                    $currValue->childEntity = $elem->child_entity;

                } else {

                    $currValue->attributes[$elem->field] = $elem->description;

                }


                $prevEntity = $elem->entity;
                $prevValue = $elem->value;

                $i = $i + 1;

            }

            array_push($currEntity->values, $currValue);
            array_push($respCodelists, $currEntity);

            return $respCodelists;

        } catch (\Exception $e) {

            LOG::error($e);

            throw new ServiceException("failed to fetch codelists");

        }

    }

    public function getCodelistByName($entity)
    {
        LOG::info("Fetching code lists by name " . $entity);

        $codelists = CodeList::where('entity', $entity)->orderBy('entity')->orderBy('value')->orderBy('field')->get();

        if ($codelists == null || count($codelists) <= 0) {
            ValidationBuilder::create()->error("codelist", "Codelist " . $entity . " is invalid")->raise();
        }

        return $this->formatCodelists($codelists);

    }

    public function getCodelistByNames(array $entity = [])
    {

        $codelists = CodeList::whereIn('entity', $entity)->orderBy('entity')->orderBy('value')->orderBy('field')->get();

        if ($codelists == null || count($codelists) <= 0) {
            ValidationBuilder::create()->error("codelist", "Codelist(s) " . implode(',', $entity) . " are not valid")->raise();
        }

        return $this->formatCodelists($codelists);

    }

}