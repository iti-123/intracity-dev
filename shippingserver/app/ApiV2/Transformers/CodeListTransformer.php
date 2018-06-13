<?php

namespace ApiV2\Transformers;

use App\CodeList as CodeList;
use League\Fractal\TransformerAbstract;

class CodeListTransformer extends TransformerAbstract
{
    public function transform(CodeList $in)
    {
        $data_codelist = array(
            'entity' => $in->entity,
            'field' => $in->field,
            'value' => $in->value,
            'description' => $in->description,
            'child_entity' => $in->child_entity,
        );
        return $data_codelist;

    }
}
