<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpCartItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('shp_cart_items', function (Blueprint $table) {

            $table->increments('id')->index();
            $table->string('title', 255);
            $table->integer('buyer_id', false)->index();
            $table->integer('seller_id', false);
            $table->string('post_type', 255);
            $table->integer('lkp_service_id', false);
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
            $table->decimal('freight_charges');
            $table->decimal('local_charges');
            $table->decimal('insurance_charges');
            $table->decimal('service_tax');
            $table->string('created_by', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->string('created_ip', 255)->nullable();
            $table->string('updated_ip', 255)->nullable();
            $table->timestamps();
            $table->index(['buyer_id', 'seller_id', 'seller_quote_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shp_cart_items');
    }
}
