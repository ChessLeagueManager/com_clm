--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2020 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 3.9.1  Import TRF-Dateien einschl. Einzelturnier mit Mannschaftswertung
--
ALTER TABLE `#__clm_swt_turniere`		MODIFY `dateStart` date NOT NULL DEFAULT '1970-01-01';
ALTER TABLE `#__clm_swt_turniere` 		MODIFY `dateEnd` date NOT NULL DEFAULT '1970-01-01';
ALTER TABLE `#__clm_swt_turniere_tlnr`	MODIFY `start_I0` smallint(6) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_swt_turniere_tlnr` 	MODIFY `tlnrStatus` tinyint(11) unsigned NOT NULL DEFAULT '1';
ALTER TABLE `#__clm_turniere`			MODIFY `dateStart` date NOT NULL DEFAULT '1970-01-01';
ALTER TABLE `#__clm_turniere` 			MODIFY `dateEnd` date NOT NULL DEFAULT '1970-01-01';
ALTER TABLE `#__clm_turniere` 			MODIFY `dateRegistration` date NOT NULL DEFAULT '1970-01-01';
ALTER TABLE `#__clm_turniere_tlnr`		MODIFY `start_I0` smallint(6) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `#__clm_swt_turniere_tlnr` 	ADD `mtln_nr` mediumint(5) unsigned DEFAULT NULL AFTER `tlnrStatus`;
ALTER TABLE `#__clm_turniere_tlnr` 		ADD `mtln_nr` mediumint(5) unsigned DEFAULT NULL AFTER `tlnrStatus`;

CREATE TABLE IF NOT EXISTS `#__clm_swt_turniere_teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `swt_tid` int(11) unsigned DEFAULT NULL,
  `tid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `sid` mediumint(5) unsigned DEFAULT NULL,
  `tln_nr` mediumint(5) unsigned DEFAULT NULL,
  `zps` varchar(5) DEFAULT NULL,
  `man_nr` mediumint(5) unsigned DEFAULT NULL,
  `published` mediumint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tid_tlnnr` (`swt_tid`,`tln_nr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__clm_turniere_teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `sid` mediumint(5) unsigned DEFAULT NULL,
  `tln_nr` mediumint(5) unsigned DEFAULT NULL,
  `zps` varchar(5) DEFAULT NULL,
  `man_nr` mediumint(5) unsigned DEFAULT NULL,
  `published` mediumint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tid_tlnnr` (`tid`,`tln_nr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
