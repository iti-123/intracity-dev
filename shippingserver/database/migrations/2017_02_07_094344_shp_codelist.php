<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpCodelist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shp_codelist', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('entity', 255)->index();
            $table->string('field', 255);
            $table->string('value', 255);
            $table->string('description', 255);
            $table->string('child_entity', 255);
            $table->integer('created_by', false);
            $table->boolean('is_active', 60);
            $table->timestamps();
            $table->index(['entity', 'value', 'field']);
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shp_codelist');
    }
}
