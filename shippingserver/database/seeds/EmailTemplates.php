<?php

use Api\Model\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplates extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {

        if (!count(EmailTemplate::find(54))) {
            EmailTemplate::create([
                'id' => '54',
                'lkp_email_event_id' => '54',
                'title' => 'Order Status Changed',
                'subject' => 'Order Update {!! tostatus !!}',
                'body' => "
Hello {!! username !!},

{!! status_update !!} by {!! buyername !!}  for  {!! order_no !!}

With Regards,
Logistiks Team

-------------------------------------------------------------------  
This is an auto-generated email. Please do not reply to this email. 
You can email to support@logistiks.com for any enquiry
            ",
                'is_active' => 1,
                'created_by' => 1,
                'created_ip' => 1,
                'updated_by' => 1,
                'updated_ip' => ''
            ]);
        }

        if (!count(EmailTemplate::find(55))) {
            EmailTemplate::create([
                'id' => '55',
                'lkp_email_event_id' => '55',
                'title' => 'Order Status Changed: needed response',
                'subject' => 'Order Update {!! tostatus !!}',
                'body' => "
Hello {!! username !!},

{!! sellername !!} has submitted the CRO document for  {!! order_no !!} . 
Kindly login into the portal to proceed further.

With Regards,
Logistiks Team

-------------------------------------------------------------------
This is an auto-generated email. Please do not reply to this email. 
You can email to support@logistiks.com for any enquiry
            ",
                'is_active' => 1,
                'created_by' => 1,
                'created_ip' => 1,
                'updated_by' => 1,
                'updated_ip' => ''
            ]);
        }


        echo "Successfully Added Email Templates \n";

    }
}
