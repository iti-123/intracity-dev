<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class BluecollarSellerRegQualifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bluecollar_seller_reg_qualifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bc_reg_id')->unsigned()->comment('bluecollar registration Id');
            $table->string('qualification', 255);
            $table->string('board', 255);
            $table->string('institution', 255);
            $table->string('state_or_city', 255);
            $table->string('percentage', 6);
            $table->enum('status', ['ACTIVE', 'DELETED'])->default('ACTIVE');
            $table->timestamps();
        });

        Schema::table('bluecollar_seller_reg_qualifications', function ($table) {
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
        Schema::drop('bluecollar_seller_reg_qualifications');
    }
}
