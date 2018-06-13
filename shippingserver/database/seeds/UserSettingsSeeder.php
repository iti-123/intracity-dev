<?php

use Illuminate\Database\Seeder;

class UserSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shp_user_settings')->where("user_id", "0")->delete();

        DB::table('shp_user_settings')->insert([
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'BuyerPostmaster',
                'settings' => 'SE.DisplayRelatedOffersOrLeadsAgainstPosts',
                'value' => 'true'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'BuyerPostmaster',
                'settings' => 'SE.DisplayRelatedPrivateRatecards',
                'value' => 'true'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'BuyerPostmaster',
                'settings' => 'SE.DisplayPartlyRelatedEnquires',
                'value' => 'false'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'BuyerPostmaster',
                'settings' => 'SE.DisplayUnrelatedEnquires',
                'value' => 'false'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'BuyerPostmaster',
                'settings' => 'SL.DisplayRelatedLeads',
                'value' => 'true'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'BuyerPostmaster',
                'settings' => 'SL.DisplayPartlyRelatedLeads',
                'value' => 'false'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'BuyerPostmaster',
                'settings' => 'SL.DisplayUnrelatedLeads',
                'value' => 'false'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'SellerPostmaster',
                'settings' => 'SE.DisplayPublicRelatedEnquires',
                'value' => 'true'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'SellerPostmaster',
                'settings' => 'SE.DisplayPublicPartyRelatedEnquires',
                'value' => 'false'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'SellerPostmaster',
                'settings' => 'SE.DisplayPublicUnRelatedEnquires',
                'value' => 'false'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'SellerPostmaster',
                'settings' => 'SL.DisplayPublicRelatedPosts',
                'value' => 'true'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'SellerPostmaster',
                'settings' => 'SL.DisplayPublicRelatedPosts',
                'value' => 'false'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'SellerPostmaster',
                'settings' => 'SL.DisplayPublicUnRelatedPosts',
                'value' => 'false'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'SellerPostmaster',
                'settings' => 'TE.DisplayMatchingTermEnquires',
                'value' => 'true'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'SellerPostmaster',
                'settings' => 'TE.DisplayPartyRelatedTermEnquires',
                'value' => 'false'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'SellerPostmaster',
                'settings' => 'TE.DisplayUnRelatedTermEnquires',
                'value' => 'false'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'SellerPostmaster',
                'settings' => 'TL.DisplayMatchingTermLeads',
                'value' => 'true'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'SellerPostmaster',
                'settings' => 'TL.DisplayPartyRelatedTermLeads',
                'value' => 'false'
            ],
            ['user_id' => 0,
                'service_id' => FCL,
                'context' => 'SellerPostmaster',
                'settings' => 'TL.DisplayUnRelatedTermLeads',
                'value' => 'false'
            ],
        ]);
        echo "Success";
    }
}
