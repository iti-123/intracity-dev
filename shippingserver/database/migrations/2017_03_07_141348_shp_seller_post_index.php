<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpSellerPostIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shp_seller_post_index', function (Blueprint $table) {
            $table->increments('id');
            $table->string("entity", 50);
            $table->integer("postId", false)->index();
            $table->string("serviceId")->index();
            $table->string("serviceName")->nullable();
            $table->string("sellerId")->index();
            $table->string("sellerName")->nullable();
            $table->string("title")->nullable();
            $table->string("loadPort")->nullable();
            $table->string("dischargePort")->nullable();
            $table->string("serviceSubType")->nullable();
            $table->string("containerType")->nullable();
            $table->integer("containerQuantity", false)->nullable();
            $table->string("tracking")->nullable();
            $table->string("carrierName")->nullable();
            $table->decimal("freightCharges")->nullable();
            $table->string("freightChargesCurrency")->nullable();
            $table->string("localChargesCurrency")->nullable();
            $table->decimal("localCharges")->nullable();
            $table->integer("transitDays")->nullable();
            $table->integer("discountBuyerId")->nullable();
            $table->string("discountBuyerName", 255)->nullable();
            $table->string("discountType", 255)->nullable();
            $table->decimal("discount")->nullable();
            $table->integer("creditDays")->nullable();
            $table->string("validFrom")->nullable();
            $table->string("validTo")->nullable();
            $table->boolean("isPublic");
            $table->string("status");
            $table->string("isDeleted");
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
        Schema::drop('shp_seller_post_index');
    }
}
