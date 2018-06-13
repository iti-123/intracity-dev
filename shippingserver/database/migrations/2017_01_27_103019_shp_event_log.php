<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpEventLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('shp_event_log', function (Blueprint $table) {
            $table->bigInteger('id', 20);
            $table->string('service_id')->nullable();
            $table->string('event', 100)->nullable();
            $table->string('comments')->nullable();
            $table->integer('created_by', false);
            $table->integer('updated_by', false)->nullable();
            $table->string('created_ip', 60);
            $table->string('updated_ip', 60)->nullable();
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
        Schema::dropIfExists('shp_event_log');
    }
}
