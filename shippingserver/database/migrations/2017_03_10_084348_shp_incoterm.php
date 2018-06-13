<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpIncoterm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shp_incoterm', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('incoterm', 255);
            $table->string('detail', 255);
            $table->string('remark', 255);
            $table->timestamps();
            $table->index(['incoterm']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shp_incoterm');
    }
}
