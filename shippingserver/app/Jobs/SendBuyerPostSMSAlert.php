<?php

namespace App\Jobs;

use Api\Services\SendSmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class SendBuyerPostSMSAlert extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $visibleToSellers;
    protected $BUYER_CREATED_POST_FOR_SELLERS_SMS;
    protected $msg_params;
    protected $mobileNumbers = [];
    protected $userID;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mobileNumbers, $BUYER_CREATED_POST_FOR_SELLERS_SMS, $msg_params, $userID)
    {


        $this->mobileNumbers = $mobileNumbers;
        $this->BUYER_CREATED_POST_FOR_SELLERS_SMS = $BUYER_CREATED_POST_FOR_SELLERS_SMS;
        $this->msg_params = $msg_params;
        $this->userID = $userID;

        // Name of the Queue
        $this->onQueue('sms');

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        LOG::info("Alerting recipients of a private buyer post through SMS");

        SendSmsService::shpSendSMS($this->mobileNumbers, $this->BUYER_CREATED_POST_FOR_SELLERS_SMS, $this->msg_params, $this->userID);
    }
}
