<?php

use Illuminate\Database\Seeder;

class DatabaseFreshInstallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ShippingServicesSeeder::class);
        $this->call(CodeListSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(BuyerDetailsSeeder::class);
        $this->call(SellerDetailsSeeder::class);
        $this->call(SellerServicesSeeder::class);
        $this->call(UserSubscriptionServicesSeeder::class);
        $this->call(EmailEvents::class);
        $this->call(EmailTemplates::class);
    }
}
