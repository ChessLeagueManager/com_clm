--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2019 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 3.7.2: Online-Turnieranmeldung
--

--
-- Tabellenstruktur für Tabelle `#__clm_online_registration`
--
CREATE TABLE IF NOT EXISTS `#__clm_online_registration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` mediumint(5) unsigned DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `vorname` varchar(50) DEFAULT NULL,
  `club` varchar(60) DEFAULT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `elo` smallint(4) unsigned DEFAULT NULL,
  `dwz` smallint(4) unsigned DEFAULT NULL,
  `status` mediumint(5) NOT NULL DEFAULT '0',
  `timestamp` int(11) NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Turniertabelle Online-Anmeldung möglich bis 
--
ALTER TABLE `#__clm_turniere` ADD `dateRegistration` date NOT NULL AFTER `niederk`;
