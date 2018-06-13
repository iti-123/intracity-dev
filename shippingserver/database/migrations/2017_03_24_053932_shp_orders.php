<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/*
  Author : Sreedutt
 */

class ShpOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('shp_order_items');

        Schema::create('shp_order', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->index();
            $table->string('order_no', 60)->nullable();
            $table->string('title', 255);
            $table->integer('buyer_id', false)->index();
            $table->integer('seller_id', false)->index();
            $table->string('post_type', 255);
            $table->integer('lkp_service_id', false)->index();
            $table->string('service_type', 255);
            $table->integer('buyer_post_id', false);
            $table->integer('seller_quote_id', false);
            $table->string('buyer_name', 255)->nullable();
            $table->string('seller_name', 255)->nullable();
            $table->string('lead_type', 255)->nullable();
            $table->string('load_port', 255)->nullable();
            $table->string('discharge_port', 255)->nullable();
            $table->string('cargo_ready_date', 255);
            $table->string('commodity_type', 255)->nullable();
            $table->string('valid_from', 255)->nullable();
            $table->string('valid_to', 255)->nullable();
            $table->tinyInteger('is_consignment_insured', false);
            $table->text('insurance_details')->nullable();
            $table->string('consignor_name', 255)->nullable();
            $table->string('consignor_email', 255)->nullable();
            $table->string('consignor_mobile', 255)->nullable();
            $table->string('consignor_address1', 255)->nullable();
            $table->string('consignor_address2', 255)->nullable();
            $table->string('consignor_address3', 255)->nullable();
            $table->string('consignor_pincode', 255)->nullable();
            $table->string('consignor_city', 255)->nullable();
            $table->string('consignor_state', 255)->nullable();
            $table->string('consignor_country', 255)->nullable();
            $table->string('consignee_name', 255)->nullable();
            $table->string('consignee_email', 255)->nullable();
            $table->string('consignee_mobile', 255)->nullable();
            $table->string('consignee_address1', 255)->nullable();
            $table->string('consignee_address2', 255)->nullable();
            $table->string('consignee_address3', 255)->nullable();
            $table->string('consignee_pincode', 255)->nullable();
            $table->string('consignee_city', 255)->nullable();
            $table->string('consignee_state', 255)->nullable();
            $table->string('consignee_country', 255)->nullable();
            $table->text('additional_details')->nullable();
            $table->tinyInteger('is_gsa_accepted', false);
            $table->longText('attributes')->nullable();
            $table->longtext('search_data')->nullable();
            $table->string('status', 255);
            $table->string('status_label', 255);
            $table->string('payment_status', 255);
            $table->decimal('freight_charges');
            $table->decimal('local_charges');
            $table->decimal('insurance_charges');
            $table->decimal('service_tax');
            $table->string('created_by', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->string('created_ip', 255)->nullable();
            $table->string('updated_ip', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('order_id')
            //       ->references('id')
            //       ->on('shp_order_batch');


//             $table->foreign('order_id')
//                   ->references('id')
//                   ->on('shp_order_batch');
// _
//             $table->foreign('buyer_id')
//                   ->references('id')
//                   ->on('users');

//             $table->foreign('seller_id')
//                   ->references('id')
//                   ->on('users');

//             $table->foreign('buyer_post_id')
//                   ->references('id')
//                   ->on('shp_buyer_posts');

//             $table->foreign('seller_quote_id')
//                   ->references('id')
//                   ->on('shp_seller_quotes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('shp_order');
    }
}
