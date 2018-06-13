<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/3/17
 * Time: 12:54 AM
 */

namespace App\Api\Events;


use Api\BusinessObjects\MessageBO;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class MessageCreated extends Event
{

    use SerializesModels;

    public $bo;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(MessageBO $bo)
    {
        $this->bo = $bo;
        //dd($bo);
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