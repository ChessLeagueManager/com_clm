--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2023 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.0.5  Erweiterung Import Turnierteilnehmer
--

SET SESSION SQL_MODE='ALLOW_INVALID_DATES';
UPDATE `#__clm_swt_turniere_tlnr` SET checked_out_time = '1970-01-01 00:00:00' WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_swt_turniere_tlnr` ADD email varchar(100) NOT NULL DEFAULT '' AFTER verein;
ALTER TABLE `#__clm_swt_turniere_tlnr` ADD tel_no varchar(30) NOT NULL DEFAULT '' AFTER zps;

