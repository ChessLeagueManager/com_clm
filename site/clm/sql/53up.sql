--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2023 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.1.1  Erweiterung Einzelturniere
--

SET SESSION SQL_MODE='ALLOW_INVALID_DATES';

UPDATE `#__clm_swt_turniere` SET checked_out_time = '1970-01-01 00:00:00' WHERE checked_out_time = '0000-00-00 00:00:00';
UPDATE `#__clm_swt_turniere` SET dateStart = '1970-01-01' WHERE dateStart = '0000-00-00';
UPDATE `#__clm_swt_turniere` SET dateEnd = '1970-01-01' WHERE dateEnd = '0000-00-00';
ALTER TABLE `#__clm_swt_turniere` ADD torg int(11) unsigned DEFAULT NULL AFTER tl;

UPDATE `#__clm_turniere` SET checked_out_time = '1970-01-01 00:00:00' WHERE checked_out_time = '0000-00-00 00:00:00';
UPDATE `#__clm_turniere` SET dateStart = '1970-01-01' WHERE dateStart = '0000-00-00';
UPDATE `#__clm_turniere` SET dateEnd = '1970-01-01' WHERE dateEnd = '0000-00-00';
ALTER TABLE `#__clm_turniere` MODIFY tl int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_turniere` ADD torg int(11) unsigned DEFAULT NULL AFTER tl;

