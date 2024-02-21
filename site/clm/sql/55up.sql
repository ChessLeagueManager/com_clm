--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2024 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.1.4  Import/Export von Terminen in iCal-Format 
--

ALTER TABLE `#__clm_termine` ADD `uid` varchar(256) NOT NULL DEFAULT '' AFTER `event_link`;
ALTER TABLE `#__clm_termine` ADD `created` varchar(16) NOT NULL DEFAULT '' AFTER `uid`;
ALTER TABLE `#__clm_termine` ADD `last_modified` varchar(16) NOT NULL DEFAULT '' AFTER `created`;

UPDATE `#__clm_termine` SET uid = id WHERE uid = '';

