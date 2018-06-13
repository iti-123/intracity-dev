<?php

namespace ApiV2\Traits;

use Illuminate\Support\Facades\Crypt;

trait Encryptable
{
    public function getEncIdAttribute()
    {
        $value = $this->id;
        return Crypt::encrypt($value);
    }
}
