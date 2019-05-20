--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2019 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 3.7.1  Rankingdaten zum Ausweis von Zwischenständen 
--
ALTER TABLE `#__clm_mannschaften` ADD `z_summanpunkte` decimal(4,1) DEFAULT NULL AFTER `bpabzug`;
ALTER TABLE `#__clm_mannschaften` ADD `z_sumbrettpunkte` decimal(4,1) DEFAULT NULL AFTER `z_summanpunkte`;
ALTER TABLE `#__clm_mannschaften` ADD `z_sumwins` tinyint(2) DEFAULT NULL AFTER `z_sumbrettpunkte`;
ALTER TABLE `#__clm_mannschaften` ADD `z_sumtiebr1` decimal(7,3) DEFAULT '0.000' AFTER `z_sumwins`;
ALTER TABLE `#__clm_mannschaften` ADD `z_sumtiebr2` decimal(7,3) DEFAULT '0.000' AFTER `z_sumtiebr1`;
ALTER TABLE `#__clm_mannschaften` ADD `z_sumtiebr3` decimal(7,3) DEFAULT '0.000' AFTER `z_sumtiebr2`;
ALTER TABLE `#__clm_mannschaften` ADD `z_rankingpos` tinyint(3) unsigned NOT NULL DEFAULT '0' AFTER `z_sumtiebr3`;


