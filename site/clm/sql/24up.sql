--
-- 3.3.5  PGN-Import
--
ALTER TABLE `#__clm_rnd_spl` ADD `pgnnr` int(11) unsigned DEFAULT '0' AFTER `dwz_editor`;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

