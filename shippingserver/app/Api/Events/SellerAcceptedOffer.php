<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/11/17
 * Time: 12:48 PM
 */

namespace App\Api\Events;


use Api\BusinessObjects\SellerQuoteBO;

class SellerAcceptedOffer extends Event
{

    use SerializesModels;

    public $bo;

    public $origin;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SellerQuoteBO $bo, $origin = "")
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