--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2019 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 3.7.3: Online-Turnieranmeldung (Backend)
--

--
-- Tabellenstruktur für Tabelle `#__clm_online_registration` erweitert
--
ALTER TABLE `#__clm_online_registration` ADD `birthYear` year(4) NOT NULL DEFAULT '0000' AFTER `vorname`;
ALTER TABLE `#__clm_online_registration` ADD `geschlecht` char(1) DEFAULT NULL AFTER `birthYear`;
ALTER TABLE `#__clm_online_registration` ADD `FIDEid` int(8) DEFAULT NULL AFTER `elo`;
ALTER TABLE `#__clm_online_registration` ADD `FIDEcco` char(3) DEFAULT NULL AFTER `FIDEid`;
ALTER TABLE `#__clm_online_registration` ADD `dwz_I0` smallint(6) NOT NULL DEFAULT '0' AFTER `dwz`;
ALTER TABLE `#__clm_online_registration` ADD `titel` char(3) DEFAULT NULL AFTER `dwz_I0`;
ALTER TABLE `#__clm_online_registration` ADD `mgl_nr` mediumint(5) NOT NULL DEFAULT '0' AFTER `titel`;
ALTER TABLE `#__clm_online_registration` ADD `PKZ` varchar(9) DEFAULT NULL AFTER `mgl_nr`;
ALTER TABLE `#__clm_online_registration` ADD `zps` varchar(5) NOT NULL DEFAULT '' AFTER `PKZ`;
ALTER TABLE `#__clm_online_registration` ADD `ordering` int(11) NOT NULL DEFAULT '0' AFTER `comment`;
