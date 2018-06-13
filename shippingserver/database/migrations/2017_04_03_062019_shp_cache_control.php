<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpCacheControl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shp_cache_control', function (Blueprint $table) {

            $table->integer("user_id")->index();
            $table->string("cache_key");
            $table->integer("cached_at");
            $table->integer("expiry_at");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shp_cache_control');
    }
}
