<?php

use Illuminate\Database\Seeder;

class UserSubscriptionServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_subscription_services')->where("user_id", "100006")->delete();
        DB::table('user_subscription_services')->where("user_id", "100007")->delete();
        DB::table('user_subscription_services')->where("user_id", "100008")->delete();
        DB::table('user_subscription_services')->where("user_id", "100009")->delete();
        DB::table('user_subscription_services')->where("user_id", "100010")->delete();

        DB::table('user_subscription_services')->insert([
            ['user_id' => 100006,
                'lkp_service_id' => 1,
                'subscription_service_fees_id' => 5,
                'service_payment_status' => 1,
                'verified_by' => 0,
                'verified_from_date' => '0000-00-00',
                'verified_to_date' => '0000-00-00',
                'activated_date' => '0000-00-00',
                'subscription_startdate' => '2017-03-01',
                'subscription_enddate' => '2018-03-01',
                'created_at' => '2017-03-01 07:43:54',
                'updated_at' => '0000-00-00 00:00:00',
                'transaction_id' => 0
            ],
            ['user_id' => 100006,
                'lkp_service_id' => 2,
                'subscription_service_fees_id' => 5,
                'service_payment_status' => 1,
                'verified_by' => 0,
                'verified_from_date' => '0000-00-00',
                'verified_to_date' => '0000-00-00',
                'activated_date' => '0000-00-00',
                'subscription_startdate' => '2017-03-01',
                'subscription_enddate' => '2018-03-01',
                'created_at' => '2017-03-01 07:43:54',
                'updated_at' => '0000-00-00 00:00:00',
                'transaction_id' => 0
            ],
            ['user_id' => 100007,
                'lkp_service_id' => 1,
                'subscription_service_fees_id' => 5,
                'service_payment_status' => 1,
                'verified_by' => 0,
                'verified_from_date' => '0000-00-00',
                'verified_to_date' => '0000-00-00',
                'activated_date' => '0000-00-00',
                'subscription_startdate' => '2017-03-01',
                'subscription_enddate' => '2018-03-01',
                'created_at' => '2017-03-01 07:43:54',
                'updated_at' => '0000-00-00 00:00:00',
                'transaction_id' => 0
            ],
            ['user_id' => 100007,
                'lkp_service_id' => 2,
                'subscription_service_fees_id' => 5,
                'service_payment_status' => 1,
                'verified_by' => 0,
                'verified_from_date' => '0000-00-00',
                'verified_to_date' => '0000-00-00',
                'activated_date' => '0000-00-00',
                'subscription_startdate' => '2017-03-01',
                'subscription_enddate' => '2018-03-01',
                'created_at' => '2017-03-01 07:43:54',
                'updated_at' => '0000-00-00 00:00:00',
                'transaction_id' => 0
            ],
            ['user_id' => 100008,
                'lkp_service_id' => 1,
                'subscription_service_fees_id' => 5,
                'service_payment_status' => 1,
                'verified_by' => 0,
                'verified_from_date' => '0000-00-00',
                'verified_to_date' => '0000-00-00',
                'activated_date' => '0000-00-00',
                'subscription_startdate' => '2017-03-01',
                'subscription_enddate' => '2018-03-01',
                'created_at' => '2017-03-01 07:43:54',
                'updated_at' => '0000-00-00 00:00:00',
                'transaction_id' => 0
            ],
            ['user_id' => 100008,
                'lkp_service_id' => 2,
                'subscription_service_fees_id' => 5,
                'service_payment_status' => 1,
                'verified_by' => 0,
                'verified_from_date' => '0000-00-00',
                'verified_to_date' => '0000-00-00',
                'activated_date' => '0000-00-00',
                'subscription_startdate' => '2017-03-01',
                'subscription_enddate' => '2018-03-01',
                'created_at' => '2017-03-01 07:43:54',
                'updated_at' => '0000-00-00 00:00:00',
                'transaction_id' => 0
            ],
            ['user_id' => 100009,
                'lkp_service_id' => 1,
                'subscription_service_fees_id' => 5,
                'service_payment_status' => 1,
                'verified_by' => 0,
                'verified_from_date' => '0000-00-00',
                'verified_to_date' => '0000-00-00',
                'activated_date' => '0000-00-00',
                'subscription_startdate' => '2017-03-01',
                'subscription_enddate' => '2018-03-01',
                'created_at' => '2017-03-01 07:43:54',
                'updated_at' => '0000-00-00 00:00:00',
                'transaction_id' => 0
            ],
            ['user_id' => 100009,
                'lkp_service_id' => 2,
                'subscription_service_fees_id' => 5,
                'service_payment_status' => 1,
                'verified_by' => 0,
                'verified_from_date' => '0000-00-00',
                'verified_to_date' => '0000-00-00',
                'activated_date' => '0000-00-00',
                'subscription_startdate' => '2017-03-01',
                'subscription_enddate' => '2018-03-01',
                'created_at' => '2017-03-01 07:43:54',
                'updated_at' => '0000-00-00 00:00:00',
                'transaction_id' => 0
            ],
            ['user_id' => 100010,
                'lkp_service_id' => 1,
                'subscription_service_fees_id' => 5,
                'service_payment_status' => 1,
                'verified_by' => 0,
                'verified_from_date' => '0000-00-00',
                'verified_to_date' => '0000-00-00',
                'activated_date' => '0000-00-00',
                'subscription_startdate' => '2017-03-01',
                'subscription_enddate' => '2018-03-01',
                'created_at' => '2017-03-01 07:43:54',
                'updated_at' => '0000-00-00 00:00:00',
                'transaction_id' => 0
            ],
            ['user_id' => 100010,
                'lkp_service_id' => 2,
                'subscription_service_fees_id' => 5,
                'service_payment_status' => 1,
                'verified_by' => 0,
                'verified_from_date' => '0000-00-00',
                'verified_to_date' => '0000-00-00',
                'activated_date' => '0000-00-00',
                'subscription_startdate' => '2017-03-01',
                'subscription_enddate' => '2018-03-01',
                'created_at' => '2017-03-01 07:43:54',
                'updated_at' => '0000-00-00 00:00:00',
                'transaction_id' => 0
            ]


        ]);
        echo "Success";
    }
}
