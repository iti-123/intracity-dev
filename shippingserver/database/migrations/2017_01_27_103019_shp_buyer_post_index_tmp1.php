<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpBuyerPostIndexTmp1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('shp_buyer_post_index_tmp1', function (Blueprint $table) {
            $table->increments('id');
            $table->string('buyerId')->nullable();
            $table->integer('postId', false);
            $table->string('leadType')->nullable();
            $table->string('lpdp')->nullable();
            $table->string('loadPort')->nullable();
            $table->string('dischargePort')->nullable();
            $table->string('serviceId');
            $table->string('serviceName')->nullable();
            $table->string('containerType')->nullable();
            $table->tinyInteger('isPublic', false);
            $table->string('visibleToSellerId')->nullable();
            $table->string('visibleToSellerName')->nullable();
            $table->string('validFrom')->nullable();
            $table->string('validTo')->nullable();
            $table->tinyInteger('lp_match', false, false);
            $table->tinyInteger('lpdp_match', false, false);
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
        //
        Schema::dropIfExists('shp_buyer_post_index_tmp1');
    }
}
