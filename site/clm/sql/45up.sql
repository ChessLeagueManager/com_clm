--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2021 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 3.9.2  Import Arena-Turnieren aus lichess
--

ALTER TABLE `#__clm_swt_turniere_tlnr` 	MODIFY `sumTiebr1` decimal(8,3) DEFAULT NULL;
ALTER TABLE `#__clm_swt_turniere_tlnr` 	MODIFY `sumTiebr2` decimal(8,3) DEFAULT NULL;
ALTER TABLE `#__clm_swt_turniere_tlnr` 	MODIFY `sumTiebr3` decimal(8,3) DEFAULT NULL;
ALTER TABLE `#__clm_swt_turniere_tlnr` 	ADD `oname` varchar(50) DEFAULT NULL AFTER `tlnrStatus`;
ALTER TABLE `#__clm_turniere_tlnr` 		ADD `oname` varchar(50) DEFAULT NULL AFTER `tlnrStatus`;

--
-- Tabellenstruktur für Tabelle `#__clm_player_decode`
--

CREATE TABLE IF NOT EXISTS `#__clm_player_decode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` mediumint(6) unsigned DEFAULT NULL,
  `source` varchar(20) DEFAULT NULL,
  `oname` varchar(50) DEFAULT NULL,
  `nname` varchar(150) DEFAULT NULL,
  `verein` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid_source_oname` (`sid`,`source`,`oname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

