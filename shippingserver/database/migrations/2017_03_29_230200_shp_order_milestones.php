<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpOrderMilestones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shp_order_milestones', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('order_id', 255);
            $table->string('milestone', 255);
            $table->integer('document_id', false);
            $table->string('comments', 500)->nullable();
            $table->text('additional_details');
            $table->integer('created_by', false);
            $table->integer('updated_by', false)->nullable();
            $table->string('created_ip', 60);
            $table->string('updated_ip', 60);
            $table->timestamps();
            $table->index(['order_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shp_order_milestones');
    }
}