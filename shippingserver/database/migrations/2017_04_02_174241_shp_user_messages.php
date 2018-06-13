<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpUserMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shp_user_messages', function (Blueprint $table) {
            $table->increments('id', 100);
            $table->integer("lkp_service_id");
            $table->integer("sender_id");
            $table->integer("recepient_id")->nullable(false);
            $table->integer("post_id");
            $table->integer("post_item_id")->nullable();
            $table->integer("order_id")->nullable();
            $table->integer("contract_id")->nullable();
            $table->integer("quote_id")->nullable();
            $table->integer("quote_item_id")->nullable();
            $table->integer("enquiry_id")->nullable();
            $table->integer("lead_id")->nullable();
            $table->integer("lkp_message_type_id")->nullable(false)->default(0);
            $table->integer("message_no")->nullable(false);
            $table->longText("subject")->nullable(false);
            $table->longText("message")->nullable(false);
            $table->tinyInteger("is_read")->nullable(false)->default(0);
            $table->tinyInteger("is_draft")->nullable(false)->default(0);
            $table->tinyInteger("is_reminder")->nullable(false)->default(0);
            $table->tinyInteger("is_notified")->nullable(false)->default(0);
            $table->tinyInteger("is_general")->nullable(false)->default(0);
            $table->tinyInteger("is_term")->nullable(false)->default(0);
            $table->integer("parent_message_id")->nullable(false)->default(0);
            $table->integer("actual_parent_message_id")->nullable(false)->default(0);
            $table->index('lkp_message_type_id');
            $table->index('lkp_service_id');
            $table->index('sender_id');
            $table->index('recepient_id');
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
        Schema::dropIfExists('shp_user_messages');
    }
}
