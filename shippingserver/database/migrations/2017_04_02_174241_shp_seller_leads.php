<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ShpSellerLeads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('shp_seller_leads', function (Blueprint $table) {

            $table->integer("service_id")->index();
            $table->integer("seller_id")->index();

            $table->integer("buyer_id")->index();
            $table->string("buyer_name");
            $table->integer("post_id")->index();

            //Price Type : Negotiation = 1, Firm Price = 2
            $table->integer("price_type");

            $table->string("load_port");
            $table->string("discharge_port");
            $table->string("port_pair");

            $table->string("title");

            $table->integer("last_datetime_for_quote");
            $table->integer("cargo_ready_date");
            $table->integer("bid_type");
            $table->integer("allotments");

            $table->string("status");

            //1=>spot, 2=term
            $table->tinyInteger("spot_term");

            //1=>lead, 2=>enquiry
            $table->tinyInteger("lead_enquiry");

            //1=>related, 2=>partly related, 3=>unrelated
            $table->tinyInteger("port_match_type");

            //A grouping of various posts based on matching.
            $table->integer("category");

        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shp_seller_leads');
    }
}
