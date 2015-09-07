--
-- 3.1.19 Saisonstatistik wieder ermöglichen
--
ALTER TABLE `#__clm_dwz_spieler` ADD `DWZ_neu` smallint(4) unsigned NOT NULL default '0' AFTER `FIDE_Land`;
ALTER TABLE `#__clm_dwz_spieler` ADD `I0` smallint(4) unsigned NOT NULL default '0' AFTER `DWZ_neu`;
ALTER TABLE `#__clm_dwz_spieler` ADD `Punkte` decimal(4,1) unsigned NOT NULL default '0.0' AFTER `I0`;
ALTER TABLE `#__clm_dwz_spieler` ADD `Partien` tinyint(3) NOT NULL default '0' AFTER `Punkte`;
ALTER TABLE `#__clm_dwz_spieler` ADD `We` decimal(6,3) NOT NULL default '0.000' AFTER `Partien`;
ALTER TABLE `#__clm_dwz_spieler` ADD `Leistung` smallint(4) NOT NULL default '0' AFTER `We`;
ALTER TABLE `#__clm_dwz_spieler` ADD `EFaktor` tinyint(2) NOT NULL default '0' AFTER `Leistung`;
ALTER TABLE `#__clm_dwz_spieler` ADD `Niveau` smallint(4) NOT NULL default '0' AFTER `EFaktor`;
