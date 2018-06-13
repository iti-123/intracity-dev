<?php

namespace App\Jobs;

use App\Http\Controllers\SolrSyncJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SyncToSolr extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SolrSyncJob $solrSyncJob)
    {
        $resServices = DB::table('shp_seller_posts')->select('id,lkp_service_id,tracking,seller_id,post_title,attributes')->where('is_search_in_sync', '0')->get();
        $commonArray = [];
        foreach ($resServices as $resService):
            $attributes = json_decode($resService->attributes);
            /*
             * Capturing common data in commonArray
             * */
            $commonArray = [
                //'shp_entity' => 'seller_post',
                'shp_service_id' => $resService->lkp_service_id,
                //'shp_service_name' => $resService->post_title,
                'shp_post_id' => $resService->id,
                'shp_seller_id' => $resService->seller_id,
                //'shp_seller_name' => '',
                'shp_is_partner' => '',
                'shp_rating' => '',
                'shp_tracking' => $resService->tracking,
                'shp_offers' => '',
            ];
            $routesArray = [];
            foreach ($attributes['routes'] as $route):
                //Todo: Need to capture carrier data
                /*
                 * Capturing route specific information
                 * */
                $routesArray = [
                    'fcl_load_port' => $route['loadPort'],
                    'fcl_discharge_port' => $route['dischargePort'],
                ];
                /*
                 * Capturing container data
                 * */
                $containerArray = [];
                foreach ($route['container'] as $container):
                    $containerArray = [
                        'fcl_container_type' => $container['containerType'],
                        'fcl_shipment_ready_date' => '', //Todo: Need to get this info
                        'fcl_product_type' => '',
                        'fcl_transit_days' => '',
                        'fcl_freight_charges' => '',
                        'fcl_freight_charges_discounted' => '',
                        'fcl_local_charges' => '',
                        'fcl_local_charges_discounted' => ''
                    ];
                    //Log::info(var_dump($resService->attributes));
                endforeach;
            endforeach;
            $syncArray = array_merge($commonArray, $routesArray, $containerArray);
            $solrSyncJob->syncToSolr($syncArray);
        endforeach;
    }
}