<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpMessageTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shp_lkp_message_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string("message_type", 100)->nullable(false)->default("");
            $table->tinyInteger("is_buyer")->default(1);
            $table->tinyInteger("is_seller")->default(1);
            $table->tinyInteger("is_active")->default(1);
            $table->string('created_by', 255)->nullable();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shp_lkp_message_types');
    }
}
