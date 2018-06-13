<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Log;
use Queue;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Populate failed jobs table
        Queue::failing(function ($connection, $job, $data) {
            Log::info("Failed Executing Job " . $job . "  => ");
            Log::info($data);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //Register Services
        $this->app->bind('Api\\Services\\ISellerPostService', 'Api\\Services\\SellerPostService');
        $this->app->bind('Api\\Services\\IBuyerPostService', 'Api\\Services\\BuyerPostService');

        $this->app->bind('Api\\Services\\FileStorage\\FileStorageInterface', 'Api\\Services\\FileStorage\\S3FileStorage');
    }
}
