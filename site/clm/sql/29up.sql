--
-- 3.6.1  User-Check auf Vereinszugehörigkeit 
--
ALTER TABLE `#__clm_user` ADD `PKZ` varchar(9) DEFAULT NULL AFTER `mglnr`;
ALTER TABLE `#__clm_user` ADD `org_exc` enum('0', '1') NOT NULL DEFAULT '0' AFTER `PKZ`;

