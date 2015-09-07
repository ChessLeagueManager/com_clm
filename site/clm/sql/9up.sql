--
-- 3.1.17: Korrektur für das neue Logging
--
DROP TABLE IF EXISTS #__clm_log;

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
