--
-- 3.1.10 Ligen und Mannschaftsturniere besitzen nun auch eine Kategorie
--
ALTER TABLE `#__clm_liga` ADD `catidAlltime` SMALLINT(6) UNSIGNED NOT NULL AFTER `teil`;
ALTER TABLE `#__clm_liga` ADD `catidEdition` SMALLINT(6) UNSIGNED NOT NULL AFTER `catidAlltime`;
