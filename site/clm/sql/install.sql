--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2019 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 08. Jul 2014 um 13:33
-- Server Version: 5.6.16
-- PHP-Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `clm`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_categories`
--

CREATE TABLE IF NOT EXISTS `#__clm_categories` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `parentid` smallint(6) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `sid` mediumint(3) unsigned DEFAULT NULL,
  `dateStart` date NOT NULL,
  `dateEnd` date NOT NULL,
  `tl` mediumint(5) unsigned DEFAULT NULL,
  `bezirk` varchar(8) DEFAULT NULL,
  `bezirkTur` enum('0','1') NOT NULL DEFAULT '1',
  `vereinZPS` varchar(5) DEFAULT NULL,
  `published` mediumint(3) unsigned DEFAULT NULL,
  `started` tinyint(1) NOT NULL DEFAULT '0',
  `finished` tinyint(1) NOT NULL DEFAULT '0',
  `invitationText` text,
  `bemerkungen` text,
  `bem_int` text,
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_config`
--

CREATE TABLE IF NOT EXISTS `#__clm_config` (
  `id` int(11) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_dwz_spieler`
--

CREATE TABLE IF NOT EXISTS `#__clm_dwz_spieler` (
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

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_dwz_verbaende`
--

CREATE TABLE IF NOT EXISTS `#__clm_dwz_verbaende` (
  `Verband` char(4) NOT NULL DEFAULT '',
  `LV` char(1) NOT NULL DEFAULT '',
  `Uebergeordnet` char(4) NOT NULL DEFAULT '',
  `Verbandname` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`Verband`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_dwz_vereine`
--

CREATE TABLE IF NOT EXISTS `#__clm_dwz_vereine` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `sid` mediumint(6) unsigned default NULL, 
  `ZPS` varchar(5) NOT NULL DEFAULT '',
  `LV` char(1) NOT NULL DEFAULT '',
  `Verband` varchar(4) NOT NULL DEFAULT '',
  `Vereinname` varchar(60) NOT NULL DEFAULT '',
  UNIQUE KEY `sid_ZPS` (`sid`,`ZPS`),
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_ergebnis`
--

CREATE TABLE IF NOT EXISTS `#__clm_ergebnis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eid` mediumint(5) unsigned DEFAULT NULL,
  `erg_text` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_liga`
--

CREATE TABLE IF NOT EXISTS `#__clm_liga` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `sid` mediumint(3) unsigned DEFAULT NULL,
  `catidAlltime` smallint(6) unsigned NOT NULL DEFAULT '0',
  `catidEdition` smallint(6) unsigned NOT NULL DEFAULT '0',
  `teil` mediumint(5) unsigned DEFAULT NULL,
  `stamm` mediumint(5) unsigned DEFAULT NULL,
  `ersatz` mediumint(5) unsigned DEFAULT NULL,
  `rang` tinyint(1) unsigned DEFAULT '0',
  `sl` mediumint(5) unsigned DEFAULT NULL,
  `runden` mediumint(5) unsigned DEFAULT NULL,
  `durchgang` mediumint(5) unsigned DEFAULT NULL,
  `mail` tinyint(1) unsigned DEFAULT NULL,
  `sl_mail` tinyint(1) unsigned DEFAULT NULL,
  `heim` tinyint(1) unsigned DEFAULT NULL,
  `sieg_bed` tinyint(2) unsigned DEFAULT NULL,
  `runden_modus` tinyint(2) unsigned DEFAULT NULL,
  `man_sieg` decimal(4,2) unsigned DEFAULT '1.00',
  `man_remis` decimal(4,2) unsigned DEFAULT '0.50',
  `man_nieder` decimal(4,2) unsigned DEFAULT '0.00',
  `man_antritt` decimal(4,2) unsigned DEFAULT '0.00',
  `sieg` decimal(2,1) unsigned DEFAULT '1.0',
  `remis` decimal(2,1) unsigned DEFAULT '0.5',
  `nieder` decimal(2,1) unsigned DEFAULT '0.0',
  `antritt` decimal(2,1) unsigned DEFAULT '0.0',
  `order` tinyint(1) unsigned DEFAULT NULL,
  `rnd` tinyint(1) unsigned DEFAULT NULL,
  `auf` tinyint(1) NOT NULL,
  `auf_evtl` tinyint(1) NOT NULL,
  `ab` tinyint(1) NOT NULL,
  `ab_evtl` tinyint(1) NOT NULL,
  `published` mediumint(3) unsigned DEFAULT NULL,
  `bemerkungen` text,
  `bem_int` text,
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `b_wertung` tinyint(1) unsigned DEFAULT '0',
  `liga_mt` tinyint(1) unsigned DEFAULT '0',
  `tiebr1` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `tiebr2` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `tiebr3` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `ersatz_regel` tinyint(1) unsigned DEFAULT '0',
  `anzeige_ma` tinyint(1) unsigned DEFAULT '0',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_mannschaften`
--

CREATE TABLE IF NOT EXISTS `#__clm_mannschaften` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `liga` mediumint(5) unsigned DEFAULT NULL,
  `zps` varchar(5) DEFAULT NULL,
  `liste` mediumint(3) NOT NULL DEFAULT '0',
  `edit_liste` mediumint(3) NOT NULL DEFAULT '0',
  `man_nr` mediumint(5) unsigned DEFAULT NULL,
  `tln_nr` mediumint(5) unsigned DEFAULT NULL,
  `mf` mediumint(5) unsigned DEFAULT NULL,
  `sg_zps` varchar(120) NOT NULL,
  `datum` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `edit_datum` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `lokal` text NOT NULL,
  `termine` text,
  `adresse` text,
  `homepage` text,
  `bemerkungen` text NOT NULL,
  `bem_int` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `summanpunkte` decimal(4,1) DEFAULT NULL,
  `sumbrettpunkte` decimal(4,1) DEFAULT NULL,
  `sumwins` tinyint(2) DEFAULT NULL,
  `sumtiebr1` decimal(7,3) DEFAULT '0.000',
  `sumtiebr2` decimal(7,3) DEFAULT '0.000',
  `sumtiebr3` decimal(7,3) DEFAULT '0.000',
  `rankingpos` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sname` varchar(20) DEFAULT '',
  `abzug` tinyint(2) NOT NULL default '0',
  `bpabzug` decimal(3,1) DEFAULT '0.0',
  PRIMARY KEY (`id`),
  KEY `published` (`published`),
  KEY `sid` (`sid`),
  KEY `liga_sid` (`liga`,`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_meldeliste_spieler`
--

CREATE TABLE IF NOT EXISTS `#__clm_meldeliste_spieler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(3) unsigned DEFAULT NULL,
  `lid` mediumint(3) unsigned DEFAULT NULL,
  `mnr` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `snr` mediumint(5) unsigned DEFAULT NULL,
  `mgl_nr` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `PKZ` varchar(9) DEFAULT NULL,
  `zps` varchar(5) NOT NULL DEFAULT '0',
  `status` mediumint(5) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `DWZ` smallint(4) unsigned NOT NULL DEFAULT '0',
  `start_dwz` smallint(4) unsigned default NULL,
  `start_I0` smallint(4) unsigned default NULL,
  `FIDEelo` smallint(4) unsigned DEFAULT NULL,
  `I0` smallint(4) unsigned NOT NULL DEFAULT '0',
  `Punkte` decimal(4,1) unsigned NOT NULL DEFAULT '0.0',
  `Partien` tinyint(3) NOT NULL DEFAULT '0',
  `We` decimal(6,3) NOT NULL DEFAULT '0.000',
  `Leistung` smallint(4) NOT NULL DEFAULT '0',
  `EFaktor` tinyint(2) NOT NULL DEFAULT '0',
  `Niveau` smallint(4) NOT NULL DEFAULT '0',
  `sum_saison` decimal(5,1) NOT NULL DEFAULT '0.0',
  `gesperrt` tinyint(1) unsigned DEFAULT NULL,
  `attr` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lid_zps_mglnr` (`lid`,`zps`,`mgl_nr`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_pgn`
--
CREATE TABLE IF NOT EXISTS `#__clm_pgn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tkz` varchar(1) DEFAULT NULL,
  `tid` smallint(4) unsigned DEFAULT NULL,
  `dg` tinyint(2) unsigned DEFAULT NULL,
  `runde` tinyint(2) unsigned DEFAULT NULL,
  `paar` tinyint(1) unsigned DEFAULT NULL,
  `brett` tinyint(5) unsigned DEFAULT NULL,
  `text` text DEFAULT NULL,
  `error` text DEFAULT NULL,
  PRIMARY KEY `id` (`id`),
  UNIQUE KEY `all` (`tkz`,`tid`,`dg`,`runde`,`paar`,`brett`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_rangliste_id`
--

CREATE TABLE IF NOT EXISTS `#__clm_rangliste_id` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` mediumint(5) NOT NULL,
  `zps` varchar(5) NOT NULL DEFAULT '00000',
  `rang` tinyint(1) NOT NULL,
  `published` mediumint(3) unsigned DEFAULT NULL,
  `bemerkungen` text NOT NULL,
  `bem_int` text NOT NULL,
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_rangliste_name`
--

CREATE TABLE IF NOT EXISTS `#__clm_rangliste_name` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Gruppe` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Meldeschluss` date DEFAULT '2009-06-30',
  `geschlecht` varchar(1) DEFAULT NULL,
  `alter_grenze` varchar(1) DEFAULT NULL,
  `alter` smallint(3) DEFAULT NULL,
  `sid` mediumint(3) unsigned DEFAULT '0',
  `user` mediumint(3) unsigned DEFAULT '0',
  `bemerkungen` text,
  `bem_int` text,
  `checked_out` tinyint(3) unsigned DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_rangliste_spieler`
--

CREATE TABLE IF NOT EXISTS `#__clm_rangliste_spieler` (
  `Gruppe` tinyint(3) unsigned NOT NULL,
  `ZPS` varchar(5) NOT NULL DEFAULT '00000',
  `Mgl_Nr` smallint(5) unsigned NOT NULL DEFAULT '0',
  `PKZ` int(10) unsigned NOT NULL DEFAULT '0',
  `Rang` int(10) unsigned NOT NULL DEFAULT '0',
  `man_nr` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `sid` mediumint(3) unsigned DEFAULT '0',
  PRIMARY KEY (`Gruppe`,`ZPS`,`man_nr`,`Rang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_rnd_man`
--

CREATE TABLE IF NOT EXISTS `#__clm_rnd_man` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) unsigned DEFAULT NULL,
  `lid` mediumint(5) unsigned DEFAULT NULL,
  `runde` mediumint(5) unsigned DEFAULT NULL,
  `paar` mediumint(5) unsigned DEFAULT NULL,
  `dg` tinyint(1) unsigned DEFAULT NULL,
  `heim` tinyint(1) unsigned DEFAULT NULL,
  `tln_nr` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `gegner` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `ergebnis` mediumint(5) unsigned DEFAULT NULL,
  `kampflos` tinyint(1) unsigned DEFAULT NULL,
  `brettpunkte` decimal(5,1) unsigned DEFAULT NULL,
  `manpunkte` mediumint(5) unsigned DEFAULT NULL,
  `bp_sum` decimal(5,1) unsigned DEFAULT NULL,
  `mp_sum` mediumint(5) unsigned DEFAULT NULL,
  `gemeldet` mediumint(5) unsigned DEFAULT NULL,
  `editor` mediumint(5) unsigned DEFAULT NULL,
  `dwz_editor` mediumint(5) unsigned DEFAULT NULL,
  `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `dwz_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `wertpunkte` decimal(5,1) DEFAULT NULL,
  `ko_decision` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `pdate` date NOT NULL DEFAULT '1970-01-01',
  `ptime` time NOT NULL DEFAULT '00:00:00',
  PRIMARY KEY (`id`),
  KEY `published` (`published`),
  KEY `lid_sid` (`lid`,`sid`),
  KEY `lid_dg_runde_paar` (`lid`,`dg`,`runde`,`paar`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_rnd_spl`
--

CREATE TABLE IF NOT EXISTS `#__clm_rnd_spl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) unsigned DEFAULT NULL,
  `lid` mediumint(5) unsigned DEFAULT NULL,
  `runde` mediumint(5) unsigned DEFAULT NULL,
  `paar` mediumint(5) unsigned DEFAULT NULL,
  `dg` tinyint(1) unsigned DEFAULT NULL,
  `tln_nr` mediumint(5) unsigned DEFAULT NULL,
  `brett` mediumint(5) unsigned DEFAULT NULL,
  `heim` tinyint(1) unsigned DEFAULT NULL,
  `weiss` tinyint(1) unsigned DEFAULT NULL,
  `spieler` mediumint(5) unsigned DEFAULT NULL,
  `PKZ` varchar(9) DEFAULT NULL,
  `zps` varchar(5) DEFAULT NULL,
  `gegner` mediumint(5) unsigned DEFAULT NULL,
  `gPKZ` varchar(9) DEFAULT NULL,
  `gzps` varchar(5) DEFAULT NULL,
  `ergebnis` mediumint(5) unsigned DEFAULT NULL,
  `kampflos` tinyint(1) unsigned DEFAULT NULL,
  `punkte` decimal(5,1) unsigned DEFAULT NULL,
  `gemeldet` mediumint(5) unsigned DEFAULT NULL,
  `dwz_edit` mediumint(5) unsigned DEFAULT NULL,
  `dwz_editor` mediumint(5) unsigned DEFAULT NULL,
  `pgnnr` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `lid_zps_spieler` (`lid`,`zps`,`spieler`),
  KEY `lid_dg_runde_paar` (`lid`,`dg`,`runde`,`paar`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_runden_termine`
--

CREATE TABLE IF NOT EXISTS `#__clm_runden_termine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `liga` mediumint(5) unsigned DEFAULT NULL,
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
  `gemeldet` mediumint(3) unsigned DEFAULT NULL,
  `editor` mediumint(3) unsigned DEFAULT NULL,
  `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `enddatum` date NOT NULL DEFAULT '1970-01-01',
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_saison`
--

CREATE TABLE IF NOT EXISTS `#__clm_saison` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `archiv` tinyint(1) NOT NULL DEFAULT '0',
  `bemerkungen` text,
  `bem_int` text,
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `datum` date NOT NULL DEFAULT '1970-01-01',
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_liga`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_liga` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lid` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `sid` mediumint(3) unsigned DEFAULT NULL,
  `teil` mediumint(5) unsigned DEFAULT NULL,
  `stamm` mediumint(5) unsigned DEFAULT NULL,
  `ersatz` mediumint(5) unsigned DEFAULT NULL,
  `rang` tinyint(1) unsigned DEFAULT '0',
  `sl` mediumint(5) unsigned DEFAULT NULL,
  `runden` mediumint(5) unsigned DEFAULT NULL,
  `durchgang` mediumint(5) unsigned DEFAULT NULL,
  `mail` tinyint(1) unsigned DEFAULT NULL,
  `sl_mail` tinyint(1) unsigned DEFAULT NULL,
  `heim` tinyint(1) unsigned DEFAULT NULL,
  `order` tinyint(1) unsigned DEFAULT NULL,
  `rnd` tinyint(1) unsigned DEFAULT NULL,
  `auf` tinyint(1) NOT NULL,
  `auf_evtl` tinyint(1) NOT NULL,
  `ab` tinyint(1) NOT NULL,
  `ab_evtl` tinyint(1) NOT NULL,
  `sieg_bed` tinyint(2) unsigned DEFAULT NULL,
  `runden_modus` tinyint(2) unsigned DEFAULT NULL,
  `man_sieg` decimal(4,2) unsigned DEFAULT '1.00',
  `man_remis` decimal(4,2) unsigned DEFAULT '0.50',
  `man_nieder` decimal(4,2) unsigned DEFAULT '0.00',
  `man_antritt` decimal(4,2) unsigned DEFAULT '0.00',
  `sieg` decimal(2,1) unsigned DEFAULT '1.0',
  `remis` decimal(2,1) unsigned DEFAULT '0.5',
  `nieder` decimal(2,1) unsigned DEFAULT '0.0',
  `antritt` decimal(2,1) unsigned DEFAULT '0.0',
  `published` mediumint(3) unsigned DEFAULT NULL,
  `bemerkungen` text,
  `bem_int` text,
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `b_wertung` tinyint(1) unsigned DEFAULT '0',
  `liga_mt` tinyint(1) unsigned DEFAULT '0',
  `tiebr1` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `tiebr2` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `tiebr3` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_mannschaften`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_mannschaften` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `swt_id` mediumint(5) unsigned DEFAULT NULL,
  `liga` mediumint(5) unsigned DEFAULT NULL,
  `zps` varchar(5) DEFAULT NULL,
  `liste` mediumint(3) NOT NULL DEFAULT '0',
  `edit_liste` mediumint(3) NOT NULL DEFAULT '0',
  `man_nr` mediumint(5) unsigned DEFAULT NULL,
  `tln_nr` mediumint(5) unsigned DEFAULT NULL,
  `mf` mediumint(5) unsigned DEFAULT NULL,
  `sg_zps` varchar(120) NOT NULL,
  `datum` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `edit_datum` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `lokal` text NOT NULL,
  `termine` text,
  `adresse` text,
  `homepage` text,
  `bemerkungen` text NOT NULL,
  `bem_int` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `summanpunkte` decimal(4,1) DEFAULT NULL,
  `sumbrettpunkte` decimal(4,1) DEFAULT NULL,
  `sumwins` tinyint(2) DEFAULT NULL,
  `sumtiebr1` decimal(6,3) DEFAULT '0.000',
  `sumtiebr2` decimal(6,3) DEFAULT '0.000',
  `sumtiebr3` decimal(6,3) DEFAULT '0.000',
  `rankingpos` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid_name_swtid` (`sid`,`name`,`swt_id`),
  KEY `published` (`published`),
  KEY `sid` (`sid`),
  KEY `swt_id` (`swt_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_meldeliste_spieler`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_meldeliste_spieler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `spielerid` smallint(6) unsigned NOT NULL,
  `sid` mediumint(3) unsigned DEFAULT NULL,
  `swt_id` mediumint(5) unsigned DEFAULT NULL,
  `lid` mediumint(5) unsigned DEFAULT NULL,
  `man_id` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `snr` mediumint(5) unsigned DEFAULT NULL,
  `mgl_nr` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `zps` varchar(5) NOT NULL DEFAULT '0',
  `status` mediumint(5) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `DWZ` smallint(4) unsigned NOT NULL DEFAULT '0',
  `I0` smallint(4) unsigned NOT NULL DEFAULT '0',
  `Punkte` decimal(4,1) unsigned NOT NULL DEFAULT '0.0',
  `Partien` tinyint(3) NOT NULL DEFAULT '0',
  `We` decimal(6,3) NOT NULL DEFAULT '0.000',
  `Leistung` smallint(4) NOT NULL DEFAULT '0',
  `EFaktor` tinyint(2) NOT NULL DEFAULT '0',
  `Niveau` smallint(4) NOT NULL DEFAULT '0',
  `sum_saison` decimal(5,1) NOT NULL DEFAULT '0.0',
  `gesperrt` tinyint(1) unsigned DEFAULT NULL,
  `attr` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid_swtid_manid_zps_mglnr` (`sid`,`swt_id`,`man_id`,`zps`,`mgl_nr`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_rnd_man`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_rnd_man` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) unsigned DEFAULT NULL,
  `swt_id` mediumint(5) unsigned DEFAULT NULL,
  `lid` mediumint(5) unsigned DEFAULT NULL,
  `runde` mediumint(5) unsigned DEFAULT NULL,
  `paar` mediumint(5) unsigned DEFAULT NULL,
  `dg` tinyint(1) unsigned DEFAULT NULL,
  `heim` tinyint(1) unsigned DEFAULT NULL,
  `tln_nr` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `gegner` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `ergebnis` mediumint(5) unsigned DEFAULT NULL,
  `kampflos` tinyint(1) unsigned DEFAULT NULL,
  `brettpunkte` decimal(5,1) unsigned DEFAULT NULL,
  `manpunkte` mediumint(5) unsigned DEFAULT NULL,
  `bp_sum` decimal(5,1) unsigned DEFAULT NULL,
  `mp_sum` mediumint(5) unsigned DEFAULT NULL,
  `gemeldet` mediumint(5) unsigned DEFAULT NULL,
  `editor` mediumint(5) unsigned DEFAULT NULL,
  `dwz_editor` mediumint(5) unsigned DEFAULT NULL,
  `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `dwz_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `wertpunkte` decimal(5,1) unsigned DEFAULT NULL,
  `ko_decision` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`),
  KEY `swt_id` (`swt_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_rnd_spl`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_rnd_spl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) unsigned DEFAULT NULL,
  `swt_id` mediumint(5) unsigned DEFAULT NULL,
  `lid` mediumint(5) unsigned DEFAULT NULL,
  `runde` mediumint(5) unsigned DEFAULT NULL,
  `paar` mediumint(5) unsigned DEFAULT NULL,
  `dg` tinyint(1) unsigned DEFAULT NULL,
  `tln_nr` mediumint(5) unsigned DEFAULT NULL,
  `brett` mediumint(5) unsigned DEFAULT NULL,
  `heim` tinyint(1) unsigned DEFAULT NULL,
  `weiss` tinyint(1) unsigned DEFAULT NULL,
  `spieler` mediumint(5) unsigned DEFAULT NULL,
  `zps` varchar(5) DEFAULT NULL,
  `gegner` mediumint(5) unsigned DEFAULT NULL,
  `gzps` varchar(5) DEFAULT NULL,
  `ergebnis` mediumint(5) unsigned DEFAULT NULL,
  `kampflos` tinyint(1) unsigned DEFAULT NULL,
  `punkte` decimal(5,1) unsigned DEFAULT NULL,
  `gemeldet` mediumint(5) unsigned DEFAULT NULL,
  `dwz_edit` mediumint(5) unsigned DEFAULT NULL,
  `dwz_editor` mediumint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_turniere`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_turniere` (
  `swt_tid` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `sid` mediumint(3) unsigned DEFAULT NULL,
  `dateStart` date NOT NULL,
  `dateEnd` date NOT NULL,
  `catidAlltime` smallint(6) unsigned NOT NULL DEFAULT '0',
  `catidEdition` smallint(6) unsigned NOT NULL DEFAULT '0',
  `typ` tinyint(1) unsigned DEFAULT NULL,
  `tiebr1` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `tiebr2` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `tiebr3` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `rnd` tinyint(1) unsigned DEFAULT NULL,
  `teil` mediumint(5) unsigned DEFAULT NULL,
  `runden` mediumint(5) unsigned DEFAULT NULL,
  `dg` mediumint(5) unsigned DEFAULT NULL,
  `tl` mediumint(5) unsigned DEFAULT NULL,
  `bezirk` varchar(8) DEFAULT NULL,
  `bezirkTur` enum('0','1') NOT NULL DEFAULT '1',
  `vereinZPS` varchar(5) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `started` tinyint(1) NOT NULL DEFAULT '0',
  `finished` tinyint(1) NOT NULL DEFAULT '0',
  `invitationText` text,
  `bemerkungen` text,
  `bem_int` text,
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `sieg` decimal(2,1) unsigned DEFAULT '1.0',
  `siegs` decimal(2,1) unsigned DEFAULT '1.0',
  `remis` decimal(2,1) unsigned DEFAULT '0.5',
  `remiss` decimal(2,1) unsigned DEFAULT '0.5',
  `nieder` decimal(2,1) unsigned DEFAULT '0.0',
  `niederk` decimal(2,1) unsigned DEFAULT '0.0',
  PRIMARY KEY (`swt_tid`),
  KEY `published` (`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_turniere_rnd_spl`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_turniere_rnd_spl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) unsigned DEFAULT NULL,
  `turnier` mediumint(5) unsigned DEFAULT NULL,
  `swt_tid` mediumint(5) unsigned DEFAULT NULL,
  `runde` mediumint(5) unsigned DEFAULT NULL,
  `paar` mediumint(5) unsigned DEFAULT NULL,
  `brett` mediumint(5) unsigned DEFAULT NULL,
  `dg` tinyint(1) unsigned DEFAULT NULL,
  `tln_nr` mediumint(5) unsigned DEFAULT NULL,
  `heim` tinyint(1) unsigned DEFAULT NULL,
  `spieler` mediumint(5) unsigned DEFAULT NULL,
  `gegner` mediumint(5) unsigned DEFAULT NULL,
  `ergebnis` mediumint(5) unsigned DEFAULT NULL,
  `tiebrS` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `tiebrG` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `kampflos` tinyint(1) unsigned DEFAULT NULL,
  `pgn` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `turnier_dg_runde_brett_heim` (`swt_tid`,`dg`,`runde`,`brett`,`heim`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_turniere_rnd_termine`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_turniere_rnd_termine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `turnier` mediumint(5) unsigned DEFAULT NULL,
  `swt_tid` mediumint(5) unsigned DEFAULT NULL,
  `dg` tinyint(1) unsigned DEFAULT NULL,
  `nr` mediumint(5) unsigned DEFAULT NULL,
  `datum` date NOT NULL DEFAULT '1970-01-01',
  `startzeit` time NOT NULL DEFAULT '00:00:00',
  `abgeschlossen` mediumint(3) NOT NULL DEFAULT '0',
  `tl_ok` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `bemerkungen` text,
  `bem_int` text,
  `gemeldet` mediumint(3) unsigned DEFAULT NULL,
  `editor` mediumint(3) unsigned DEFAULT NULL,
  `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `turnier_dg_runde` (`swt_tid`,`dg`,`nr`),
  KEY `published` (`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_turniere_tlnr`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_turniere_tlnr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(3) unsigned DEFAULT NULL,
  `turnier` mediumint(4) unsigned DEFAULT NULL,
  `swt_tid` mediumint(4) unsigned DEFAULT NULL,
  `snr` mediumint(5) unsigned DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `birthYear` year(4) NOT NULL DEFAULT '0000',
  `geschlecht` char(1) DEFAULT NULL,
  `verein` varchar(150) DEFAULT NULL,
  `twz` smallint(4) unsigned DEFAULT NULL,
  `start_dwz` smallint(4) unsigned DEFAULT NULL,
  `FIDEelo` smallint(4) unsigned DEFAULT NULL,
  `FIDEid` int(8) unsigned DEFAULT NULL,
  `FIDEcco` char(3) DEFAULT NULL,
  `titel` char(3) DEFAULT NULL,
  `mgl_nr` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `PKZ` varchar(9) DEFAULT NULL,
  `zps` varchar(5) NOT NULL DEFAULT '0',
  `status` mediumint(5) NOT NULL DEFAULT '0',
  `rankingPos` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tlnrStatus` tinyint(1) unsigned NOT NULL,
  `s_punkte` decimal(3,1) DEFAULT '0.0',
  `sum_punkte` decimal(4,1) DEFAULT NULL,
  `sum_bhlz` decimal(5,2) DEFAULT NULL,
  `sum_busum` decimal(6,2) DEFAULT NULL,
  `sum_sobe` decimal(5,2) DEFAULT NULL,
  `sum_wins` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sumTiebr1` decimal(6,3) NOT NULL DEFAULT '0.000',
  `sumTiebr2` decimal(6,3) NOT NULL DEFAULT '0.000',
  `sumTiebr3` decimal(6,3) NOT NULL DEFAULT '0.000',
  `koStatus` enum('0','1') NOT NULL DEFAULT '1',
  `koRound` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `DWZ` smallint(4) unsigned NOT NULL DEFAULT '0',
  `I0` smallint(4) unsigned NOT NULL DEFAULT '0',
  `Punkte` decimal(4,1) unsigned NOT NULL DEFAULT '0.0',
  `Partien` tinyint(3) NOT NULL DEFAULT '0',
  `We` decimal(6,3) NOT NULL DEFAULT '0.000',
  `Leistung` smallint(4) NOT NULL DEFAULT '0',
  `EFaktor` tinyint(2) NOT NULL DEFAULT '0',
  `Niveau` smallint(4) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`zps`,`mgl_nr`,`status`),
  UNIQUE KEY `turnier_snr` (`swt_tid`,`snr`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_termine`
--

CREATE TABLE IF NOT EXISTS `#__clm_termine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `beschreibung` text,
  `address` varchar(100) NOT NULL DEFAULT '',
  `catidAlltime` smallint(6) unsigned NOT NULL DEFAULT '0',
  `catidEdition` smallint(6) unsigned NOT NULL DEFAULT '0',
  `category` varchar(33) NOT NULL DEFAULT '',
  `host` varchar(5) DEFAULT NULL,
  `startdate` date NOT NULL DEFAULT '1970-01-01',
  `starttime` time NOT NULL DEFAULT '00:00:00',
  `allday` tinyint(3) NOT NULL DEFAULT '0',
  `enddate` date NOT NULL DEFAULT '1970-01-01',
  `endtime` time NOT NULL DEFAULT '00:00:00',
  `noendtime` tinyint(3) NOT NULL DEFAULT '0',
  `attached_file` varchar(256) DEFAULT '',
  `attached_file_description` varchar(128) DEFAULT '',
  `published` mediumint(3) unsigned DEFAULT NULL,
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `event_link` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_turniere`
--

CREATE TABLE IF NOT EXISTS `#__clm_turniere` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `sid` mediumint(3) unsigned DEFAULT NULL,
  `dateStart` date NOT NULL,
  `dateEnd` date NOT NULL,
  `catidAlltime` smallint(6) unsigned NOT NULL DEFAULT '0',
  `catidEdition` smallint(6) unsigned NOT NULL DEFAULT '0',
  `typ` tinyint(1) unsigned DEFAULT NULL,
  `tiebr1` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `tiebr2` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `tiebr3` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `rnd` tinyint(1) unsigned DEFAULT NULL,
  `teil` mediumint(5) unsigned DEFAULT NULL,
  `runden` mediumint(5) unsigned DEFAULT NULL,
  `dg` mediumint(5) unsigned DEFAULT NULL,
  `tl` mediumint(5) unsigned DEFAULT NULL,
  `bezirk` varchar(8) DEFAULT NULL,
  `bezirkTur` enum('0','1') NOT NULL DEFAULT '1',
  `vereinZPS` varchar(5) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `started` tinyint(1) NOT NULL DEFAULT '0',
  `finished` tinyint(1) NOT NULL DEFAULT '0',
  `invitationText` text,
  `bemerkungen` text,
  `bem_int` text,
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `sieg` decimal(2,1) unsigned DEFAULT '1.0',
  `siegs` decimal(2,1) unsigned DEFAULT '1.0',
  `remis` decimal(2,1) unsigned DEFAULT '0.5',
  `remiss` decimal(2,1) unsigned DEFAULT '0.5',
  `nieder` decimal(2,1) unsigned DEFAULT '0.0',
  `niederk` decimal(2,1) unsigned DEFAULT '0.0',
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_turniere_rnd_spl`
--

CREATE TABLE IF NOT EXISTS `#__clm_turniere_rnd_spl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) unsigned DEFAULT NULL,
  `turnier` mediumint(5) unsigned DEFAULT NULL,
  `runde` mediumint(5) unsigned DEFAULT NULL,
  `paar` mediumint(5) unsigned DEFAULT NULL,
  `brett` mediumint(5) unsigned DEFAULT NULL,
  `dg` tinyint(1) unsigned DEFAULT NULL,
  `tln_nr` mediumint(5) unsigned DEFAULT NULL,
  `heim` tinyint(1) unsigned DEFAULT NULL,
  `spieler` mediumint(5) unsigned DEFAULT NULL,
  `gegner` mediumint(5) unsigned DEFAULT NULL,
  `ergebnis` mediumint(5) unsigned DEFAULT NULL,
  `tiebrS` decimal(2,1) unsigned NOT NULL DEFAULT '0.0',
  `tiebrG` decimal(2,1) unsigned NOT NULL DEFAULT '0.0',
  `kampflos` tinyint(1) unsigned DEFAULT NULL,
  `gemeldet` mediumint(3) unsigned DEFAULT NULL,
  `pgn` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_turniere_rnd_termine`
--

CREATE TABLE IF NOT EXISTS `#__clm_turniere_rnd_termine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `turnier` mediumint(5) unsigned DEFAULT NULL,
  `dg` tinyint(1) unsigned DEFAULT NULL,
  `nr` mediumint(5) unsigned DEFAULT NULL,
  `datum` date NOT NULL DEFAULT '1970-01-01',
  `startzeit` time NOT NULL DEFAULT '00:00:00',
  `abgeschlossen` mediumint(3) NOT NULL DEFAULT '0',
  `tl_ok` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `bemerkungen` text,
  `bem_int` text,
  `gemeldet` mediumint(3) unsigned DEFAULT NULL,
  `editor` mediumint(3) unsigned DEFAULT NULL,
  `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_turniere_sonderranglisten`
--

CREATE TABLE IF NOT EXISTS `#__clm_turniere_sonderranglisten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `turnier` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `use_rating_filter` enum('0','1') DEFAULT '0',
  `rating_type` tinyint(1) DEFAULT '0',
  `rating_higher_than` smallint(4) DEFAULT '0',
  `rating_lower_than` smallint(4) DEFAULT '3000',
  `use_birthYear_filter` enum('0','1') DEFAULT '0',
  `birthYear_younger_than` year(4) DEFAULT NULL,
  `birthYear_older_than` year(4) DEFAULT NULL,
  `use_sex_filter` enum('0','1') DEFAULT '0',
  `sex` enum('','M','W') DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` tinyint(3) unsigned NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `use_zps_filter` enum('0','1') DEFAULT '0',
  `zps_higher_than` varchar(5) DEFAULT '',
  `zps_lower_than` varchar(5) DEFAULT 'ZZZZZ',
  `use_sex_year_filter` enum('0','1') default '0',
  `maleYear_younger_than` year(4) default NULL,
  `maleYear_older_than` year(4) default NULL,
  `femaleYear_younger_than` year(4) default NULL,
  `femaleYear_older_than` year(4) default NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_turniere_tlnr`
--

CREATE TABLE IF NOT EXISTS `#__clm_turniere_tlnr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(3) unsigned DEFAULT NULL,
  `turnier` mediumint(4) unsigned DEFAULT NULL,
  `snr` mediumint(5) unsigned DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `birthYear` year(4) NOT NULL DEFAULT '0000',
  `geschlecht` char(1) DEFAULT NULL,
  `verein` varchar(150) DEFAULT NULL,
  `twz` smallint(4) unsigned DEFAULT NULL,
  `start_dwz` smallint(4) unsigned DEFAULT NULL,
  `start_I0` smallint(4) unsigned default NULL,
  `FIDEelo` smallint(4) unsigned DEFAULT NULL,
  `FIDEid` int(8) unsigned DEFAULT NULL,
  `FIDEcco` char(3) DEFAULT NULL,
  `titel` char(3) DEFAULT NULL,
  `mgl_nr` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `PKZ` varchar(9) DEFAULT NULL,
  `zps` varchar(5) NOT NULL DEFAULT '0',
  `status` mediumint(5) NOT NULL DEFAULT '0',
  `rankingPos` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tlnrStatus` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `s_punkte` decimal(3,1) DEFAULT '0.0',
  `anz_spiele` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `sum_punkte` decimal(4,1) DEFAULT NULL,
  `sum_bhlz` decimal(5,2) DEFAULT NULL,
  `sum_busum` decimal(6,2) DEFAULT NULL,
  `sum_sobe` decimal(5,2) DEFAULT NULL,
  `koStatus` enum('0','1') NOT NULL DEFAULT '1',
  `koRound` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sum_wins` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `sumTiebr1` decimal(8,3) DEFAULT NULL,
  `sumTiebr2` decimal(8,3) DEFAULT NULL,
  `sumTiebr3` decimal(8,3) DEFAULT NULL,
  `DWZ` smallint(4) unsigned NOT NULL DEFAULT '0',
  `I0` smallint(4) unsigned NOT NULL DEFAULT '0',
  `Punkte` decimal(4,1) unsigned NOT NULL DEFAULT '0.0',
  `Partien` tinyint(3) NOT NULL DEFAULT '0',
  `We` decimal(6,3) NOT NULL DEFAULT '0.000',
  `Leistung` smallint(4) NOT NULL DEFAULT '0',
  `EFaktor` tinyint(2) NOT NULL DEFAULT '0',
  `Niveau` smallint(4) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`zps`,`mgl_nr`,`status`),
  KEY `turnier_snr` (`turnier`,`snr`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_user`
--

CREATE TABLE IF NOT EXISTS `#__clm_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` smallint(3) unsigned DEFAULT NULL,
  `jid` mediumint(5) unsigned DEFAULT NULL,
  `name` text NOT NULL,
  `username` varchar(150) NOT NULL DEFAULT '',
  `aktive` tinyint(3) NOT NULL DEFAULT '0',
  `email` varchar(100) NOT NULL DEFAULT '',
  `tel_fest` varchar(30) NOT NULL DEFAULT '',
  `tel_mobil` varchar(30) NOT NULL DEFAULT '',
  `usertype` varchar(75) NOT NULL DEFAULT '',
  `zps` varchar(5) DEFAULT NULL,
  `mglnr` varchar(5) DEFAULT NULL,
  `PKZ` varchar(9) DEFAULT NULL,
  `org_exc` enum('0','1') NOT NULL DEFAULT '0',
  `mannschaft` smallint(3) unsigned DEFAULT NULL,
  `published` smallint(3) unsigned DEFAULT NULL,
  `bemerkungen` text NOT NULL,
  `bem_int` text NOT NULL,
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `block` tinyint(4) NOT NULL DEFAULT '0',
  `activation` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid_jid` (`sid`,`jid`),
  KEY `published` (`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_usertype`
--

CREATE TABLE IF NOT EXISTS `#__clm_usertype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `usertype` varchar(15) DEFAULT '0',
  `kind` varchar(4) NOT NULL DEFAULT 'USER',
  `published` int(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usertype` (`usertype`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_vereine`
--

CREATE TABLE IF NOT EXISTS `#__clm_vereine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `sid` mediumint(5) unsigned DEFAULT NULL,
  `zps` varchar(5) DEFAULT NULL,
  `vl` mediumint(5) unsigned DEFAULT NULL,
  `lokal` varchar(200) NOT NULL DEFAULT '',
  `homepage` varchar(200) NOT NULL DEFAULT '',
  `adresse` varchar(200) NOT NULL DEFAULT '',
  `vs` varchar(200) NOT NULL DEFAULT '',
  `vs_mail` varchar(200) NOT NULL DEFAULT '',
  `vs_tel` varchar(200) NOT NULL DEFAULT '',
  `tl` varchar(200) NOT NULL DEFAULT '',
  `tl_mail` varchar(200) NOT NULL DEFAULT '',
  `tl_tel` varchar(200) NOT NULL DEFAULT '',
  `jw` varchar(200) NOT NULL DEFAULT '',
  `jw_mail` varchar(200) NOT NULL DEFAULT '',
  `jw_tel` varchar(200) NOT NULL DEFAULT '',
  `pw` varchar(200) NOT NULL DEFAULT '',
  `pw_mail` varchar(200) NOT NULL DEFAULT '',
  `pw_tel` varchar(200) NOT NULL DEFAULT '',
  `kw` varchar(200) NOT NULL DEFAULT '',
  `kw_mail` varchar(200) NOT NULL DEFAULT '',
  `kw_tel` varchar(200) NOT NULL DEFAULT '',
  `sw` varchar(200) NOT NULL DEFAULT '',
  `sw_mail` varchar(200) NOT NULL DEFAULT '',
  `sw_tel` varchar(200) NOT NULL DEFAULT '',
  `termine` varchar(200) NOT NULL DEFAULT '',
  `published` mediumint(3) unsigned DEFAULT NULL,
  `bemerkungen` text NOT NULL,
  `bem_int` text NOT NULL,
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_logging`
--

CREATE TABLE IF NOT EXISTS `#__clm_logging` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `callid` varchar(13) NOT NULL,
  `userid` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `name` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
