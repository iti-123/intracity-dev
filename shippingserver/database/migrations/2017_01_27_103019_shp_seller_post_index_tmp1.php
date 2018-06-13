<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpSellerPostIndexTmp1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('shp_seller_post_index_tmp1', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sellerId')->nullable();
            $table->string('lpdp')->nullable();
            $table->string('loadPort')->nullable();
            $table->string('dischargePort')->nullable();
            $table->string('serviceId');
            $table->string('serviceName')->nullable();
            $table->string('containerType')->nullable();
            $table->tinyInteger('isPublic', false, false);
            $table->string('validFrom')->nullable();
            $table->string('validTo')->nullable();
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
        Schema::dropIfExists('shp_seller_post_index_tmp1');
    }
}
