--
-- @ Chess League Manager (CLM) Component
-- @Copyright (C) 2008-2026 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link https://chessleaguemanager.org


--
-- 4.3.2 Tabelle Images (Logos)
--

DROP TABLE IF EXISTS #__clm_images;

CREATE TABLE IF NOT EXISTS `#__clm_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typ` varchar(4) DEFAULT NULL COMMENT 'Anwendungsfeld (Key0)',
  `key1` varchar(10) DEFAULT NULL COMMENT 'Key1',
  `key2` varchar(5) DEFAULT NULL COMMENT 'Key2',
  `image` text NOT NULL COMMENT 'Bild in base64',
  `width` smallint(4) UNSIGNED NOT NULL COMMENT 'Breite in px',
  `height` smallint(4) UNSIGNED NOT NULL COMMENT 'Höhe in px',
  PRIMARY KEY (`id`),
  UNIQUE KEY `typ_key1_key2` (`typ`,`key1`,`key2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
