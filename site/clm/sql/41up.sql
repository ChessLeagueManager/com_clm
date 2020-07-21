--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2020 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 3.8.4  Erweiterung Online-Anmeldung
--
ALTER TABLE `#__clm_online_registration` ADD `tel_no` varchar(30) NOT NULL DEFAULT '' AFTER `zps`;
ALTER TABLE `#__clm_online_registration` ADD `account` varchar(50) NOT NULL DEFAULT '' AFTER `tel_no`;
ALTER TABLE `#__clm_online_registration` ADD `pid` varchar(32) NOT NULL DEFAULT '' AFTER `ordering`;
ALTER TABLE `#__clm_online_registration` ADD `approved` tinyint(1) NOT NULL DEFAULT '0' AFTER `pid`;
ALTER TABLE `#__clm_turniere_tlnr` ADD `email` varchar(100) NOT NULL DEFAULT '' AFTER `verein`;
ALTER TABLE `#__clm_turniere_tlnr` ADD `tel_no` varchar(30) NOT NULL DEFAULT '' AFTER `zps`;
ALTER TABLE `#__clm_turniere_tlnr` ADD `account` varchar(50) NOT NULL DEFAULT '' AFTER `tel_no`;
