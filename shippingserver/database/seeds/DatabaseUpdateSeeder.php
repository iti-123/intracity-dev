<?php

use Illuminate\Database\Seeder;

class DatabaseUpdateSeeder extends Seeder
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
        $this->call(EmailEvents::class);
        $this->call(EmailTemplates::class);

    }
}
