--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2021 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 4.3.2  Schiedsrichter-Tabellen
--

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
  `name` varchar(32) DEFAULT NULL,
  `vorname` varchar(32) DEFAULT NULL,
  `fideid` int(11) DEFAULT 0,
  `endoflicense` date DEFAULT NULL,
  `pkz` int(11) DEFAULT 0,
  `strasse` varchar(64) DEFAULT NULL,
  `ort` varchar(64) DEFAULT NULL,
  `koord` varchar(64) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `telefon` varchar(128) DEFAULT NULL,
  `mobil` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `arb_uuid` (`uuid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__clm_arbiterlicense` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__clm_arbiter_arbiterlicense` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `r_arbiter` int(11) NOT NULL,
  `r_arbiterlicense` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__clm_arbiter_turnier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `r_arbiter` int(11) NOT NULL,
  `r_turnier` int(11) NOT NULL,
  `funktion` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

REPLACE INTO `#__clm_arbiterlicense` (`id`, `name`) VALUES
(1, 'verein_turnierleiter'),
(2, 'verein_turnierorganisator'),
(3, 'bezirk_turnierleiter'),
(4, 'bezirk_turnierorganisator'),
(5, 'bezirk_spielleiter'),
(6, 'bezirk_staffelleiter'),
(7, 'verband_turnierleiter'),
(8, 'verband_turnierorganisator'),
(9, 'verband_spielleiter'),
(10, 'verband_staffelleiter'),
(11, 'landesverband_turnierleiter'),
(12, 'landesverband_turnierorganisator'),
(13, 'landesverband_spielleiter'),
(14, 'landesverband_staffelleiter'),
(15, 'dsb_verbandsschiedsrichter'),
(16, 'dsb_turnierorganisator'),
(17, 'dsb_regionalschiedsrichter'),
(18, 'dsb_nationalschiedsrichter'),
(19, 'fide_turnierorganisator'),
(20, 'fide_national_arbiter'),
(21, 'fide_fide_arbiter'),
(22, 'fide_international_arbiter');
