<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class EmailNotification extends Model
{
    protected $table = 'lkp_email_templates';
    protected $fillable = ['subject', 'body', 'title'];

    public static function get_email_template($event_id)
    {
        $emailTemplate = EmailNotification::select('title', 'subject', 'body')->where("lkp_email_event_id", $event_id)
            ->where("is_active", 1)
            ->get()
            ->first();

        return $emailTemplate;
    }
}
