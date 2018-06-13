/*************************INSTRUCTIONS TO RUN MIGRATION SCRIPTS ON YOUR LOCAL ENVIRONMENT
Created By: Nazia Rahaman
Date Created:(2/2/2017)**********************/

Follow the below instructions to setup the shipping database

Step 1:
Check your .env file whether it is pointing to right database.

Step 2
------
Navigate to your local server folder on the command prompt:
Eg: D:/shipping/shippingServer >

Step 3:
------
Laravel stores migration statu in the table called migration, check if it exist or not.
If it does not exist then run the following command to create the migration tables.

 php artisan migrate:install

Step 4 :
------
Recreate shipping tables by issuing drop-create.
php artisan migrate:refresh

Note:While running above command, if you encounter any issues let Nazia and Shivaram know about it.

Step 5:
------
You can check the status of migration by running the folloiwng command
php artisan migrate:status

Example
G:\Accounts\Logistiks\GIT\code\shipping\shippingserver>php artisan migrate:status
+------+-----------------------------------------------------+
| Ran? | Migration                                           |
+------+-----------------------------------------------------+
| Y    | 2014_10_12_000000_create_users_table                |
| Y    | 2014_10_12_100000_create_password_resets_table      |
| Y    | 2017_01_27_103019_shp_log                           |
| Y    | 2017_01_27_111013_shp_buyer_post_selected_sellers   |
| Y    | 2017_01_27_112229_shp_seller_posts                  |
| Y    | 2017_01_27_115455_shp_buyer_posts                   |
| Y    | 2017_01_31_063401_create_jobs_table                 |
| Y    | 2017_01_31_063402_shp_fcl_search_buyer_post         |
| Y    | 2017_02_07_030410_create_seller_post_searches_table |
| Y    | 2017_02_07_094344_shp_codelist                      |
| Y    | 2017_02_09_190202_shp_upload_files                  |
| N    | 2017_02_10_123010_shp_audit_log                     |
+------+-----------------------------------------------------+

In the above migration status table, Wherever you see 'Y' in Ran? column, all those tables are successfully created
in your database.





========================
Fixing migration issues
========================

1) What do I do, when I face issues as indicated below.

a) PHP Fatal error:  Class 'ShpBuyerPostSelectedSellers' not found in /home/shivaram
[Symfony\Component\Debug\Exception\FatalErrorException]
  Class 'ShpBuyerPostSelectedSellers' not found

b) SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'shp_buyer_posts' already exists

Solution Steps:

1) Navigate to folder shipping-server under your git folder and issue the following command.
composer dump-autoload

2) Login to mysql as logistiks users, change to logistiks database and issue the following command.
delete from migrations;

3) Attempt to run the following command
php artisan migrate:refresh

4) The above command might complain that certain tables already exist. Login to mysql as the logistiks user and issue a drop command.
drop table shp-xxxxx;

5) Rerun the command and repeat step-4 for all errors.
php artisan migrate:refresh
php artisan migrate:refresh --seed

6) Finally this should work. Rerun it again to it works correctly. It should drop all tables and recreate them successfully.


Database Seeding to setup Default Data
--------------------------------------

-- To setup all seeders.
php artisan migrate:refresh --seed


-- To setup individual seeders
php artisan db:seed --class= UsersSeeder
php artisan db:seed --class= BuyerDetailsSeeder
php artisan db:seed --class= SellerDetailsSeeder
php artisan db:seed --class= SellerServicesSeeder
php artisan db:seed --class= UserSubscriptionServicesSeeder
php artisan db:seed --class= CodeListSeeder
