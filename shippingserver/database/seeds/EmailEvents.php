<?php

use Api\Model\EmailEvent;
use Illuminate\Database\Seeder;

class EmailEvents extends Seeder
{

    public function run()
    {

        if (!count(EmailEvent::find(54))) {
            EmailEvent::create([
                'id' => '54',
                'title' => 'Order Status Changed',
                'description' => 'Order Status Changed from one status to another status',
                'is_active' => 1,
                'created_by' => 1,
                'created_ip' => 1,
                'updated_by' => 1,
                'updated_ip' => ''
            ]);
        }
        if (!count(EmailEvent::find(55))) {
            EmailEvent::create([
                'id' => '55',
                'title' => 'Order Status Changed: needed response',
                'description' => 'Order Status Changed from one status to another status and seller/buyer needs response',
                'is_active' => 1,
                'created_by' => 1,
                'created_ip' => 1,
                'updated_by' => 1,
                'updated_ip' => ''
            ]);
        }

        echo "Successfully Added Email Events \n";

    }
}