<?php

use Illuminate\Database\Seeder;

class SellerServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('seller_services')->where("user_id", "100006")->delete();
        DB::table('seller_services')->where("user_id", "100007")->delete();
        DB::table('seller_services')->where("user_id", "100008")->delete();
        DB::table('seller_services')->where("user_id", "100009")->delete();
        DB::table('seller_services')->where("user_id", "100010")->delete();

        DB::table('seller_services')->insert([
            ['user_id' => 100006,
                'lkp_service_id' => 1,
                'is_service_offered' => 1,
                'is_service_required' => 0,
                'created_by' => 100006,
                'created_at' => '2017-03-01 00:00:00',
                'created_ip' => '182.72.152.98',
                'updated_by' => NULL,
                'updated_at' => '2017-03-01 00:00:00',
                'updated_ip' => ''

            ],
            ['user_id' => 100006,
                'lkp_service_id' => 2,
                'is_service_offered' => 1,
                'is_service_required' => 0,
                'created_by' => 100006,
                'created_at' => '2017-03-01 00:00:00',
                'created_ip' => '182.72.152.98',
                'updated_by' => NULL,
                'updated_at' => '2017-03-01 00:00:00',
                'updated_ip' => ''

            ],
            ['user_id' => 100007,
                'lkp_service_id' => 1,
                'is_service_offered' => 1,
                'is_service_required' => 0,
                'created_by' => 100007,
                'created_at' => '2017-03-01 00:00:00',
                'created_ip' => '182.72.152.98',
                'updated_by' => NULL,
                'updated_at' => '2017-03-01 00:00:00',
                'updated_ip' => ''

            ],
            ['user_id' => 100007,
                'lkp_service_id' => 2,
                'is_service_offered' => 1,
                'is_service_required' => 0,
                'created_by' => 100007,
                'created_at' => '2017-03-01 00:00:00',
                'created_ip' => '182.72.152.98',
                'updated_by' => NULL,
                'updated_at' => '2017-03-01 00:00:00',
                'updated_ip' => ''

            ],

            ['user_id' => 100008,
                'lkp_service_id' => 1,
                'is_service_offered' => 1,
                'is_service_required' => 0,
                'created_by' => 100009,
                'created_at' => '2017-03-01 00:00:00',
                'created_ip' => '182.72.152.98',
                'updated_by' => NULL,
                'updated_at' => '2017-03-01 00:00:00',
                'updated_ip' => ''

            ],
            ['user_id' => 100008,
                'lkp_service_id' => 2,
                'is_service_offered' => 1,
                'is_service_required' => 0,
                'created_by' => 100009,
                'created_at' => '2017-03-01 00:00:00',
                'created_ip' => '182.72.152.98',
                'updated_by' => NULL,
                'updated_at' => '2017-03-01 00:00:00',
                'updated_ip' => ''

            ],
            ['user_id' => 100009,
                'lkp_service_id' => 1,
                'is_service_offered' => 1,
                'is_service_required' => 0,
                'created_by' => 100009,
                'created_at' => '2017-03-01 00:00:00',
                'created_ip' => '182.72.152.98',
                'updated_by' => NULL,
                'updated_at' => '2017-03-01 00:00:00',
                'updated_ip' => ''

            ],
            ['user_id' => 100009,
                'lkp_service_id' => 2,
                'is_service_offered' => 1,
                'is_service_required' => 0,
                'created_by' => 100009,
                'created_at' => '2017-03-01 00:00:00',
                'created_ip' => '182.72.152.98',
                'updated_by' => NULL,
                'updated_at' => '2017-03-01 00:00:00',
                'updated_ip' => ''

            ],
            ['user_id' => 100010,
                'lkp_service_id' => 1,
                'is_service_offered' => 1,
                'is_service_required' => 0,
                'created_by' => 100010,
                'created_at' => '2017-03-01 00:00:00',
                'created_ip' => '182.72.152.98',
                'updated_by' => NULL,
                'updated_at' => '2017-03-01 00:00:00',
                'updated_ip' => ''

            ],
            ['user_id' => 100010,
                'lkp_service_id' => 2,
                'is_service_offered' => 1,
                'is_service_required' => 0,
                'created_by' => 100010,
                'created_at' => '2017-03-01 00:00:00',
                'created_ip' => '182.72.152.98',
                'updated_by' => NULL,
                'updated_at' => '2017-03-01 00:00:00',
                'updated_ip' => ''

            ]

        ]);
    }
}
