<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpOrderDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shp_order_documents', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('order_id', 255)->index();
            $table->string('order_no', 60);
            $table->string('document_id', 255);
            $table->string('document_type', 255);
            $table->string('document_name', 255);
            $table->integer('created_by', false);
            $table->integer('updated_by', false)->nullable();
            $table->string('created_ip', 60);
            $table->string('updated_ip', 60);

            $table->timestamps();
            $table->index(['order_id', 'document_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shp_order_documents');
    }
}