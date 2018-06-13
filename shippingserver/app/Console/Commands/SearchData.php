<?php

namespace App\Console\Commands;

use App\Http\Controllers\SyncToSearch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SearchData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'searchdata:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command fetches data and sync to search engine';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(SyncToSearch $syncToSearch)
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
            //$syncToSearch->syncToSolr($syncArray);
        endforeach;
    }
}
