<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpBuyerPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    // set new grammar class

    public function up()
    {
        //
        Schema::create('shp_buyer_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 250);
            $table->integer('buyerId', false)->index();
            $table->integer('serviceId', false)->index();
            $table->string('leadType');
            //$table->integer('serviceSubType',false)->nullable();
            $table->string('lastDateTimeOfQuoteSubmission');
            $table->integer('viewCount')->default(0);
            $table->boolean('isPublic');
            $table->integer('createdBy', false);
            $table->integer('updatedBy', false)->nullable();
            $table->string('createdIP', 60);
            $table->string('updatedIP', 60);
            $table->string('isTermAccepted', 60);
            //$table->integer('originLocation',false);
            $table->string('transactionId');
            $table->boolean('syncSearch');
            $table->boolean('syncLeads');
            $table->integer('version')->default(1);
            $table->longText('attributes')->nullable();
            $table->string('status', 15);
            $table->timestamps();


            // $table->foreign('lkp_service_id','buyer_quotes_ibfk_1')->references('id')->on('lkp_servicephp s')->onUpdate('NO ACTION')->onDelete('CASCADE');
            // $table->foreign('lkp_lead_type_id','buyer_quotes_ibfk_2')->references('id')->on('lkp_lead_types')->onUpdate('NO ACTION')->onDelete('CASCADE');
            // $table->foreign('lkp_quote_access_id','buyer_quotes_ibfk_3')->references('id')->on('lkp_quote_accesses')->onUpdate('NO ACTION')->onDelete('CASCADE');
            // $table->foreign('buyer_id','buyer_quotes_ibfk_4')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');

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
        Schema::dropIfExists('shp_buyer_posts');
    }
}

