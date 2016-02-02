--
-- 3.2.4  DB-Erweiterung für Rundendatum von-bis einschl. Termin pro Paarung
--
ALTER TABLE `#__clm_runden_termine` ADD `enddatum` date NOT NULL DEFAULT '0000-00-00' AFTER `ordering`;
ALTER TABLE `#__clm_rnd_man` ADD `pdate` date NOT NULL DEFAULT '0000-00-00' AFTER `comment`;
ALTER TABLE `#__clm_rnd_man` ADD `ptime` time NOT NULL DEFAULT '00:00:00' AFTER `pdate`;
