<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpOrderbatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('shp_orders');

        Schema::create('shp_order_batch', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('buyer_id', false)->index();
            $table->string('payment_type', 255);
            $table->string('amount_to_pay', "255");
            $table->string('amount_received', "255");
            $table->enum('payment_status', ['INPROGRESS', 'FAILED', 'SUCCESS']);
            $table->string('reference_no', 255);
            $table->string('created_by', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->string('created_ip', 255)->nullable();
            $table->string('updated_ip', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            /* $table->foreign('buyer_id')
                   ->references('id')
                   ->on('users');*/

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('shp_order_batch');
    }
}
