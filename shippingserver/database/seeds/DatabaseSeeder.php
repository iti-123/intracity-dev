<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         *    Run LkpServiceGroupsSeeder, LkpServicesSeeder once
         *    Do not seed this after a migrate:refresh
         */
        // $this->call(LkpServiceGroupsSeeder::class);
        // $this->call(LkpServicesSeeder::class);
        $this->call(ShippingServicesSeeder::class);
        $this->call(CodeListSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(BuyerDetailsSeeder::class);
        $this->call(BuyerBusinessDetailsSeeder::class);
        $this->call(SellerDetailsSeeder::class);
        $this->call(SellerServicesSeeder::class);
        $this->call(UserSubscriptionServicesSeeder::class);
        $this->call(EmailEvents::class);
        $this->call(EmailTemplates::class);
        $this->call(UserSettingsSeeder::class);

        /*
         *
         * Add seeders for
         *
         * Code Lists
         * Services
         * Default users that we can use on all computers. This should be a combination of some buyers and sellers.
         * maybe like seller01 .. seller10, likewise buyer01 .. buyer10  - You can assign test email accounts.
         * All passwords should be the same.
         *
         *
         */

    }
}
