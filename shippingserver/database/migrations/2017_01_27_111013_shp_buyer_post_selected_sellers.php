<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpBuyerPostSelectedSellers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('shp_buyer_post_selected_sellers', function (Blueprint $table) {
            $table->integer('post_id', false)->index();;
            $table->integer('seller_id', false);
            //$table->foreign('fk_buyer_quote_id')->references('id')->on('shp_buyer_posts');
            //$table->foreign('fk_seller_id')->references('id')->on('shp_seller_posts');
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
        Schema::dropIfExists('shp_buyer_post_selected_sellers');
    }
}
