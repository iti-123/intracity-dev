<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpAuditLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // create table shp_audit_log
        Schema::create('shp_audit_log', function (Blueprint $table) {
            $table->bigInteger('id', 20);
            $table->integer('entity_id', false);
            $table->string('entity', 255);
            $table->longText('post_data');
            $table->integer('created_by', false);
            $table->string('created_ip', 60);
            //$table->primary(['id']);
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
        // drop table shp_audit_log
        Schema::dropIfExists('shp_audit_log');
    }
}
