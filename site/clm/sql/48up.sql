--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2022 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.0.1  Ergebnismeldung mit interner Bemerkung (Rückwirkung auf Importe)
--

SET SESSION SQL_MODE='ALLOW_INVALID_DATES';
UPDATE `#__clm_swt_rnd_man` SET checked_out_time = '1970-01-01 00:00:00' WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_swt_rnd_man` ADD `icomment` text NOT NULL  AFTER `comment`;


