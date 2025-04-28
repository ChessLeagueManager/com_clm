--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2025 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.3.4  Schiedsrichtertabellen anpassen
--

ALTER TABLE `#__clm_arbiter_turnier` DROP COLUMN `tkz`;
ALTER TABLE `#__clm_arbiter_turnier` MODIFY `turnier` int(11) DEFAULT NULL;
ALTER TABLE `#__clm_arbiter_turnier` ADD `liga` int(11) DEFAULT NULL AFTER `turnier`;
ALTER TABLE `#__clm_arbiter_turnier` ADD `trole` varchar(3) DEFAULT NULL AFTER `paar`;
ALTER TABLE `#__clm_arbiter_turnier` MODIFY `role` varchar(3) DEFAULT NULL;
