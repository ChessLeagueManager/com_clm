--
-- 3.3.4  Einzelturniere: Einbau 3-Punkte-Wertung u. a.
--
ALTER TABLE `#__clm_turniere` ADD `sieg` decimal(2,1) unsigned DEFAULT '1.0' AFTER `params`;
ALTER TABLE `#__clm_turniere` ADD `siegs` decimal(2,1) unsigned DEFAULT '1.0' AFTER `sieg`;
ALTER TABLE `#__clm_turniere` ADD `remis` decimal(2,1) unsigned DEFAULT '0.5' AFTER `siegs`;
ALTER TABLE `#__clm_turniere` ADD `remiss` decimal(2,1) unsigned DEFAULT '0.5' AFTER `remis`;
ALTER TABLE `#__clm_turniere` ADD `nieder` decimal(2,1) unsigned DEFAULT '0.0' AFTER `remiss`;
ALTER TABLE `#__clm_turniere` ADD `niederk` decimal(2,1) unsigned DEFAULT '0.0' AFTER `nieder`;

ALTER TABLE `#__clm_swt_turniere` ADD `sieg` decimal(2,1) unsigned DEFAULT '1.0' AFTER `params`;
ALTER TABLE `#__clm_swt_turniere` ADD `siegs` decimal(2,1) unsigned DEFAULT '1.0' AFTER `sieg`;
ALTER TABLE `#__clm_swt_turniere` ADD `remis` decimal(2,1) unsigned DEFAULT '0.5' AFTER `siegs`;
ALTER TABLE `#__clm_swt_turniere` ADD `remiss` decimal(2,1) unsigned DEFAULT '0.5' AFTER `remis`;
ALTER TABLE `#__clm_swt_turniere` ADD `nieder` decimal(2,1) unsigned DEFAULT '0.0' AFTER `remiss`;
ALTER TABLE `#__clm_swt_turniere` ADD `niederk` decimal(2,1) unsigned DEFAULT '0.0' AFTER `nieder`;
