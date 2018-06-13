<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpBuyerPostIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shp_buyer_post_index', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->string("entity", 50);
            $table->integer("postId", false)->index();
            $table->string("serviceId")->index();
            $table->string("serviceName");
            $table->string("buyerId")->index();
            $table->string("buyerName");
            $table->string("leadType", 50);
            $table->string("title")->nullable();
            $table->string("loadPort")->nullable();
            $table->string("dischargePort")->nullable();
            $table->string("serviceSubType")->nullable();
            $table->string("originLocation")->nullable();
            $table->string("destinationLocation")->nullable();
            $table->string("commodity")->nullable();
            //FCL&LCL Specific fields - Start
            $table->string("containerType")->nullable();
            $table->string("packagingType")->nullable();
            $table->integer("noOfPackages", false)->nullable();
            $table->integer("containerQuantity", false)->nullable();
            //FCL Specific fields - End
            $table->string("cargoReadyDate")->nullable();
            $table->string("priceType")->nullable();

            //TODO Following gross weight fields really required?
            $table->integer("grossWeight")->nullable();
            $table->string("weightUnit")->nullable();

            //Airfreight Specific fields - Start
            $table->string("airFreightType")->nullable();   //General, Express, Temperature Controlled
            $table->string("chargeableWeight")->nullable();   //chargeableWeight is calculated value from LBH, no.of packages and gross weight
            //Airfreight Specific fields - End

            //Roro Specific fields - Start
            $table->string("roroCondition")->nullable();   //Towable, Self Driven
            //Roro Specific fields - End

            $table->string("lastDateTimeForQuote")->nullable();
            $table->string("validFrom")->nullable();
            $table->string("validTo")->nullable();
            $table->boolean("isPublic")->nullable();
            $table->string("visibleToSellerId")->nullable();
            $table->string("visibleToSellerName")->nullable();
            $table->string("status");
            $table->string("isDeleted")->default(0);
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
        Schema::drop('shp_buyer_post_index');
    }
}
