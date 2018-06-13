s<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpFclSearchBuyerPost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shp_fcl_search_buyerpost', function (Blueprint $table) {
            $table->bigIncrements('id', 16);
            $table->string('title');
            $table->integer('lkp_post_id', false);
            $table->integer('lkp_buyer_id', false);
            $table->integer('lkp_service_id', false);
            $table->string('serviceName');
            $table->integer('leadType', false);
            $table->string('leadTypeName');
            $table->string('serviceSubType');
            $table->string('originLocation');
            $table->string('destinationLocation');
            $table->string('lastDateTimeOfQuoteSubmission');
            $table->string('viewCount');
            $table->boolean('isPublic');
            $table->string('loadPort');
            $table->string('dischargePort');
            $table->string('commodity');
            $table->string('commodityDescription');
            $table->string('packagingType');
            $table->string('cargoReadyDate');
            $table->boolean('isFumigationRequired');
            $table->boolean('isFactoryStuffingRequired');
            $table->string('containerType');
            $table->integer('quantity');
            $table->string('weightUnit');
            $table->double('grossWeight');
            $table->string('priceType');
            $table->double('actualPrice');
            $table->double('counterOffer');
            $table->string('currency');
            $table->string('transitDays');
            $table->string('visibleToSellersIds');
            $table->string('visibleToSellersNames');
            $table->timestamps();
            // $table->index(['containerType', 'commodity', 'cargoReadyDate'], shp_fcl_search_buyerpost_index);

        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shp_fcl_search_buyerpost');
    }
}
