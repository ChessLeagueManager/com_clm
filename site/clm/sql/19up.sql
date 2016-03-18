--
-- 3.2.5  Erweiterung notwendig für Ranglistenkorrektur nach FIDE
--
ALTER TABLE `#__clm_rnd_man` ADD `ergebnis` mediumint(5) unsigned DEFAULT NULL AFTER `gegner`;
ALTER TABLE `#__clm_rnd_man` ADD `kampflos` tinyint(1) unsigned DEFAULT NULL AFTER `ergebnis`;
ALTER TABLE `#__clm_swt_rnd_man` ADD `ergebnis` mediumint(5) unsigned DEFAULT NULL AFTER `gegner`;
ALTER TABLE `#__clm_swt_rnd_man` ADD `kampflos` tinyint(1) unsigned DEFAULT NULL AFTER `ergebnis`;
