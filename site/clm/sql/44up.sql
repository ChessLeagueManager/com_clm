--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2021 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 3.9.1  Import SWM-Dateien Mannschaftsturniere TUMX UND TUTX
--
ALTER TABLE `#__clm_swt_liga`		MODIFY `auf` tinyint(1) unsigned DEFAULT '0';
ALTER TABLE `#__clm_swt_liga`		MODIFY `auf_evtl` tinyint(1) unsigned DEFAULT '0';
ALTER TABLE `#__clm_swt_liga`		MODIFY `ab` tinyint(1) unsigned DEFAULT '0';
ALTER TABLE `#__clm_swt_liga`		MODIFY `ab_evtl` tinyint(1) unsigned DEFAULT '0';
ALTER TABLE `#__clm_liga`			MODIFY `auf` tinyint(1) unsigned DEFAULT '0';
ALTER TABLE `#__clm_liga`			MODIFY `auf_evtl` tinyint(1) unsigned DEFAULT '0';
ALTER TABLE `#__clm_liga`			MODIFY `ab` tinyint(1) unsigned DEFAULT '0';
ALTER TABLE `#__clm_liga`			MODIFY `catidAlltime` smallint(6) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_liga`			MODIFY `catidEdition` smallint(6) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `#__clm_mannschaften`			MODIFY `sg_zps` varchar(120) DEFAULT NULL;
ALTER TABLE `#__clm_mannschaften`			MODIFY `lokal` text DEFAULT NULL;
ALTER TABLE `#__clm_mannschaften`			MODIFY `bemerkungen` text DEFAULT NULL;
ALTER TABLE `#__clm_mannschaften`			MODIFY `bem_int` text DEFAULT NULL;
ALTER TABLE `#__clm_swt_mannschaften`		MODIFY `sg_zps` varchar(120) DEFAULT NULL;
ALTER TABLE `#__clm_swt_mannschaften`		MODIFY `lokal` text DEFAULT NULL;
ALTER TABLE `#__clm_swt_mannschaften`		MODIFY `bemerkungen` text DEFAULT NULL;
ALTER TABLE `#__clm_swt_mannschaften`		MODIFY `bem_int` text DEFAULT NULL;

ALTER TABLE `#__clm_swt_mannschaften` DROP INDEX `sid_name_swtid`; 
ALTER TABLE `#__clm_swt_mannschaften` ADD UNIQUE KEY `sid_tlnnr_swtid` (`sid`,`tln_nr`,`swt_id`);
-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_dwz_spieler`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_dwz_spieler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(6) unsigned DEFAULT NULL,
  `PKZ` varchar(9) DEFAULT NULL,
  `ZPS` varchar(5) NOT NULL DEFAULT '',
  `Mgl_Nr` mediumint(5) unsigned DEFAULT NULL,
  `Status` char(1) DEFAULT NULL,
  `Spielername` varchar(50) NOT NULL DEFAULT '',
  `Spielername_G` varchar(50) NOT NULL DEFAULT '',
  `Geschlecht` char(1) DEFAULT NULL,
  `Spielberechtigung` char(1) NOT NULL DEFAULT '',
  `Geburtsjahr` year(4) NOT NULL DEFAULT '0000',
  `Junior` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Letzte_Auswertung` mediumint(6) unsigned DEFAULT NULL,
  `DWZ` smallint(4) unsigned DEFAULT NULL,
  `DWZ_Index` smallint(3) unsigned DEFAULT NULL,
  `FIDE_Elo` smallint(4) unsigned DEFAULT NULL,
  `FIDE_Titel` char(3) DEFAULT NULL,
  `FIDE_ID` int(8) unsigned DEFAULT NULL,
  `FIDE_Land` char(3) DEFAULT NULL,
  `DWZ_neu` smallint(4) unsigned NOT NULL default '0',
  `I0` smallint(4) unsigned NOT NULL default '0',
  `Punkte` decimal(4,1) unsigned NOT NULL default '0.0',
  `Partien` tinyint(3) NOT NULL default '0',
  `We` decimal(6,3) NOT NULL default '0.000',
  `Leistung` smallint(4) NOT NULL default '0',
  `EFaktor` tinyint(2) NOT NULL default '0',
  `Niveau` smallint(4) NOT NULL default '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid_zps_mglnr` (`sid`,`ZPS`,`Mgl_Nr`),
  KEY `sid` (`sid`),
  KEY `ZPS` (`ZPS`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Tabellenstruktur für Tabelle `#__clm_swt_runden_termine`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_runden_termine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `liga` mediumint(5) unsigned DEFAULT NULL,
  `swt_liga` mediumint(5) unsigned DEFAULT NULL,
  `nr` mediumint(5) unsigned DEFAULT NULL,
  `datum` date NOT NULL DEFAULT '1970-01-01',
  `startzeit` time NOT NULL DEFAULT '00:00:00',
  `deadlineday` date NOT NULL DEFAULT '1970-01-01',
  `deadlinetime` time NOT NULL DEFAULT '24:00:00',
  `meldung` tinyint(1) NOT NULL DEFAULT '0',
  `sl_ok` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `bemerkungen` text,
  `bem_int` text,
  `gemeldet` int(11) unsigned DEFAULT NULL,
  `editor` int(11) unsigned DEFAULT NULL,
  `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `enddatum` date NOT NULL DEFAULT '1970-01-01',
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

