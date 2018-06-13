<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpSellerQuotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shp_seller_quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('buyerPostId', false)->index();
            $table->integer('serviceId', false)->index();
            $table->integer('buyerId', false)->index();
            $table->integer('sellerId', false)->index();
            $table->string('validTill')->nullable();
            $table->boolean('isSellerAccepted')->nullable();
            $table->boolean('isBuyerAccepted')->nullable();
            $table->boolean('isContractGenerated')->nullable();
            $table->string('initialQuoteAt');
            $table->string('counterQuoteAt');
            $table->string('finalQuoteAt');
            $table->string('status')->nullable();
            $table->boolean('isBooked')->nullable();
            $table->string('awardType')->nullable();
            $table->string('loadPort')->nullable();
            $table->string('dischargePort')->nullable();
            $table->integer('totalFreightCharges', false)->index();
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
        Schema::drop('shp_seller_quotes');
    }
}
