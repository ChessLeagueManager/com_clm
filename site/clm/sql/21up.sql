--
-- 3.2.6  Anpassung Kategorie bei Veranstaltungen
--
ALTER TABLE `#__clm_termine` ADD `catidAlltime` smallint(6) unsigned NOT NULL DEFAULT '0' AFTER `address`;
ALTER TABLE `#__clm_termine` ADD `catidEdition` smallint(6) unsigned NOT NULL DEFAULT '0' AFTER `catidAlltime`;
