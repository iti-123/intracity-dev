<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/11/17
 * Time: 12:39 PM
 */

namespace App\Api\Events;


use ApiV2\BusinessObjects\ContractBO;
use App\Events\Event;

class ContractAccepted extends Event
{

    use SerializesModels;

    public $bo;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ContractBO $bo)
    {
        $this->bo = $bo;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

}