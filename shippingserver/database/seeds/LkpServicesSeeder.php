<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LkpServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lkp_services')->insert([

            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'International', 'service_name' => 'Ocean  FCL', 'subcategory_id' => 0, 'group_name' => 'Container', 'service_image_path' => 'images/log-icons/FCL.svg', 'is_active' => 1, 'created_at' => Carbon::now()),
            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'International', 'service_name' => 'Ocean  LCL', 'subcategory_id' => 0, 'group_name' => 'Container', 'service_image_path' => 'images/log-icons/LCL.svg', 'is_active' => 1, 'created_at' => Carbon::now()),

            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'International', 'service_name' => 'Dry Bulk', 'subcategory_id' => 0, 'group_name' => 'Bulk', 'service_image_path' => 'images/log-icons/Dry_Bulk.svg', 'is_active' => 1, 'created_at' => Carbon::now()),
            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'International', 'service_name' => 'Liquid Bulk', 'subcategory_id' => 0, 'group_name' => 'Bulk', 'service_image_path' => 'images/log-icons/Liquid_Bulk.svg', 'is_active' => 1, 'created_at' => Carbon::now()),
            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'International', 'service_name' => 'Break Bulk', 'subcategory_id' => 0, 'group_name' => 'Bulk', 'service_image_path' => 'images/log-icons/Break_Bulk.svg', 'is_active' => 1, 'created_at' => Carbon::now()),

            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'International', 'service_name' => 'Ro Ro', 'subcategory_id' => '0', 'group_name' => 'Coastal', 'service_image_path' => 'images/log-icons/roro.svg', 'is_active' => 1, 'created_at' => Carbon::now()),
            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'assets', 'service_name' => 'Vessel Chartering', 'subcategory_id' => '0', 'group_name' => 'Ocean', 'service_image_path' => 'images/log-icons/no_image.svg', 'is_active' => 1, 'created_at' => Carbon::now()),


            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'Coastal', 'service_name' => 'Dry Bulk', 'subcategory_id' => '0', 'group_name' => 'Ocean', 'service_image_path' => 'images/log-icons/Dry_Bulk.svg', 'is_active' => 1, 'created_at' => Carbon::now()),
            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'Coastal', 'service_name' => 'RoRo', 'subcategory_id' => '0', 'group_name' => 'Ocean', 'service_image_path' => 'images/log-icons/roro.svg', 'is_active' => 1, 'created_at' => Carbon::now()),


            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'Coastal', 'service_name' => 'FCL', 'subcategory_id' => '0', 'group_name' => 'Coastal', 'service_image_path' => 'images/log-icons/FCL.svg', 'is_active' => 1, 'created_at' => Carbon::now()),
            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'Coastal', 'service_name' => 'LCL', 'subcategory_id' => '0', 'group_name' => 'Coastal', 'service_image_path' => 'images/log-icons/LCL.svg', 'is_active' => 1, 'created_at' => Carbon::now()),

            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'Air Freight', 'service_name' => 'Domestic', 'subcategory_id' => '0', 'group_name' => 'Ocean', 'service_image_path' => 'images/log-icons/no_image.svg', 'is_active' => 1, 'created_at' => Carbon::now()),
            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'Port Services', 'service_name' => 'International', 'subcategory_id' => '0', 'group_name' => 'Ocean', 'service_image_path' => 'images/log-icons/port_marine_services.svg', 'is_active' => 1, 'created_at' => Carbon::now()),

            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'assets', 'service_name' => 'Air Chartering', 'subcategory_id' => '0', 'group_name' => 'Ocean', 'service_image_path' => 'images/log-icons/no_image.svg', 'is_active' => 1, 'created_at' => Carbon::now()),

            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'Port services', 'service_name' => 'Port services', 'subcategory_id' => '0', 'group_name' => 'Port services', 'service_image_path' => 'images/log-icons/MarinePort.png', 'is_active' => 1, 'created_at' => Carbon::now()),

            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'International', 'service_name' => 'Marine services', 'subcategory_id' => '0', 'group_name' => 'Marine services', 'service_image_path' => 'images/log-icons/no_image.svg', 'is_active' => 1, 'created_at' => Carbon::now()),

            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'International', 'service_name' => 'Cross Border', 'subcategory_id' => '0', 'group_name' => 'Ocean', 'service_image_path' => 'images/log-icons/no_image.svg', 'is_active' => 1, 'created_at' => Carbon::now()),

            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'bluecollar', 'service_name' => 'Blue Collar', 'subcategory_id' => '0', 'group_name' => 'bluecollar', 'service_image_path' => 'images/log-icons/no_image.svg', 'is_active' => 1, 'created_at' => Carbon::now()),

            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'Coastal', 'service_name' => 'Liquid', 'subcategory_id' => '0', 'group_name' => 'Ocean', 'service_image_path' => 'images/log-icons/Liquid_Bulk.svg', 'is_active' => 1, 'created_at' => Carbon::now()),
            array('lkp_invoice_service_group_id' => '2', 'service_crumb_name' => 'Coastal', 'service_name' => 'Break Bulk', 'subcategory_id' => '0', 'group_name' => 'Ocean', 'service_image_path' => 'images/log-icons/Break_Bulk.svg', 'is_active' => 1, 'created_at' => Carbon::now()),
        ]);
    }
}
