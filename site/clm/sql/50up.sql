--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2023 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.0.5  Spielgemeinschaften bei Ranglistensystem
--

SET SESSION SQL_MODE='ALLOW_INVALID_DATES';
UPDATE `#__clm_rangliste_id` SET checked_out_time = '1970-01-01 00:00:00' WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_rangliste_id` ADD `sg_zps` varchar(120) NOT NULL DEFAULT '00000' AFTER `zps`;

ALTER TABLE `#__clm_rangliste_spieler` ADD `ZPSmgl` varchar(5) NOT NULL DEFAULT '00000' AFTER `ZPS`;
UPDATE `#__clm_rangliste_spieler` SET ZPSmgl = ZPS WHERE ZPSmgl = '00000';

UPDATE `#__clm_rangliste_name` SET checked_out_time = '1970-01-01 00:00:00' WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_rangliste_name` MODIFY `user` int(11) unsigned NOT NULL DEFAULT '0';


--
-- 4.0.5  Korrektur von DEFAULT-Werten
--

UPDATE `#__clm_liga` SET checked_out_time = '1970-01-01 00:00:00' WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_liga` MODIFY `man_sieg` decimal(4,2) unsigned DEFAULT '2.00';
ALTER TABLE `#__clm_liga` MODIFY `man_remis` decimal(4,2) unsigned DEFAULT '1.00';

