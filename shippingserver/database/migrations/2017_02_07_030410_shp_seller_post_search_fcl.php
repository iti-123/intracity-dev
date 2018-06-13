<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpSellerPostSearchFcl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shp_sellerpost_search_fcl', function (Blueprint $table) {
            $table->bigIncrements('id');
            //'shp_entity' => 'seller_post',
            $table->bigInteger('shp_service_id');
            $table->bigInteger('shp_post_id');
            $table->bigInteger('shp_seller_id');
            $table->boolean('shp_is_partner');
            $table->smallInteger('shp_rating');
            $table->string('shp_tracking');
            $table->string('shp_offers');
            $table->smallInteger('fcl_load_port');
            $table->smallInteger('fcl_discharge_port');
            $table->string('fcl_container_type');
            $table->date('fcl_shipment_ready_date');
            $table->string('fcl_product_type');
            $table->smallInteger('fcl_transit_days');
            $table->float('fcl_freight_charges');
            $table->float('fcl_freight_charges_discounted');
            $table->float('fcl_local_charges');
            $table->float('fcl_local_charges_discounted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shp_sellerpost_search_fcl');
    }
}
