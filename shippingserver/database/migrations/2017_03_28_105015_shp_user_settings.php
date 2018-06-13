<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpUserSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shp_user_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("user_id", false)->index();
            $table->integer("service_id", false)->index();
            $table->string('context', "255")->nullable();
            $table->longText('settings', "255")->nullable();
            $table->string('value', "255")->nullable();
            $table->string('created_by', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->string('created_ip', 255)->nullable();
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
        Schema::drop('shp_user_settings');
    }
}
