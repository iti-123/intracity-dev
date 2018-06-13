<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

        'App\ApiV2\Events\SellerPostCreated' => [
            'App\ApiV2\Listeners\NotificationListener',
        ],

        'App\ApiV2\Events\BuyerPostCreated' => [
            'App\ApiV2\Listeners\NotificationListener',
        ],
        /*  'App\Api\Events\MessageCreated' => [
              'App\Api\Listeners\NotificationListener',
          ],
        */
        'App\ApiV2\Events\BuyerPostSpotCreated' => [
            'App\ApiV2\Listeners\RecomputeRecommendationsOnBuyerPostCreation',
            'App\ApiV2\Listeners\NotificationListener',
        ],

        'App\ApiV2\Events\BuyerPostCreatedEvent' => [
            'App\ApiV2\Listeners\PostNotificationListener',            
        ],
        'App\ApiV2\Events\SellerPostCreatedEvent' => [
            'App\ApiV2\Listeners\PostNotificationListener',            
        ],
        'App\ApiV2\Events\OrderEvent' => [
            'App\ApiV2\Listeners\OrderNotificationListener',            
        ],
        'App\ApiV2\Events\BookNow' => [
            'App\ApiV2\Listeners\NotificationListener',            
        ],

        
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
