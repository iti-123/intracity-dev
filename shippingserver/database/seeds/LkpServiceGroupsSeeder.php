<?php

use Illuminate\Database\Seeder;

class LkpServiceGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('lkp_invoice_service_groups')->insert([
            array('invoice_service_group_name' => 'Ocean'),
            array('invoice_service_group_name' => 'Air'),
            array('invoice_service_group_name' => 'CrossBorder'),
            array('invoice_service_group_name' => 'Marine or Port services'),
        ]);
    }

    public function down()
    {
        DB::table('lkp_invoice_service_groups')->delete();
    }
}
