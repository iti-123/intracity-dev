<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpFclTermSellerQuotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shp_fcl_term_seller_quotes', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('buyerPostId', false)->index();
            $table->integer('serviceId', false)->index();
            $table->integer('buyerId', false)->index();
            $table->integer('sellerId', false)->index();
            $table->boolean('isSellerAccepted')->nullable();
            $table->string('initialQuoteAt');
            $table->string('counterQuoteAt');
            $table->string('finalQuoteAt');
            $table->string('status')->nullable();
            $table->boolean('isBooked')->nullable();
            $table->string('awardType')->nullable();
            $table->string('loadPort')->nullable();
            $table->string('dischargePort')->nullable();
            $table->string('totalFreightCharges')->nullable();
            $table->longText('attributes');
            $table->integer('createdBy');
            $table->string('createdIp');
            $table->integer('updatedBy');
            $table->string('updatedIp');

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
        Schema::drop('shp_fcl_term_seller_quotes');
    }
}
