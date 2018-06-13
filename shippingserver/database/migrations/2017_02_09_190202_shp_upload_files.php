<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpUploadFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('shp_upload_files', function (Blueprint $table) {
            $table->bigInteger('id', 20);
            $table->string('entity', 10);
            $table->integer('entity_id', false)->index();   //check this with the team
            $table->string('file_name', 255)->nullable();
            $table->string('file_type', 255)->nullable();
            $table->string('file_size', 255)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->integer('created_by', false);
            $table->string('created_ip', 60);
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
        Schema::dropIfExists('shp_upload_files');

    }
}
