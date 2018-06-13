<?php

namespace App\Jobs;

use Api\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrdersEmailAlert extends Job implements ShouldQueue
{

    use InteractsWithQueue, SerializesModels;

    protected $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->params = $params;
        $this->onQueue('email');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        EmailService::sendMail($this->params);
    }
}