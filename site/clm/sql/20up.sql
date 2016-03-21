--
-- 3.2.5  Erweiterung Abzug Brettpunkte
--
ALTER TABLE `#__clm_mannschaften` ADD `bpabzug` decimal(3,1) DEFAULT '0.0' AFTER `abzug`;
