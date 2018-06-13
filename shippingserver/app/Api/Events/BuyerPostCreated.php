<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/3/17
 * Time: 12:54 AM
 */

namespace App\Api\Events;

use Api\BusinessObjects\BuyerPostBO;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class BuyerPostCreated extends Event
{

    use SerializesModels;

    public $bo;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BuyerPostBO $bo)
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