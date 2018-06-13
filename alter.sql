ALTER TABLE  `intra_hp_sellerpost_ratecart` ADD  `assign_buyer` TEXT NULL ;
ALTER TABLE  `intra_hp_sellerpost_ratecart` ADD  `routedata` TEXT NULL ;
ALTER TABLE  `intra_hp_sellerpost_ratecart` ADD  `discount` TEXT NULL ;

ALTER TABLE  `intra_hp_sellerpost_ratecart` ADD  `routedata` TEXT NULL ;
ALTER TABLE  `intra_hp_sellerpost_ratecart` ADD  `discount` TEXT NULL ;

ALTER TABLE  `intra_hp_sellerpost_ratecart` ADD  `transectionId` VARCHAR( 200 ) NOT NULL;

*****************************************************************************************
				01-07-2017
*****************************************************************************************

ALTER TABLE `intra_hp_buyer_posts` CHANGE `product_type` `lkp_service_id` INT(11) NOT NULL COMMENT '3=intracity';
ALTER TABLE `intra_hp_buyer_posts` ADD `visible_to_seller` TEXT NULL AFTER `attribute`;
ALTER TABLE `intra_hp_buyer_posts` CHANGE `average_turn_over` `average_turn_over` INT(11) NOT NULL COMMENT 'in lakhs';
ALTER TABLE `intra_hp_buyer_posts` CHANGE `income_tax_assesse` `income_tax_assesse` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '1=yes,0=no';
ALTER TABLE `intra_hp_buyer_posts` CHANGE `term_contract_woc` `term_contract_woc` INT(11) NOT NULL COMMENT '1=yes, 0=no, Term Contract with other companies';
ALTER TABLE `intra_hp_buyer_posts` ADD `json_data` LONGTEXT NULL AFTER `attribute`;
ALTER TABLE `intra_hp_assigned_seller_buyer` CHANGE `type` `type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '2=seller,1=buyer';
ALTER TABLE `intra_hp_buyer_posts` ADD `post_transaction_id` VARCHAR(255) NOT NULL COMMENT 'Post transaction Id' AFTER `emd_mode`;
