<?php

namespace ApiV2\Transformers;

use App\User as User;
use League\Fractal\TransformerAbstract;

class AuthTransformer extends TransformerAbstract
{
    public function transform(User $users)
    {
        $data_users = array(
            'id' => (int)$users->id,
            'username' => $users->username,
            'email' => $users->email,
        );
        return $data_users;
    }
}