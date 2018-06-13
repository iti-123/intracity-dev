<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpHdfcTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::create('shp_hfdc_payment_transactions', function (Blueprint $table) {

            $table->increments('id');

            $table->string('response_message', 255);
            $table->dateTime('date_created');
            $table->integer('payment_id', false);
            $table->integer('order_id', false);
            $table->decimal('amount', 5, 2);
            $table->string('mode', 255);
            $table->string('billing_name', 255);
            $table->string('billing_address', 255);
            $table->string('billing_city', 255);
            $table->string('billing_state', 255);
            $table->integer('billing_postal_code', false);
            $table->string('billing_country', 255);
            $table->string('billing_phone', 255);
            $table->string('billing_email', 255);
            $table->string('delivery_name', 255)->nullable();
            $table->string('delivery_address', 255)->nullable();
            $table->string('delivery_city', 255)->nullable();
            $table->string('delivery_state', 255)->nullable();
            $table->integer('delivery_postal_code', false)->nullable();
            $table->string('delivery_country', 255)->nullable();
            $table->string('delivery_phone', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('is_flagged', 255);
            $table->string('transaction_id', 255);
            $table->string('payment_method', 255);

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
        //
        Schema::dropIfExists('shp_hfdc_payment_transactions');
    }
}
