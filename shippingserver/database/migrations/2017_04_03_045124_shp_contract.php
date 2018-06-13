<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shp_contract', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 250);
            $table->integer('buyerPostId', false)->index();
            $table->integer('serviceId', false)->index();
            $table->integer('buyerId', false)->index();
            $table->integer('sellerId', false)->index();
            $table->string('validFrom', 20);
            $table->string('validTo', 20);
            $table->integer('createdBy', false);
            $table->integer('updatedBy', false);
            $table->string('createdIp', 250);
            $table->string('updatedIp', 250);
            $table->string('isSellerAccepted', 250);
            $table->string('status', 250);
            $table->string('awardType', 10);
            //$table->longText('attributes')->nullable();
            $table->timestamps();
        });


        Schema::create('shp_contract_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contractId', false);
            $table->string('commodity', 250);
            $table->string('loadPort', 250);
            $table->string('dischargePort', 250);
            $table->string('containerType', 250);
            $table->integer('quantity', false);
            $table->longText('attributes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('shp_contract');
        Schema::drop('shp_contract_items');
    }
}
