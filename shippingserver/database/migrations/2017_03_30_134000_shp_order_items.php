<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/*
  Author : Sreedutt
 */

class ShpOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('shp_order_items');

        Schema::create('shp_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->index();
            $table->integer("service_id")->index();
            $table->string("service_name");
            $table->integer("buyer_id")->index();
            $table->string("buyer_name");
            $table->integer('seller_id', false)->index();
            $table->string("seller_name");
            $table->string('lead_type', 255);  //Spot, term
            $table->string('booking_type', 255);
            $table->string('load_port', 255);
            $table->string('discharge_port', 255);
            $table->string("commodity");
            $table->string("container_type");
            $table->string('consignor_name', 255);
            $table->string('consignee_name', 255);
            $table->string('created_by', 255);
            $table->string('updated_by', 255)->nullable();
            $table->string('created_ip', 255)->nullable();
            $table->string('updated_ip', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('shp_order_items');
    }
}
