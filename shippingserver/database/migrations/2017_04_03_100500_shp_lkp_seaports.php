<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/*
  Author : Karunya
 */

class ShpLkpSeaports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('shp_lkp_seaports');

        Schema::create('shp_lkp_seaports', function (Blueprint $table) {
            $table->increments('id');
            $table->string("country_name");
            $table->string("seaport_name");
            $table->tinyInteger('is_active');
            $table->string('created_by', 255)->nullable();
            $table->string('created_ip', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->string('updated_ip', 255)->nullable();

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
        Schema::drop('shp_lkp_seaports');
    }
}