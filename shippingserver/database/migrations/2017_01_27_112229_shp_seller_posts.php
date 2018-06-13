<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpSellerPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('shp_seller_posts')) {
            Schema::create('shp_seller_posts', function (Blueprint $table) {
                $table->integer('id', 11)->index();
                $table->integer('service_id', false)->index();
                $table->string('service_subcategory', 15)->nullable();
                $table->integer('seller_id', false);
                $table->string('title', 300);
                $table->string('valid_from');
                $table->string('valid_to');
                $table->string('status', 15)->default("draft");;
                //$table->string('tracking',15)->nullable();//NO Need
                $table->integer('access_id', false)->nullable();//No Need
                $table->text('terms_conditions')->default("");;
                $table->integer('terms_accepted', false)->default(1);;
                $table->integer('view_count', false)->default(0);
                $table->tinyInteger('sys_solr_sync')->default(0);
                $table->tinyInteger('sys_refresh_leads')->default(0);
                $table->longText('attributes')->nullable();
                $table->integer('created_by', false)->nullable();
                $table->integer('updated_by', false)->nullable();
                $table->string('created_ip', 60);
                $table->string('updated_ip', 60)->nullable();
                $table->string('version', 60)->nullable();
                $table->boolean('isPublic');
                $table->string('transactionId');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('shp_seller_posts');
    }
}
