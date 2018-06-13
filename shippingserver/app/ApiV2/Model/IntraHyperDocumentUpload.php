<?php

namespace ApiV2\Model;

use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;
use DB;
use Illuminate\Database\Eloquent\Model;



class IntraHyperDocumentUpload extends Model
{
    protected $table = 'intra_hp_file_uploads';
}