--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2025 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.3.4  Schiedsrichtertabellen anpassen
--

ALTER TABLE `#__clm_arbiter` ADD `title` varchar(3) DEFAULT NULL AFTER `source`;
ALTER TABLE `#__clm_arbiter` ADD `fidefed` char(3) DEFAULT 'GER' AFTER `fideid`;
ALTER TABLE `#__clm_arbiter` ADD `published` mediumint(3) UNSIGNED DEFAULT NULL AFTER `fidefed`;
ALTER TABLE `#__clm_arbiter` ADD `ordering` int(11) DEFAULT 0 AFTER `published`;
ALTER TABLE `#__clm_arbiter` DROP COLUMN `endoflicense`;
ALTER TABLE `#__clm_arbiter` ADD `bemerkungen` text DEFAULT NULL AFTER `mobil`;
ALTER TABLE `#__clm_arbiter` ADD `bem_int` text DEFAULT NULL AFTER `bemerkungen`;

ALTER TABLE `#__clm_arbiter` DROP KEY `arb_uuid`;
ALTER TABLE `#__clm_arbiter` ADD UNIQUE KEY  `fideid` (`fideid`);

ALTER TABLE `#__clm_arbiter_turnier` CHANGE `r_arbiter` `fideid` int(11) UNSIGNED NOT NULL;
ALTER TABLE `#__clm_arbiter_turnier` CHANGE `r_turnier` `turnier` int(11) UNSIGNED NOT NULL;
ALTER TABLE `#__clm_arbiter_turnier` DROP COLUMN `funktion`;
ALTER TABLE `#__clm_arbiter_turnier` ADD `tkz` char(1) NOT NULL AFTER `fideid`;
ALTER TABLE `#__clm_arbiter_turnier` ADD `dg` tinyint(3) UNSIGNED NOT NULL AFTER `turnier`;
ALTER TABLE `#__clm_arbiter_turnier` ADD `runde` tinyint(3) UNSIGNED NOT NULL AFTER `dg`;
ALTER TABLE `#__clm_arbiter_turnier` ADD `paar` tinyint(3) UNSIGNED NOT NULL AFTER `runde`;
ALTER TABLE `#__clm_arbiter_turnier` ADD `role` char(3) NOT NULL AFTER `paar`;
