<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class BluecollarSellerRegistration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bluecollar_seller_registration', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('created_by');
            $table->enum('profile_type', ['DRIVER', 'BIKER', 'CLEANER', 'SKILLED_OPERATOR', 'UNSKILLED_OPERATOR', 'MHE_OPERATOR']);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('date_of_birth');
            $table->enum('bloodgroup', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);
            $table->string('email', 255);
            $table->string('pan_no', 10);
            $table->string('pan_document', 255);
            $table->string('aadhar_no', 12);
            $table->string('aadhar_document', 255);
            $table->string('ration_card_no', 12);
            $table->string('ration_card_document', 255);
            $table->enum('same_addresses', ['YES', 'NO'])->default('NO')->comment('same current and permanent addresses');
            $table->string('cur_house_no', 100);
            $table->string('cur_street', 100);
            $table->string('cur_locality', 100);
            $table->integer('cur_state_id');
            $table->integer('cur_city_id');
            $table->integer('cur_district_id');
            $table->string('cur_pincode', 6);
            $table->string('cur_landline', 12)->nullable();
            $table->string('cur_mobile', 10);
            $table->string('cur_alt_mobile', 10)->nullable();
            $table->string('per_house_no', 100)->nullable();
            $table->string('per_street', 100)->nullable();
            $table->string('per_locality', 100)->nullable();
            $table->integer('per_state_id')->nullable();
            $table->integer('per_city_id')->nullable();
            $table->integer('per_district_id')->nullable();
            $table->string('per_pincode', 6)->nullable();
            $table->string('per_landline', 12)->nullable();
            $table->string('per_mobile', 10)->nullable();
            $table->string('per_alt_mobile', 10)->nullable();
            $table->set('vehicle_type', ['BIKE', 'LMV', 'MMV', 'HMV']);
            $table->string('licence_no', 10)->nullable();
            $table->string('licence_doc', 255)->nullable();
            $table->string('licence_state', 100)->nullable();
            $table->date('licence_valid_from')->nullable();
            $table->date('licence_valid_to')->nullable();
            $table->text('licence_transport_endorsement')->nullable();
            $table->enum('lic_policy', ['YES', 'NO'])->default('NO');
            $table->enum('health_policy', ['YES', 'NO'])->default('NO');
            $table->enum('medical_policy', ['YES', 'NO'])->default('NO');
            $table->text('languages');
            $table->set('employmentType', ['FULL_TIME', 'PART_TIME', 'CONTRACT']);
            $table->integer('current_salary');
            $table->integer('total_experience');
            $table->enum('verified', ['YES', 'NO'])->default('NO');
            $table->integer('verified_by')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('bluecollar_seller_registration', function ($table) {
            $table->foreign('created_by')->references('id')->on('users');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bluecollar_seller_registration');
    }
}
