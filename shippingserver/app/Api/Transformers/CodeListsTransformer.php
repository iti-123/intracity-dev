<?php

namespace Api\Transformers;

use App\CodeLists as CodeLists;
use League\Fractal\TransformerAbstract;

class CodeListsTransformer extends TransformerAbstract
{
    public function transform(CodeLists $CodeLists)
    {

        $data_codelists = array(
            'entity' => $CodeLists->entity,
            'field' => $CodeLists->field,
            'value' => $CodeLists->value,
            'description' => $CodeLists->description,
            'child_entity' => $CodeLists->child_entity,
        );
        return $data_codelists;
    }
}