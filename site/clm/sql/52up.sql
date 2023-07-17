--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2023 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.0.5  Auswahl Mitgliederstatus im Ranglistensystem 
--

SET SESSION SQL_MODE='ALLOW_INVALID_DATES';
UPDATE `#__clm_rangliste_name` SET checked_out_time = '1970-01-01 00:00:00' WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_rangliste_name` ADD `status` varchar(3) NOT NULL DEFAULT '' AFTER `alter`;

UPDATE `#__clm_rangliste_spieler` SET ZPSmgl = ZPS WHERE ZPSmgl = '00000';

