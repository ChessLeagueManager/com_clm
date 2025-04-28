--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2024 CLM Team.  All rights reserved
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
-- Datenbank: `clm`  Change status: 10.02.2021
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_categories`
--

CREATE TABLE IF NOT EXISTS `#__clm_categories` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `parentid` smallint(6) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `sid` mediumint(3) UNSIGNED DEFAULT NULL,
  `dateStart` date NOT NULL,
  `dateEnd` date NOT NULL,
  `tl` mediumint(5) UNSIGNED DEFAULT NULL,
  `bezirk` varchar(8) DEFAULT NULL,
  `bezirkTur` enum('0','1') NOT NULL DEFAULT '1',
  `vereinZPS` varchar(5) DEFAULT NULL,
  `published` mediumint(3) UNSIGNED DEFAULT NULL,
  `started` tinyint(1) NOT NULL DEFAULT 0,
  `finished` tinyint(1) NOT NULL DEFAULT 0,
  `invitationText` text DEFAULT NULL,
  `bemerkungen` text DEFAULT NULL,
  `bem_int` text DEFAULT NULL,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_config`
--

CREATE TABLE IF NOT EXISTS `#__clm_config` (
  `id` int(11) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_dwz_spieler`
--

CREATE TABLE IF NOT EXISTS `#__clm_dwz_spieler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(6) UNSIGNED DEFAULT NULL,
  `PKZ` varchar(9) DEFAULT NULL,
  `ZPS` varchar(5) NOT NULL DEFAULT '',
  `Mgl_Nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `Status` char(1) DEFAULT NULL,
  `Spielername` varchar(50) NOT NULL DEFAULT '',
  `Spielername_G` varchar(50) NOT NULL DEFAULT '',
  `Geschlecht` char(1) DEFAULT NULL,
  `Spielberechtigung` char(1) NOT NULL DEFAULT '',
  `Geburtsjahr` year(4) NOT NULL DEFAULT 0000,
  `Junior` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `Letzte_Auswertung` mediumint(6) UNSIGNED DEFAULT NULL,
  `DWZ` smallint(4) UNSIGNED DEFAULT NULL,
  `DWZ_Index` smallint(3) UNSIGNED DEFAULT NULL,
  `FIDE_Elo` smallint(4) UNSIGNED DEFAULT NULL,
  `FIDE_Titel` char(3) DEFAULT NULL,
  `FIDE_ID` int(8) UNSIGNED DEFAULT NULL,
  `FIDE_Land` char(3) DEFAULT NULL,
  `DWZ_neu` smallint(4) UNSIGNED NOT NULL DEFAULT 0,
  `I0` smallint(4) UNSIGNED NOT NULL DEFAULT 0,
  `Punkte` decimal(4,1) UNSIGNED NOT NULL DEFAULT 0.0,
  `Partien` tinyint(3) NOT NULL DEFAULT 0,
  `We` decimal(6,3) NOT NULL DEFAULT 0.000,
  `Leistung` smallint(4) NOT NULL DEFAULT 0,
  `EFaktor` tinyint(2) NOT NULL DEFAULT 0,
  `Niveau` smallint(4) NOT NULL DEFAULT 0,
  `joiningdate` date NOT NULL DEFAULT '1970-01-01',
  `leavingdate` date NOT NULL DEFAULT '1970-01-01',
  `synflag` tinyint(1) NOT NULL DEFAULT 0,
  `gesperrt` tinyint(1) UNSIGNED DEFAULT NULL,
  `inofFIDEelo` smallint(4) UNSIGNED DEFAULT NULL,
  `K` smallint(4) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid_zps_mglnr` (`sid`,`ZPS`,`Mgl_Nr`),
  KEY `sid` (`sid`),
  KEY `ZPS` (`ZPS`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_dwz_vereine`
--

CREATE TABLE IF NOT EXISTS `#__clm_dwz_vereine` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sid` mediumint(6) UNSIGNED DEFAULT NULL,
  `ZPS` varchar(5) NOT NULL DEFAULT '',
  `LV` char(1) NOT NULL DEFAULT '',
  `Verband` varchar(4) NOT NULL DEFAULT '',
  `Vereinname` varchar(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid_ZPS` (`sid`,`ZPS`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_ergebnis`
--

CREATE TABLE IF NOT EXISTS `#__clm_ergebnis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eid` mediumint(5) UNSIGNED DEFAULT NULL,
  `erg_text` varchar(10) NOT NULL DEFAULT '',
  `dsb_w` char(1) NOT NULL DEFAULT '',
  `dsb_s` char(1) NOT NULL DEFAULT '',
  `xml_w` varchar(3) NOT NULL DEFAULT '',
  `xml_s` varchar(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_liga`
--

CREATE TABLE IF NOT EXISTS `#__clm_liga` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `sid` mediumint(3) UNSIGNED DEFAULT NULL,
  `catidAlltime` smallint(6) UNSIGNED NOT NULL DEFAULT 0,
  `catidEdition` smallint(6) UNSIGNED NOT NULL DEFAULT 0,
  `teil` mediumint(5) UNSIGNED DEFAULT NULL,
  `stamm` mediumint(5) UNSIGNED DEFAULT NULL,
  `ersatz` mediumint(5) UNSIGNED DEFAULT NULL,
  `rang` tinyint(1) UNSIGNED DEFAULT 0,
  `sl` int(11) UNSIGNED DEFAULT NULL,
  `runden` mediumint(5) UNSIGNED DEFAULT NULL,
  `durchgang` mediumint(5) UNSIGNED DEFAULT NULL,
  `mail` tinyint(1) UNSIGNED DEFAULT NULL,
  `sl_mail` tinyint(1) UNSIGNED DEFAULT NULL,
  `heim` tinyint(1) UNSIGNED DEFAULT NULL,
  `sieg_bed` tinyint(2) UNSIGNED DEFAULT NULL,
  `runden_modus` tinyint(2) UNSIGNED DEFAULT NULL,
  `man_sieg` decimal(4,2) UNSIGNED DEFAULT 2.00,
  `man_remis` decimal(4,2) UNSIGNED DEFAULT 1.00,
  `man_nieder` decimal(4,2) UNSIGNED DEFAULT 0.00,
  `man_antritt` decimal(4,2) UNSIGNED DEFAULT 0.00,
  `sieg` decimal(2,1) UNSIGNED DEFAULT 1.0,
  `remis` decimal(2,1) UNSIGNED DEFAULT 0.5,
  `nieder` decimal(2,1) UNSIGNED DEFAULT 0.0,
  `antritt` decimal(2,1) UNSIGNED DEFAULT 0.0,
  `order` tinyint(1) UNSIGNED DEFAULT NULL,
  `rnd` tinyint(1) UNSIGNED DEFAULT NULL,
  `auf` tinyint(1) UNSIGNED DEFAULT 0,
  `auf_evtl` tinyint(1) UNSIGNED DEFAULT 0,
  `ab` tinyint(1) UNSIGNED DEFAULT 0,
  `ab_evtl` tinyint(1) UNSIGNED DEFAULT 0,
  `published` mediumint(3) UNSIGNED DEFAULT NULL,
  `bemerkungen` text DEFAULT NULL,
  `bem_int` text DEFAULT NULL,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `b_wertung` tinyint(1) UNSIGNED DEFAULT 0,
  `liga_mt` tinyint(1) UNSIGNED DEFAULT 0,
  `tiebr1` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `tiebr2` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `tiebr3` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `ersatz_regel` tinyint(1) UNSIGNED DEFAULT 0,
  `anzeige_ma` tinyint(1) UNSIGNED DEFAULT 0,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__clm_logging`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_mannschaften`
--

CREATE TABLE IF NOT EXISTS `#__clm_mannschaften` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(100) NOT NULL DEFAULT '',
  `liga` mediumint(5) UNSIGNED DEFAULT NULL,
  `zps` varchar(5) DEFAULT NULL,
  `liste` int(11) NOT NULL DEFAULT 0,
  `edit_liste` int(11) NOT NULL DEFAULT 0,
  `man_nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `tln_nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `mf` int(11) UNSIGNED DEFAULT NULL,
  `sg_zps` varchar(120) DEFAULT NULL,
  `datum` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `edit_datum` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `lokal` text DEFAULT NULL,
  `lokal_coord` text DEFAULT NULL,
  `termine` text DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `homepage` text DEFAULT NULL,
  `bemerkungen` text DEFAULT NULL,
  `bem_int` text DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `summanpunkte` decimal(4,1) DEFAULT NULL,
  `sumbrettpunkte` decimal(4,1) DEFAULT NULL,
  `sumwins` tinyint(2) DEFAULT NULL,
  `sumtiebr1` decimal(7,3) DEFAULT 0.000,
  `sumtiebr2` decimal(7,3) DEFAULT 0.000,
  `sumtiebr3` decimal(7,3) DEFAULT 0.000,
  `rankingpos` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `sname` varchar(20) DEFAULT '',
  `abzug` tinyint(2) NOT NULL DEFAULT 0,
  `bpabzug` decimal(3,1) DEFAULT 0.0,
  `z_summanpunkte` decimal(4,1) DEFAULT NULL,
  `z_sumbrettpunkte` decimal(4,1) DEFAULT NULL,
  `z_sumwins` tinyint(2) DEFAULT NULL,
  `z_sumtiebr1` decimal(7,3) DEFAULT 0.000,
  `z_sumtiebr2` decimal(7,3) DEFAULT 0.000,
  `z_sumtiebr3` decimal(7,3) DEFAULT 0.000,
  `z_rankingpos` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `published` (`published`),
  KEY `sid` (`sid`),
  KEY `liga_sid` (`liga`,`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_meldeliste_spieler`
--

CREATE TABLE IF NOT EXISTS `#__clm_meldeliste_spieler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(3) UNSIGNED DEFAULT NULL,
  `lid` mediumint(3) UNSIGNED DEFAULT NULL,
  `mnr` mediumint(5) UNSIGNED NOT NULL DEFAULT 0,
  `snr` mediumint(5) UNSIGNED DEFAULT NULL,
  `mgl_nr` mediumint(5) UNSIGNED NOT NULL DEFAULT 0,
  `PKZ` varchar(9) DEFAULT NULL,
  `zps` varchar(5) NOT NULL DEFAULT '0',
  `status` mediumint(5) NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `DWZ` smallint(4) UNSIGNED NOT NULL DEFAULT 0,
  `start_dwz` smallint(4) UNSIGNED DEFAULT NULL,
  `start_I0` smallint(4) UNSIGNED DEFAULT NULL,
  `FIDEelo` smallint(4) UNSIGNED DEFAULT NULL,
  `I0` smallint(4) UNSIGNED NOT NULL DEFAULT 0,
  `Punkte` decimal(4,1) UNSIGNED NOT NULL DEFAULT 0.0,
  `Partien` tinyint(3) NOT NULL DEFAULT 0,
  `We` decimal(6,3) NOT NULL DEFAULT 0.000,
  `Leistung` smallint(4) NOT NULL DEFAULT 0,
  `EFaktor` tinyint(2) NOT NULL DEFAULT 0,
  `Niveau` smallint(4) NOT NULL DEFAULT 0,
  `sum_saison` decimal(5,1) NOT NULL DEFAULT 0.0,
  `gesperrt` tinyint(1) UNSIGNED DEFAULT NULL,
  `attr` varchar(4) DEFAULT NULL,
  `inofFIDEelo` smallint(4) UNSIGNED DEFAULT NULL,
  `K` smallint(4) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lid_zps_mglnr` (`lid`,`zps`,`mgl_nr`),
  KEY `lid` (`lid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_online_registration`
--

CREATE TABLE IF NOT EXISTS `#__clm_online_registration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` mediumint(5) UNSIGNED DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `vorname` varchar(50) DEFAULT NULL,
  `birthYear` year(4) NOT NULL DEFAULT 0000,
  `geschlecht` char(1) DEFAULT NULL,
  `club` varchar(60) DEFAULT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `elo` smallint(4) UNSIGNED DEFAULT NULL,
  `FIDEid` int(8) DEFAULT NULL,
  `FIDEcco` char(3) DEFAULT NULL,
  `dwz` smallint(4) UNSIGNED DEFAULT NULL,
  `dwz_I0` smallint(6) NOT NULL DEFAULT 0,
  `titel` char(3) DEFAULT NULL,
  `mgl_nr` mediumint(5) NOT NULL DEFAULT 0,
  `PKZ` varchar(9) DEFAULT NULL,
  `zps` varchar(5) NOT NULL DEFAULT '',
  `tel_no` varchar(30) NOT NULL DEFAULT '',
  `account` varchar(50) NOT NULL DEFAULT '',
  `status` mediumint(5) NOT NULL DEFAULT 0,
  `timestamp` int(11) NOT NULL,
  `comment` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `pid` varchar(32) NOT NULL DEFAULT '',
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_pgn`
--

CREATE TABLE IF NOT EXISTS `#__clm_pgn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tkz` varchar(1) DEFAULT NULL,
  `tid` smallint(4) UNSIGNED DEFAULT NULL,
  `dg` tinyint(2) UNSIGNED DEFAULT NULL,
  `runde` tinyint(2) UNSIGNED DEFAULT NULL,
  `paar` tinyint(1) UNSIGNED DEFAULT NULL,
  `brett` smallint(8) UNSIGNED DEFAULT NULL,
  `text` text DEFAULT NULL,
  `error` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `all` (`tkz`,`tid`,`dg`,`runde`,`paar`,`brett`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_player_decode`
--

CREATE TABLE IF NOT EXISTS `#__clm_player_decode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(6) UNSIGNED DEFAULT NULL,
  `source` varchar(20) DEFAULT NULL,
  `oname` varchar(50) DEFAULT NULL,
  `nname` varchar(150) DEFAULT NULL,
  `verein` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid_source_oname` (`sid`,`source`,`oname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_rangliste_id`
--

CREATE TABLE IF NOT EXISTS `#__clm_rangliste_id` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `gid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sid` mediumint(5) NOT NULL,
  `zps` varchar(5) NOT NULL DEFAULT '00000',
  `sg_zps` varchar(120) NOT NULL DEFAULT '00000',
  `rang` tinyint(1) NOT NULL,
  `published` mediumint(3) UNSIGNED DEFAULT NULL,
  `bemerkungen` text NOT NULL,
  `bem_int` text NOT NULL,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `gid_sid_zps` (`gid`,`sid`,`zps`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_rangliste_name`
--

CREATE TABLE IF NOT EXISTS `#__clm_rangliste_name` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Gruppe` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Meldeschluss` date DEFAULT '2009-06-30',
  `geschlecht` varchar(1) DEFAULT NULL,
  `alter_grenze` varchar(1) DEFAULT NULL,
  `alter` smallint(3) DEFAULT NULL,
  `status` varchar(3) NOT NULL DEFAULT '',
  `sid` mediumint(3) UNSIGNED DEFAULT 0,
  `user` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `bemerkungen` text DEFAULT NULL,
  `bem_int` text DEFAULT NULL,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `anz_sgp` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_rangliste_spieler`
--

CREATE TABLE IF NOT EXISTS `#__clm_rangliste_spieler` (
  `Gruppe` tinyint(3) UNSIGNED NOT NULL,
  `ZPS` varchar(5) NOT NULL DEFAULT '00000',
  `ZPSmgl` varchar(5) NOT NULL DEFAULT '00000',
  `Mgl_Nr` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `PKZ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `Rang` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `man_nr` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `sid` mediumint(3) UNSIGNED DEFAULT 0,
  `gesperrt` tinyint(1) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`Gruppe`,`ZPS`,`man_nr`,`Rang`),
  KEY `sid_ZPS_mannr_mglnr` (`sid`,`ZPS`,`man_nr`,`Mgl_Nr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_rnd_man`
--

CREATE TABLE IF NOT EXISTS `#__clm_rnd_man` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) UNSIGNED DEFAULT NULL,
  `lid` mediumint(5) UNSIGNED DEFAULT NULL,
  `runde` mediumint(5) UNSIGNED DEFAULT NULL,
  `paar` mediumint(5) UNSIGNED DEFAULT NULL,
  `dg` tinyint(1) UNSIGNED DEFAULT NULL,
  `heim` tinyint(1) UNSIGNED DEFAULT NULL,
  `tln_nr` mediumint(5) UNSIGNED NOT NULL DEFAULT 0,
  `gegner` mediumint(5) UNSIGNED NOT NULL DEFAULT 0,
  `ergebnis` mediumint(5) UNSIGNED DEFAULT NULL,
  `kampflos` tinyint(1) UNSIGNED DEFAULT NULL,
  `brettpunkte` decimal(5,1) UNSIGNED DEFAULT NULL,
  `manpunkte` mediumint(5) UNSIGNED DEFAULT NULL,
  `bp_sum` decimal(5,1) UNSIGNED DEFAULT NULL,
  `mp_sum` mediumint(5) UNSIGNED DEFAULT NULL,
  `gemeldet` int(11) UNSIGNED DEFAULT NULL,
  `editor` int(11) UNSIGNED DEFAULT NULL,
  `dwz_editor` int(11) UNSIGNED DEFAULT NULL,
  `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `dwz_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `wertpunkte` decimal(5,1) DEFAULT NULL,
  `ko_decision` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `icomment` text NOT NULL,
  `pdate` date NOT NULL DEFAULT '1970-01-01',
  `ptime` time NOT NULL DEFAULT '00:00:00',
  PRIMARY KEY (`id`),
  KEY `published` (`published`),
  KEY `lid_sid` (`lid`,`sid`),
  KEY `lid_dg_runde_paar` (`lid`,`dg`,`runde`,`paar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_rnd_spl`
--

CREATE TABLE IF NOT EXISTS `#__clm_rnd_spl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) UNSIGNED DEFAULT NULL,
  `lid` mediumint(5) UNSIGNED DEFAULT NULL,
  `runde` mediumint(5) UNSIGNED DEFAULT NULL,
  `paar` mediumint(5) UNSIGNED DEFAULT NULL,
  `dg` tinyint(1) UNSIGNED DEFAULT NULL,
  `tln_nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `brett` mediumint(5) UNSIGNED DEFAULT NULL,
  `heim` tinyint(1) UNSIGNED DEFAULT NULL,
  `weiss` tinyint(1) UNSIGNED DEFAULT NULL,
  `spieler` mediumint(5) UNSIGNED DEFAULT NULL,
  `PKZ` varchar(9) DEFAULT NULL,
  `zps` varchar(5) DEFAULT NULL,
  `gegner` mediumint(5) UNSIGNED DEFAULT NULL,
  `gPKZ` varchar(9) DEFAULT NULL,
  `gzps` varchar(5) DEFAULT NULL,
  `ergebnis` mediumint(5) UNSIGNED DEFAULT NULL,
  `kampflos` tinyint(1) UNSIGNED DEFAULT NULL,
  `punkte` decimal(5,1) UNSIGNED DEFAULT NULL,
  `gemeldet` int(11) UNSIGNED DEFAULT NULL,
  `dwz_edit` mediumint(5) UNSIGNED DEFAULT NULL,
  `dwz_editor` int(11) UNSIGNED DEFAULT NULL,
  `pgnnr` int(11) UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `lid_zps_spieler` (`lid`,`zps`,`spieler`),
  KEY `lid_dg_runde_paar` (`lid`,`dg`,`runde`,`paar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_runden_termine`
--

CREATE TABLE IF NOT EXISTS `#__clm_runden_termine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `liga` mediumint(5) UNSIGNED DEFAULT NULL,
  `nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `datum` date NOT NULL DEFAULT '1970-01-01',
  `startzeit` time NOT NULL DEFAULT '00:00:00',
  `deadlineday` date NOT NULL DEFAULT '1970-01-01',
  `deadlinetime` time NOT NULL DEFAULT '00:00:00',
  `meldung` tinyint(1) NOT NULL DEFAULT 0,
  `sl_ok` tinyint(1) NOT NULL DEFAULT 0,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `bemerkungen` text DEFAULT NULL,
  `bem_int` text DEFAULT NULL,
  `gemeldet` int(11) UNSIGNED DEFAULT NULL,
  `editor` int(11) UNSIGNED DEFAULT NULL,
  `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `enddatum` date NOT NULL DEFAULT '1970-01-01',
  PRIMARY KEY (`id`),
  KEY `published` (`published`),
  KEY `liga` (`liga`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_saison`
--

CREATE TABLE IF NOT EXISTS `#__clm_saison` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `archiv` tinyint(1) NOT NULL DEFAULT 0,
  `bemerkungen` text DEFAULT NULL,
  `bem_int` text DEFAULT NULL,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `datum` date NOT NULL DEFAULT '1970-01-01',
  `rating_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `published` (`published`),
  KEY `archiv` (`archiv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_dwz_spieler`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_dwz_spieler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(6) UNSIGNED DEFAULT NULL,
  `PKZ` varchar(9) DEFAULT NULL,
  `ZPS` varchar(5) NOT NULL DEFAULT '',
  `Mgl_Nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `Status` char(1) DEFAULT NULL,
  `Spielername` varchar(50) NOT NULL DEFAULT '',
  `Spielername_G` varchar(50) NOT NULL DEFAULT '',
  `Geschlecht` char(1) DEFAULT NULL,
  `Spielberechtigung` char(1) NOT NULL DEFAULT '',
  `Geburtsjahr` year(4) NOT NULL DEFAULT 0000,
  `Junior` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `Letzte_Auswertung` mediumint(6) UNSIGNED DEFAULT NULL,
  `DWZ` smallint(4) UNSIGNED DEFAULT NULL,
  `DWZ_Index` smallint(3) UNSIGNED DEFAULT NULL,
  `FIDE_Elo` smallint(4) UNSIGNED DEFAULT NULL,
  `FIDE_Titel` char(3) DEFAULT NULL,
  `FIDE_ID` int(8) UNSIGNED DEFAULT NULL,
  `FIDE_Land` char(3) DEFAULT NULL,
  `DWZ_neu` smallint(4) UNSIGNED NOT NULL DEFAULT 0,
  `I0` smallint(4) UNSIGNED NOT NULL DEFAULT 0,
  `Punkte` decimal(4,1) UNSIGNED NOT NULL DEFAULT 0.0,
  `Partien` tinyint(3) NOT NULL DEFAULT 0,
  `We` decimal(6,3) NOT NULL DEFAULT 0.000,
  `Leistung` smallint(4) NOT NULL DEFAULT 0,
  `EFaktor` tinyint(2) NOT NULL DEFAULT 0,
  `Niveau` smallint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid_zps_mglnr` (`sid`,`ZPS`,`Mgl_Nr`),
  KEY `sid` (`sid`),
  KEY `ZPS` (`ZPS`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_liga`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_liga` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lid` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `sid` mediumint(3) UNSIGNED DEFAULT NULL,
  `catidAlltime` smallint(6) UNSIGNED NOT NULL DEFAULT 0,
  `catidEdition` smallint(6) UNSIGNED NOT NULL DEFAULT 0,
  `teil` mediumint(5) UNSIGNED DEFAULT NULL,
  `stamm` mediumint(5) UNSIGNED DEFAULT NULL,
  `ersatz` mediumint(5) UNSIGNED DEFAULT NULL,
  `rang` tinyint(1) UNSIGNED DEFAULT 0,
  `sl` int(11) UNSIGNED DEFAULT NULL,
  `runden` mediumint(5) UNSIGNED DEFAULT NULL,
  `durchgang` mediumint(5) UNSIGNED DEFAULT NULL,
  `mail` tinyint(1) UNSIGNED DEFAULT NULL,
  `sl_mail` tinyint(1) UNSIGNED DEFAULT NULL,
  `heim` tinyint(1) UNSIGNED DEFAULT NULL,
  `order` tinyint(1) UNSIGNED DEFAULT NULL,
  `rnd` tinyint(1) UNSIGNED DEFAULT NULL,
  `auf` tinyint(1) UNSIGNED DEFAULT 0,
  `auf_evtl` tinyint(1) UNSIGNED DEFAULT 0,
  `ab` tinyint(1) UNSIGNED DEFAULT 0,
  `ab_evtl` tinyint(1) UNSIGNED DEFAULT 0,
  `sieg_bed` tinyint(2) UNSIGNED DEFAULT NULL,
  `runden_modus` tinyint(2) UNSIGNED DEFAULT NULL,
  `man_sieg` decimal(4,2) UNSIGNED DEFAULT 1.00,
  `man_remis` decimal(4,2) UNSIGNED DEFAULT 0.50,
  `man_nieder` decimal(4,2) UNSIGNED DEFAULT 0.00,
  `man_antritt` decimal(4,2) UNSIGNED DEFAULT 0.00,
  `sieg` decimal(2,1) UNSIGNED DEFAULT 1.0,
  `remis` decimal(2,1) UNSIGNED DEFAULT 0.5,
  `nieder` decimal(2,1) UNSIGNED DEFAULT 0.0,
  `antritt` decimal(2,1) UNSIGNED DEFAULT 0.0,
  `published` mediumint(3) UNSIGNED DEFAULT NULL,
  `bemerkungen` text DEFAULT NULL,
  `bem_int` text DEFAULT NULL,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `b_wertung` tinyint(1) UNSIGNED DEFAULT 0,
  `liga_mt` tinyint(1) UNSIGNED DEFAULT 0,
  `tiebr1` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `tiebr2` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `tiebr3` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `ersatz_regel` tinyint(1) UNSIGNED DEFAULT 0,
  `anzeige_ma` tinyint(1) UNSIGNED DEFAULT 0,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_mannschaften`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_mannschaften` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(100) NOT NULL DEFAULT '',
  `swt_id` mediumint(5) UNSIGNED DEFAULT NULL,
  `liga` mediumint(5) UNSIGNED DEFAULT NULL,
  `zps` varchar(5) DEFAULT NULL,
  `liste` int(11) NOT NULL DEFAULT 0,
  `edit_liste` int(11) NOT NULL DEFAULT 0,
  `man_nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `tln_nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `mf` int(11) UNSIGNED DEFAULT NULL,
  `sg_zps` varchar(120) DEFAULT NULL,
  `datum` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `edit_datum` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `lokal` text DEFAULT NULL,
  `termine` text DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `homepage` text DEFAULT NULL,
  `bemerkungen` text DEFAULT NULL,
  `bem_int` text DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `summanpunkte` decimal(4,1) DEFAULT NULL,
  `sumbrettpunkte` decimal(4,1) DEFAULT NULL,
  `sumwins` tinyint(2) DEFAULT NULL,
  `sumtiebr1` decimal(6,3) DEFAULT 0.000,
  `sumtiebr2` decimal(6,3) DEFAULT 0.000,
  `sumtiebr3` decimal(6,3) DEFAULT 0.000,
  `rankingpos` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid_tlnnr_swtid` (`sid`,`tln_nr`,`swt_id`),
  KEY `published` (`published`),
  KEY `sid` (`sid`),
  KEY `swt_id` (`swt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_meldeliste_spieler`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_meldeliste_spieler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `spielerid` smallint(6) UNSIGNED NOT NULL,
  `sid` mediumint(3) UNSIGNED DEFAULT NULL,
  `swt_id` mediumint(5) UNSIGNED DEFAULT NULL,
  `lid` mediumint(5) UNSIGNED DEFAULT NULL,
  `man_id` mediumint(5) UNSIGNED NOT NULL DEFAULT 0,
  `snr` mediumint(5) UNSIGNED DEFAULT NULL,
  `mgl_nr` mediumint(5) UNSIGNED NOT NULL DEFAULT 0,
  `PKZ` varchar(9) DEFAULT NULL,
  `zps` varchar(5) NOT NULL DEFAULT '0',
  `status` mediumint(5) NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `start_dwz` smallint(4) UNSIGNED DEFAULT NULL,
  `start_I0` smallint(4) UNSIGNED DEFAULT NULL,
  `FIDEelo` smallint(4) UNSIGNED DEFAULT NULL,
  `DWZ` smallint(4) UNSIGNED NOT NULL DEFAULT 0,
  `I0` smallint(4) UNSIGNED NOT NULL DEFAULT 0,
  `Punkte` decimal(4,1) UNSIGNED NOT NULL DEFAULT 0.0,
  `Partien` tinyint(3) NOT NULL DEFAULT 0,
  `We` decimal(6,3) NOT NULL DEFAULT 0.000,
  `Leistung` smallint(4) NOT NULL DEFAULT 0,
  `EFaktor` tinyint(2) NOT NULL DEFAULT 0,
  `Niveau` smallint(4) NOT NULL DEFAULT 0,
  `sum_saison` decimal(5,1) NOT NULL DEFAULT 0.0,
  `gesperrt` tinyint(1) UNSIGNED DEFAULT NULL,
  `attr` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid_swtid_manid_zps_mglnr` (`sid`,`swt_id`,`man_id`,`zps`,`mgl_nr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_rnd_man`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_rnd_man` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) UNSIGNED DEFAULT NULL,
  `swt_id` mediumint(5) UNSIGNED DEFAULT NULL,
  `lid` mediumint(5) UNSIGNED DEFAULT NULL,
  `runde` mediumint(5) UNSIGNED DEFAULT NULL,
  `paar` mediumint(5) UNSIGNED DEFAULT NULL,
  `dg` tinyint(1) UNSIGNED DEFAULT NULL,
  `heim` tinyint(1) UNSIGNED DEFAULT NULL,
  `tln_nr` mediumint(5) UNSIGNED NOT NULL DEFAULT 0,
  `gegner` mediumint(5) UNSIGNED NOT NULL DEFAULT 0,
  `ergebnis` mediumint(5) UNSIGNED DEFAULT NULL,
  `kampflos` tinyint(1) UNSIGNED DEFAULT NULL,
  `brettpunkte` decimal(5,1) UNSIGNED DEFAULT NULL,
  `manpunkte` mediumint(5) UNSIGNED DEFAULT NULL,
  `bp_sum` decimal(5,1) UNSIGNED DEFAULT NULL,
  `mp_sum` mediumint(5) UNSIGNED DEFAULT NULL,
  `gemeldet` int(11) UNSIGNED DEFAULT NULL,
  `editor` int(11) UNSIGNED DEFAULT NULL,
  `dwz_editor` int(11) UNSIGNED DEFAULT NULL,
  `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `dwz_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `wertpunkte` decimal(5,1) UNSIGNED DEFAULT NULL,
  `ko_decision` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `icomment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`),
  KEY `swt_id` (`swt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_rnd_spl`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_rnd_spl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) UNSIGNED DEFAULT NULL,
  `swt_id` mediumint(5) UNSIGNED DEFAULT NULL,
  `lid` mediumint(5) UNSIGNED DEFAULT NULL,
  `runde` mediumint(5) UNSIGNED DEFAULT NULL,
  `paar` mediumint(5) UNSIGNED DEFAULT NULL,
  `dg` tinyint(1) UNSIGNED DEFAULT NULL,
  `tln_nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `brett` mediumint(5) UNSIGNED DEFAULT NULL,
  `heim` tinyint(1) UNSIGNED DEFAULT NULL,
  `weiss` tinyint(1) UNSIGNED DEFAULT NULL,
  `spieler` mediumint(5) UNSIGNED DEFAULT NULL,
  `zps` varchar(5) DEFAULT NULL,
  `gegner` mediumint(5) UNSIGNED DEFAULT NULL,
  `gzps` varchar(5) DEFAULT NULL,
  `ergebnis` mediumint(5) UNSIGNED DEFAULT NULL,
  `kampflos` tinyint(1) UNSIGNED DEFAULT NULL,
  `punkte` decimal(5,1) UNSIGNED DEFAULT NULL,
  `gemeldet` int(11) UNSIGNED DEFAULT NULL,
  `dwz_edit` mediumint(5) UNSIGNED DEFAULT NULL,
  `dwz_editor` int(11) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_runden_termine`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_runden_termine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `liga` mediumint(5) UNSIGNED DEFAULT NULL,
  `swt_liga` mediumint(5) UNSIGNED DEFAULT NULL,
  `nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `datum` date NOT NULL DEFAULT '1970-01-01',
  `startzeit` time NOT NULL DEFAULT '00:00:00',
  `deadlineday` date NOT NULL DEFAULT '1970-01-01',
  `deadlinetime` time NOT NULL DEFAULT '00:00:00',
  `meldung` tinyint(1) NOT NULL DEFAULT 0,
  `sl_ok` tinyint(1) NOT NULL DEFAULT 0,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `bemerkungen` text DEFAULT NULL,
  `bem_int` text DEFAULT NULL,
  `gemeldet` int(11) UNSIGNED DEFAULT NULL,
  `editor` int(11) UNSIGNED DEFAULT NULL,
  `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `enddatum` date NOT NULL DEFAULT '1970-01-01',
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_turniere`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_turniere` (
  `swt_tid` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(100) NOT NULL DEFAULT '',
  `sid` mediumint(3) UNSIGNED DEFAULT NULL,
  `dateStart` date NOT NULL DEFAULT '1970-01-01',
  `dateEnd` date NOT NULL DEFAULT '1970-01-01',
  `catidAlltime` smallint(6) UNSIGNED NOT NULL DEFAULT 0,
  `catidEdition` smallint(6) UNSIGNED NOT NULL DEFAULT 0,
  `typ` tinyint(1) UNSIGNED DEFAULT NULL,
  `tiebr1` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `tiebr2` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `tiebr3` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `rnd` tinyint(1) UNSIGNED DEFAULT NULL,
  `teil` mediumint(5) UNSIGNED DEFAULT NULL,
  `runden` mediumint(5) UNSIGNED DEFAULT NULL,
  `dg` mediumint(5) UNSIGNED DEFAULT NULL,
  `tl` int(11) UNSIGNED DEFAULT NULL,
  `torg` int(11) UNSIGNED DEFAULT NULL,
  `bezirk` varchar(8) DEFAULT NULL,
  `bezirkTur` enum('0','1') NOT NULL DEFAULT '1',
  `vereinZPS` varchar(5) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `started` tinyint(1) NOT NULL DEFAULT 0,
  `finished` tinyint(1) NOT NULL DEFAULT 0,
  `invitationText` text DEFAULT NULL,
  `bemerkungen` text DEFAULT NULL,
  `bem_int` text DEFAULT NULL,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `params` text NOT NULL,
  `sieg` decimal(2,1) UNSIGNED DEFAULT 1.0,
  `siegs` decimal(2,1) UNSIGNED DEFAULT 1.0,
  `remis` decimal(2,1) UNSIGNED DEFAULT 0.5,
  `remiss` decimal(2,1) UNSIGNED DEFAULT 0.5,
  `nieder` decimal(2,1) UNSIGNED DEFAULT 0.0,
  `niederk` decimal(2,1) UNSIGNED DEFAULT 0.0,
  PRIMARY KEY (`swt_tid`),
  KEY `published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_turniere_rnd_spl`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_turniere_rnd_spl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) UNSIGNED DEFAULT NULL,
  `turnier` mediumint(5) UNSIGNED DEFAULT NULL,
  `swt_tid` mediumint(5) UNSIGNED DEFAULT NULL,
  `runde` mediumint(5) UNSIGNED DEFAULT NULL,
  `paar` mediumint(5) UNSIGNED DEFAULT NULL,
  `brett` mediumint(5) UNSIGNED DEFAULT NULL,
  `dg` tinyint(1) UNSIGNED DEFAULT NULL,
  `tln_nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `heim` tinyint(1) UNSIGNED DEFAULT NULL,
  `spieler` mediumint(5) UNSIGNED DEFAULT NULL,
  `gegner` mediumint(5) UNSIGNED DEFAULT NULL,
  `ergebnis` mediumint(5) UNSIGNED DEFAULT NULL,
  `tiebrS` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `tiebrG` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `kampflos` tinyint(1) UNSIGNED DEFAULT NULL,
  `pgn` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `turnier_dg_runde_brett_heim` (`swt_tid`,`dg`,`runde`,`brett`,`heim`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_turniere_rnd_termine`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_turniere_rnd_termine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `turnier` mediumint(5) UNSIGNED DEFAULT NULL,
  `swt_tid` mediumint(5) UNSIGNED DEFAULT NULL,
  `dg` tinyint(1) UNSIGNED DEFAULT NULL,
  `nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `datum` date NOT NULL DEFAULT '1970-01-01',
  `startzeit` time NOT NULL DEFAULT '00:00:00',
  `abgeschlossen` mediumint(3) NOT NULL DEFAULT 0,
  `tl_ok` tinyint(1) NOT NULL DEFAULT 0,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `bemerkungen` text DEFAULT NULL,
  `bem_int` text DEFAULT NULL,
  `gemeldet` int(11) UNSIGNED DEFAULT NULL,
  `editor` int(11) UNSIGNED DEFAULT NULL,
  `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `turnier_dg_runde` (`swt_tid`,`dg`,`nr`),
  KEY `published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_turniere_teams`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_turniere_teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `swt_tid` int(11) UNSIGNED DEFAULT NULL,
  `tid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(100) NOT NULL DEFAULT '',
  `sid` mediumint(5) UNSIGNED DEFAULT NULL,
  `tln_nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `zps` varchar(5) DEFAULT NULL,
  `man_nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `published` mediumint(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tid_tlnnr` (`swt_tid`,`tln_nr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_swt_turniere_tlnr`
--

CREATE TABLE IF NOT EXISTS `#__clm_swt_turniere_tlnr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(3) UNSIGNED DEFAULT NULL,
  `turnier` mediumint(4) UNSIGNED DEFAULT NULL,
  `swt_tid` mediumint(4) UNSIGNED DEFAULT NULL,
  `snr` mediumint(5) UNSIGNED DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `birthYear` year(4) NOT NULL DEFAULT 0000,
  `geschlecht` char(1) DEFAULT NULL,
  `verein` varchar(150) DEFAULT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `twz` smallint(4) UNSIGNED DEFAULT NULL,
  `start_dwz` smallint(4) UNSIGNED DEFAULT NULL,
  `start_I0` smallint(6) UNSIGNED NOT NULL DEFAULT 0,
  `FIDEelo` smallint(4) UNSIGNED DEFAULT NULL,
  `FIDEid` int(8) UNSIGNED DEFAULT NULL,
  `FIDEcco` char(3) DEFAULT NULL,
  `titel` char(3) DEFAULT NULL,
  `mgl_nr` mediumint(5) UNSIGNED NOT NULL DEFAULT 0,
  `PKZ` varchar(9) DEFAULT NULL,
  `zps` varchar(5) NOT NULL DEFAULT '0',
  `tel_no` varchar(30) NOT NULL DEFAULT '',
  `status` mediumint(5) NOT NULL DEFAULT 0,
  `rankingPos` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `tlnrStatus` tinyint(11) UNSIGNED NOT NULL DEFAULT 1,
  `oname` varchar(50) DEFAULT NULL,
  `mtln_nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `s_punkte` decimal(3,1) DEFAULT 0.0,
  `sum_punkte` decimal(4,1) DEFAULT NULL,
  `sum_bhlz` decimal(5,2) DEFAULT NULL,
  `sum_busum` decimal(6,2) DEFAULT NULL,
  `sum_sobe` decimal(5,2) DEFAULT NULL,
  `sum_wins` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `sumTiebr1` decimal(8,3) NOT NULL DEFAULT 0.000,
  `sumTiebr2` decimal(8,3) NOT NULL DEFAULT 0.000,
  `sumTiebr3` decimal(8,3) NOT NULL DEFAULT 0.000,
  `koStatus` enum('0','1') NOT NULL DEFAULT '1',
  `koRound` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `DWZ` smallint(4) UNSIGNED NOT NULL DEFAULT 0,
  `I0` smallint(4) UNSIGNED NOT NULL DEFAULT 0,
  `Punkte` decimal(4,1) UNSIGNED NOT NULL DEFAULT 0.0,
  `Partien` tinyint(3) NOT NULL DEFAULT 0,
  `We` decimal(6,3) NOT NULL DEFAULT 0.000,
  `Leistung` smallint(4) NOT NULL DEFAULT 0,
  `EFaktor` tinyint(2) NOT NULL DEFAULT 0,
  `Niveau` smallint(4) NOT NULL DEFAULT 0,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`,`zps`,`mgl_nr`,`status`),
  UNIQUE KEY `turnier_snr` (`swt_tid`,`snr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_termine`
--

CREATE TABLE IF NOT EXISTS `#__clm_termine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `beschreibung` text DEFAULT NULL,
  `address` varchar(100) NOT NULL DEFAULT '',
  `catidAlltime` smallint(6) UNSIGNED NOT NULL DEFAULT 0,
  `catidEdition` smallint(6) UNSIGNED NOT NULL DEFAULT 0,
  `category` varchar(33) NOT NULL DEFAULT '',
  `host` varchar(5) DEFAULT NULL,
  `startdate` date NOT NULL DEFAULT '1970-01-01',
  `starttime` time NOT NULL DEFAULT '00:00:00',
  `allday` tinyint(3) NOT NULL DEFAULT 0,
  `enddate` date NOT NULL DEFAULT '1970-01-01',
  `endtime` time NOT NULL DEFAULT '00:00:00',
  `noendtime` tinyint(3) NOT NULL DEFAULT 0,
  `attached_file` varchar(256) DEFAULT '',
  `attached_file_description` varchar(128) DEFAULT '',
  `published` mediumint(3) UNSIGNED DEFAULT NULL,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `event_link` varchar(500) NOT NULL,
  `uid` varchar(256) NOT NULL DEFAULT '',
  `created` varchar(16) NOT NULL DEFAULT '',
  `last_modified` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_turniere`
--

CREATE TABLE IF NOT EXISTS `#__clm_turniere` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `sid` mediumint(3) UNSIGNED DEFAULT NULL,
  `dateStart` date NOT NULL DEFAULT '1970-01-01',
  `dateEnd` date NOT NULL DEFAULT '1970-01-01',
  `catidAlltime` smallint(6) UNSIGNED NOT NULL DEFAULT 0,
  `catidEdition` smallint(6) UNSIGNED NOT NULL DEFAULT 0,
  `typ` tinyint(1) UNSIGNED DEFAULT NULL,
  `tiebr1` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `tiebr2` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `tiebr3` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `rnd` tinyint(1) UNSIGNED DEFAULT NULL,
  `teil` mediumint(5) UNSIGNED DEFAULT NULL,
  `runden` mediumint(5) UNSIGNED DEFAULT NULL,
  `dg` mediumint(5) UNSIGNED DEFAULT NULL,
  `tl` int(11) UNSIGNED DEFAULT NULL,
  `torg` int(11) UNSIGNED DEFAULT NULL,
  `bezirk` varchar(8) DEFAULT NULL,
  `bezirkTur` enum('0','1') NOT NULL DEFAULT '1',
  `vereinZPS` varchar(5) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `started` tinyint(1) NOT NULL DEFAULT 0,
  `finished` tinyint(1) NOT NULL DEFAULT 0,
  `invitationText` text DEFAULT NULL,
  `bemerkungen` text DEFAULT NULL,
  `bem_int` text DEFAULT NULL,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `params` text NOT NULL,
  `sieg` decimal(2,1) UNSIGNED DEFAULT 1.0,
  `siegs` decimal(2,1) UNSIGNED DEFAULT 1.0,
  `remis` decimal(2,1) UNSIGNED DEFAULT 0.5,
  `remiss` decimal(2,1) UNSIGNED DEFAULT 0.5,
  `nieder` decimal(2,1) UNSIGNED DEFAULT 0.0,
  `niederk` decimal(2,1) UNSIGNED DEFAULT 0.0,
  `dateRegistration` date NOT NULL DEFAULT '1970-01-01',
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_turniere_rnd_spl`
--

CREATE TABLE IF NOT EXISTS `#__clm_turniere_rnd_spl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) UNSIGNED DEFAULT NULL,
  `turnier` mediumint(5) UNSIGNED DEFAULT NULL,
  `runde` mediumint(5) UNSIGNED DEFAULT NULL,
  `paar` mediumint(5) UNSIGNED DEFAULT NULL,
  `brett` mediumint(5) UNSIGNED DEFAULT NULL,
  `dg` tinyint(1) UNSIGNED DEFAULT NULL,
  `tln_nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `heim` tinyint(1) UNSIGNED DEFAULT NULL,
  `spieler` mediumint(5) UNSIGNED DEFAULT NULL,
  `gegner` mediumint(5) UNSIGNED DEFAULT NULL,
  `ergebnis` mediumint(5) UNSIGNED DEFAULT NULL,
  `tiebrS` decimal(2,1) UNSIGNED NOT NULL DEFAULT 0.0,
  `tiebrG` decimal(2,1) UNSIGNED NOT NULL DEFAULT 0.0,
  `kampflos` tinyint(1) UNSIGNED DEFAULT NULL,
  `gemeldet` mediumint(3) UNSIGNED DEFAULT NULL,
  `pgn` text DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_turniere_rnd_termine`
--

CREATE TABLE IF NOT EXISTS `#__clm_turniere_rnd_termine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(5) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `turnier` mediumint(5) UNSIGNED DEFAULT NULL,
  `dg` tinyint(1) UNSIGNED DEFAULT NULL,
  `nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `datum` date NOT NULL DEFAULT '1970-01-01',
  `startzeit` time NOT NULL DEFAULT '00:00:00',
  `abgeschlossen` mediumint(3) NOT NULL DEFAULT 0,
  `tl_ok` tinyint(1) NOT NULL DEFAULT 0,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `bemerkungen` text DEFAULT NULL,
  `bem_int` text DEFAULT NULL,
  `gemeldet` int(11) UNSIGNED DEFAULT NULL,
  `editor` int(11) UNSIGNED DEFAULT NULL,
  `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_turniere_sonderranglisten`
--

CREATE TABLE IF NOT EXISTS `#__clm_turniere_sonderranglisten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `turnier` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `shortname` varchar(10) NOT NULL DEFAULT '',
  `use_rating_filter` enum('0','1') DEFAULT '0',
  `rating_type` tinyint(1) DEFAULT 0,
  `rating_higher_than` smallint(4) DEFAULT 0,
  `rating_lower_than` smallint(4) DEFAULT 3000,
  `use_birthYear_filter` enum('0','1') DEFAULT '0',
  `birthYear_younger_than` year(4) DEFAULT NULL,
  `birthYear_older_than` year(4) DEFAULT NULL,
  `use_sex_filter` enum('0','1') DEFAULT '0',
  `sex` enum('','M','W') DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `use_zps_filter` enum('0','1') DEFAULT '0',
  `zps_higher_than` varchar(5) DEFAULT '',
  `zps_lower_than` varchar(5) DEFAULT 'ZZZZZ',
  `use_sex_year_filter` enum('0','1') DEFAULT '0',
  `maleYear_younger_than` year(4) DEFAULT NULL,
  `maleYear_older_than` year(4) DEFAULT NULL,
  `femaleYear_younger_than` year(4) DEFAULT NULL,
  `femaleYear_older_than` year(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_turniere_teams`
--

CREATE TABLE IF NOT EXISTS `#__clm_turniere_teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(100) NOT NULL DEFAULT '',
  `sid` mediumint(5) UNSIGNED DEFAULT NULL,
  `tln_nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `zps` varchar(5) DEFAULT NULL,
  `man_nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `published` mediumint(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tid_tlnnr` (`tid`,`tln_nr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_turniere_tlnr`
--

CREATE TABLE IF NOT EXISTS `#__clm_turniere_tlnr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(3) UNSIGNED DEFAULT NULL,
  `turnier` mediumint(4) UNSIGNED DEFAULT NULL,
  `snr` mediumint(5) UNSIGNED DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `birthYear` year(4) NOT NULL DEFAULT 0000,
  `birthDay` date DEFAULT NULL,
  `geschlecht` char(1) DEFAULT NULL,
  `verein` varchar(150) DEFAULT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `twz` smallint(4) UNSIGNED DEFAULT NULL,
  `start_dwz` smallint(4) UNSIGNED DEFAULT NULL,
  `start_I0` smallint(6) UNSIGNED NOT NULL DEFAULT 0,
  `FIDEelo` smallint(4) UNSIGNED DEFAULT NULL,
  `FIDEid` int(8) UNSIGNED DEFAULT NULL,
  `FIDEcco` char(3) DEFAULT NULL,
  `titel` char(3) DEFAULT NULL,
  `mgl_nr` mediumint(5) UNSIGNED NOT NULL DEFAULT 0,
  `PKZ` varchar(9) DEFAULT NULL,
  `zps` varchar(5) NOT NULL DEFAULT '0',
  `tel_no` varchar(30) NOT NULL DEFAULT '',
  `account` varchar(50) NOT NULL DEFAULT '',
  `status` mediumint(5) NOT NULL DEFAULT 0,
  `rankingPos` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `tlnrStatus` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `oname` varchar(50) DEFAULT NULL,
  `mtln_nr` mediumint(5) UNSIGNED DEFAULT NULL,
  `s_punkte` decimal(3,1) DEFAULT 0.0,
  `anz_spiele` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `sum_punkte` decimal(4,1) DEFAULT NULL,
  `sum_bhlz` decimal(5,2) DEFAULT NULL,
  `sum_busum` decimal(6,2) DEFAULT NULL,
  `sum_sobe` decimal(5,2) DEFAULT NULL,
  `koStatus` enum('0','1') NOT NULL DEFAULT '1',
  `koRound` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `sum_wins` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `sumTiebr1` decimal(8,3) DEFAULT NULL,
  `sumTiebr2` decimal(8,3) DEFAULT NULL,
  `sumTiebr3` decimal(8,3) DEFAULT NULL,
  `DWZ` smallint(4) UNSIGNED NOT NULL DEFAULT 0,
  `I0` smallint(4) UNSIGNED NOT NULL DEFAULT 0,
  `Punkte` decimal(4,1) UNSIGNED NOT NULL DEFAULT 0.0,
  `Partien` tinyint(3) NOT NULL DEFAULT 0,
  `We` decimal(6,3) NOT NULL DEFAULT 0.000,
  `Leistung` smallint(4) NOT NULL DEFAULT 0,
  `EFaktor` tinyint(2) NOT NULL DEFAULT 0,
  `Niveau` smallint(4) NOT NULL DEFAULT 0,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `inofFIDEelo` smallint(4) UNSIGNED DEFAULT NULL,
  `K` smallint(4) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`,`zps`,`mgl_nr`,`status`),
  KEY `turnier_snr` (`turnier`,`snr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_user`
--

CREATE TABLE IF NOT EXISTS `#__clm_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` smallint(3) UNSIGNED DEFAULT NULL,
  `jid` int(11) UNSIGNED DEFAULT NULL,
  `name` text NOT NULL,
  `username` varchar(150) NOT NULL DEFAULT '',
  `aktive` tinyint(3) NOT NULL DEFAULT 0,
  `email` varchar(100) NOT NULL DEFAULT '',
  `tel_fest` varchar(30) NOT NULL DEFAULT '',
  `tel_mobil` varchar(30) NOT NULL DEFAULT '',
  `usertype` varchar(75) NOT NULL DEFAULT '',
  `zps` varchar(5) DEFAULT NULL,
  `mglnr` varchar(5) DEFAULT NULL,
  `PKZ` varchar(9) DEFAULT NULL,
  `org_exc` enum('0','1') NOT NULL DEFAULT '0',
  `mannschaft` smallint(3) UNSIGNED DEFAULT NULL,
  `published` smallint(3) UNSIGNED DEFAULT NULL,
  `bemerkungen` text NOT NULL,
  `bem_int` text NOT NULL,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `block` tinyint(4) NOT NULL DEFAULT 0,
  `activation` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid_jid` (`sid`,`jid`),
  KEY `published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_usertype`
--

CREATE TABLE IF NOT EXISTS `#__clm_usertype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `usertype` varchar(15) DEFAULT '0',
  `kind` varchar(4) NOT NULL DEFAULT 'USER',
  `published` int(1) NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usertype` (`usertype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__clm_vereine`
--

CREATE TABLE IF NOT EXISTS `#__clm_vereine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `sid` mediumint(5) UNSIGNED DEFAULT NULL,
  `zps` varchar(5) DEFAULT NULL,
  `vl` int(11) UNSIGNED DEFAULT NULL,
  `lokal` varchar(200) NOT NULL DEFAULT '',
  `lokal_coord` text DEFAULT NULL,
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
  `published` mediumint(3) UNSIGNED DEFAULT NULL,
  `bemerkungen` text NOT NULL,
  `bem_int` text NOT NULL,
  `checked_out` int(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Tabellenstrukturen für Tabellen `#__clm_arbiter*`
--

CREATE TABLE IF NOT EXISTS `#__clm_arbiter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `uuid` varchar(64) NOT NULL DEFAULT 'Fehler',
  `geloescht` timestamp NULL DEFAULT NULL,
  `nurlokal` varchar(1) DEFAULT 'Y',
  `source` varchar(64) DEFAULT NULL,
  `title` varchar(3) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `vorname` varchar(32) DEFAULT NULL,
  `fideid` int(11) DEFAULT 0,
  `fidefed` char(3) DEFAULT 'GER',
  `published` mediumint(11) UNSIGNED DEFAULT NULL,
  `ordering` int(11) DEFAULT 0,
  `pkz` int(11) DEFAULT 0,
  `strasse` varchar(64) DEFAULT NULL,
  `ort` varchar(64) DEFAULT NULL,
  `koord` varchar(64) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `telefon` varchar(128) DEFAULT NULL,
  `mobil` varchar(128) DEFAULT NULL,
  `bemerkungen` text DEFAULT NULL,
  `bem_int` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fideid` (`fideid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__clm_arbiterlicense` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__clm_arbiter_arbiterlicense` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `r_arbiter` int(11) NOT NULL,
  `r_arbiterlicense` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__clm_arbiter_turnier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fideid` int(11) UNSIGNED NOT NULL,
  `turnier` int(11) DEFAULT NULL,
  `liga` int(11) DEFAULT NULL,
  `dg` tinyint(3) UNSIGNED NOT NULL,
  `runde` tinyint(3) UNSIGNED NOT NULL,
  `paar` tinyint(3) UNSIGNED NOT NULL,
  `trole` char(3) DEFAULT NULL,
  `role` char(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

