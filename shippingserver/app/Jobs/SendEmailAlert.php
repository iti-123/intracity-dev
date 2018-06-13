<?php

namespace App\Jobs;

use Api\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class SendEmailAlert extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $sendTo = [];
    protected $event;
    protected $emailInfo = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sendTo, $emailInfo, $event)
    {

        $this->sendTo = $sendTo;
        $this->emailInfo = $emailInfo;
        $this->event = $event;


        // Name of the Queue
        $this->onQueue('email');

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        LOG::info("Alerting recipients of a private buyer post");
        EmailService::sendMailTo($this->sendTo, $this->emailInfo, $this->event);


    }
}
