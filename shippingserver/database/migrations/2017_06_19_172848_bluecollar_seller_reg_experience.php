<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class BluecollarSellerRegExperience extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bluecollar_seller_reg_experience', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bc_reg_id')->unsigned()->comment('bluecollar registration Id');
            $table->string('vehicle_type', 100);
            $table->integer('experience');
            $table->string('employer_name', 255);
            $table->string('location', 100);
            $table->string('salary', 10);
            $table->enum('status', ['ACTIVE', 'DELETED'])->default('ACTIVE');
            $table->timestamps();
        });

        Schema::table('bluecollar_seller_reg_experience', function ($table) {
            $table->foreign('bc_reg_id')->references('id')->on('bluecollar_seller_registration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bluecollar_seller_reg_experience');
    }
}
