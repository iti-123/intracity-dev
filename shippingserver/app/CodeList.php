<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodeList extends Model
{
    public $timestamps = false;
    //protected $table = 'codelist';
    //protected $fillable = ['entity', 'field', 'value', 'description', 'child_entity', 'created_by', 'is_active' ];
    protected $table = 'shp_codelist';


}
