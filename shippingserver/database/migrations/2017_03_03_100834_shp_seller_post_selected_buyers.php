<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpSellerPostSelectedBuyers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shp_seller_post_selected_buyers', function (Blueprint $table) {
            $table->integer('postId', false)->index();
            $table->string('loadPort')->nullable();
            $table->string('dischargePort')->nullable();
            $table->integer('buyerId', false)->index();
            $table->integer('discountType', false)->nullable();
            $table->integer('discount', false)->nullable();
            $table->integer('creditDays', false)->nullable();
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
        Schema::drop('shp_seller_post_selected_buyers');
    }
}
