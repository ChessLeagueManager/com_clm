--
-- @ Chess League Manager (CLM) Component
-- @Copyright (C) 2008-2025 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link https://chessleaguemanager.org


--
-- 4.3.2 Ergänzung TRF-Ausgabe für Elo-Auswertung
--

ALTER TABLE `#__clm_liga` ADD `dateStart` date NOT NULL DEFAULT '1970-01-01' AFTER `sid`;
ALTER TABLE `#__clm_liga` MODIFY `dateStart` date NOT NULL DEFAULT '1970-01-01' COMMENT 'Startdatum';
ALTER TABLE `#__clm_liga` ADD `dateEnd` date NOT NULL DEFAULT '1970-01-01' AFTER `dateStart`;
ALTER TABLE `#__clm_liga` MODIFY `dateEnd` date NOT NULL DEFAULT '1970-01-01' COMMENT 'Endedatum';
ALTER TABLE `#__clm_swt_liga` ADD `dateStart` date NOT NULL DEFAULT '1970-01-01' AFTER `sid`;
ALTER TABLE `#__clm_swt_liga` MODIFY `dateStart` date NOT NULL DEFAULT '1970-01-01' COMMENT 'Startdatum';
ALTER TABLE `#__clm_swt_liga` ADD `dateEnd` date NOT NULL DEFAULT '1970-01-01' AFTER `dateStart`;
ALTER TABLE `#__clm_swt_liga` MODIFY `dateEnd` date NOT NULL DEFAULT '1970-01-01' COMMENT 'Endedatum';

ALTER TABLE `#__clm_liga` ADD `FIDEcco` char(3) DEFAULT 'GER' AFTER `params`;
ALTER TABLE `#__clm_liga` MODIFY `FIDEcco` char(3) DEFAULT 'GER' COMMENT 'Föderation des Veranstalters';
ALTER TABLE `#__clm_liga` ADD `city` varchar(100) DEFAULT NULL AFTER `FIDEcco`;
ALTER TABLE `#__clm_liga` MODIFY `city` varchar(100) DEFAULT NULL COMMENT 'Ort des Turniers';
ALTER TABLE `#__clm_swt_liga` ADD `FIDEcco` char(3) DEFAULT 'GER' AFTER `params`;
ALTER TABLE `#__clm_swt_liga` MODIFY `FIDEcco` char(3) DEFAULT 'GER' COMMENT 'Föderation des Veranstalters';
ALTER TABLE `#__clm_swt_liga` ADD `city` varchar(100) DEFAULT NULL AFTER `FIDEcco`;
ALTER TABLE `#__clm_swt_liga` MODIFY `city` varchar(100) DEFAULT NULL COMMENT 'Ort des Turniers';


--
-- 4.3.2 Anzeige Spiellokal
--

ALTER TABLE `#__clm_liga` ADD `lokal` varchar(200) NOT NULL DEFAULT '' AFTER `city`;
ALTER TABLE `#__clm_liga` MODIFY `lokal` varchar(200) NOT NULL DEFAULT '' COMMENT 'Spiellokal einschl. Anschrift';
ALTER TABLE `#__clm_liga` ADD `lokal_coord` text DEFAULT NULL AFTER `lokal`;
ALTER TABLE `#__clm_liga` MODIFY `lokal_coord` text DEFAULT NULL COMMENT 'Geokoordinaten des Spiellokal';
ALTER TABLE `#__clm_swt_liga` ADD `lokal` varchar(200) NOT NULL DEFAULT '' AFTER `city`;
ALTER TABLE `#__clm_swt_liga` MODIFY `lokal` varchar(200) NOT NULL DEFAULT '' COMMENT 'Spiellokal einschl. Anschrift';
ALTER TABLE `#__clm_swt_liga` ADD `lokal_coord` text DEFAULT NULL AFTER `lokal`;
ALTER TABLE `#__clm_swt_liga` MODIFY `lokal_coord` text DEFAULT NULL COMMENT 'Geokoordinaten des Spiellokal';



--
-- 4.3.2 Tabelle Zeitmodus
--

CREATE TABLE IF NOT EXISTS `#__clm_zeitmodus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typ` varchar(20) NOT NULL DEFAULT '' COMMENT 'Zeittyp',
  `ordering` smallint(4) UNSIGNED DEFAULT '0' COMMENT 'Anzeige-Reihenfolge',
  `trf` varchar(30) NOT NULL DEFAULT '' COMMENT 'Code für TRF-Ausgabe',
  `pgn` varchar(30) NOT NULL DEFAULT '' COMMENT 'Code für PGN-Ausgabe',
  `time60` smallint(6) UNSIGNED DEFAULT '0' COMMENT 'max. Zeit für 60 Züge',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT 'Text für Anzeige in FE und BE',
  `zuege_phase_1` smallint(4) UNSIGNED DEFAULT '0' COMMENT 'Züge 1.Periode',
  `sekunden_phase_1` smallint(6) UNSIGNED DEFAULT '0' COMMENT 'Zeit 1.Periode',
  `increment_phase_1` smallint(4) UNSIGNED DEFAULT '0' COMMENT 'Inkrement 1.Periode',
  `zuege_phase_2` smallint(4) UNSIGNED DEFAULT '0' COMMENT 'Züge 2.Periode',
  `sekunden_phase_2` smallint(6) UNSIGNED DEFAULT '0' COMMENT 'Zeit 2.Periode',
  `increment_phase_2` smallint(4) UNSIGNED DEFAULT '0' COMMENT 'Inkrement 2.Periode',
  `zuege_phase_3` smallint(4) UNSIGNED DEFAULT '0' COMMENT 'Züge 3.Periode',
  `sekunden_phase_3` smallint(6) UNSIGNED DEFAULT '0' COMMENT 'Zeit 3.Periode',
  `increment_phase_3` smallint(4) UNSIGNED DEFAULT '0' COMMENT 'Inkrement 3.Periode',
  `published` tinyint(1) UNSIGNED DEFAULT '0' COMMENT 'veröffentlicht',
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `#__clm_zeitmodus`
--
REPLACE INTO `#__clm_zeitmodus` 
(`id`, `typ`, `ordering`, `trf`, `pgn`, `time60`, `name`, `zuege_phase_1`, `sekunden_phase_1`, `increment_phase_1`, `zuege_phase_2`, `sekunden_phase_2`, `increment_phase_2`, `zuege_phase_3`, `sekunden_phase_3`, `increment_phase_3`, `published`) VALUES 
(1, 'Bullet',10, '60', '60', '60', '1 min für die Partie',0,60,0,0,0,0,0,0,0, 1),
(2, 'Bullet',12, '0+2', '0+2', '120', 'nur 2 sec pro Zug',0,0,2,0,0,0,0,0,0, 1),
(3, 'Blitz',20, '180+2', '180+2', '300', '3 min plus 2 sec pro Zug',0,180,2,0,0,0,0,0,0, 1),
(4, 'Blitz',22, '300', '300', '300', '5 min für die Partie',0,300,0,0,0,0,0,0,0, 1),
(5, 'Blitz',24, '300+2', '300+2', '420', '5 min plus 2 sec pro Zug',0,300,2,0,0,0,0,0,0, 1),
(6, 'Rapid',30, '600', '600', '600', '10 min für die Partie',0,600,0,0,0,0,0,0,0, 1),
(7, 'Rapid',32, '150+10', '150+10', '750', '150 sec plus 10 sec pro Zug',0,150,10,0,0,0,0,0,0, 1),
(8, 'Rapid',34, '600+5', '600+5', '900', '10 min plus 5 sec pro Zug',0,600,5,0,0,0,0,0,0, 1),
(9, 'Rapid',36, '900', '900', '900', '15 min für die Partie',0,900,0,0,0,0,0,0,0, 1),
(10, 'Rapid',38, '1200', '1200', '1200', '20 min für die Partie',0,1200,0,0,0,0,0,0,0, 1),
(11, 'Rapid',40, '1500', '1500', '1500', '25 min für die Partie',0,1500,0,0,0,0,0,0,0, 1),
(12, 'Rapid',42, '1800', '1800', '1800', '30 min für die Partie',0,1800,0,0,0,0,0,0,0, 1),
(13, 'Rapid',44, '36/1500:300+10', '36/1500:300+10', '2040', '25 min / 36 Züge + 5 min / Rest der Partie plus 10 sec / Zug ab dem 37. Zug  ',36,1500,0,0,0,0,0,0,0, 1),
(14, 'Rapid',46, '1500+10', '1500+10', '2100', '25 min plus 10 sec pro Zug',0,1500,10,0,0,0,0,0,0, 1),
(15, 'Rapid',48, '3600', '3600', '3600', '60 min für die Partie',0,3600,0,0,0,0,0,0,0, 1),
(16, 'Standard',60, '3600+30', '3600+30', '5400',  '60 min für die Partie plus 30 sec / Zug ab dem 1. Zug',0,3600,30,0,0,0,0,0,0, 1),
(17, 'Standard',62, '5400', '5400', '5400', '90 min für die gesamte Partie',0,5400,0,0,0,0,0,0,0, 1),
(18, 'Standard',64, '5400+30', '5400+30', '7200', '90 min für die Partie plus 30 sec / Zug ab dem 1. Zug',0,5400,30,0,0,0,0,0,0, 1),
(19, 'Standard',66, '7200', '7200', '7200', '120 min für die gesamte Partie',0,7200,0,0,0,0,0,0,0, 1),
(20, 'Standard',68, '40/5400+30:900+30', '40/5400+30:900+30', '8100', '90 min / 40 Züge + 15 min / Rest der Partie plus 30 sec ab dem 1. Zug',40,5400,30,0,900,30,0,0,0, 1),
(21, 'Standard',70, '40/6300+30', '40/6300+30', '8100', '105 min plus 30 sec / Zug ab dem 1. Zug ',0,6300,30,0,0,0,0,0,0, 1),
(22, 'Standard',72, '40/7200:900+30', '40/7200:900+30', '8700', '120 min / 40 Züge + 15 min / Rest der Partie plus 30 sec / Zug ab dem 41. Zug ',40,7200,0,0,900,30,0,0,0, 1),
(23, 'Standard',74, '40/5400+30:1800+30', '40/5400+30:1800+30', '9000', '90 min / 40 Züge + 30 min / Rest der Partie plus 30 sec ab dem 1. Zug',40,5400,30,0,1800,30,0,0,0, 1),
(24, 'Standard',76, '7200+30', '7200+30', '9000', '120 min für die gesamte Partie plus 30 sec / Zug ab dem 1. Zug',0,7200,30,0,0,0,0,0,0, 1),
(25, 'Standard',80, '40/7200:1800', '40/7200:1800', '9000', '120 min / 40 Züge + 30 min / Rest der Partie',40,7200,0,0,1800,0,0,0,0, 1),
(26, 'Standard',82, '40/6000+30:20/3000+30:900+30', '40/6000+30:20/3000+30:900+30', '10800', '100 min / 40 Züge + 50 min / 20 Züge + 15 min / Rest der Partie plus 30 sec / Zug ab dem 1. Zug',40,6000,30,20,3000,30,0,900,30, 1),
(27, 'Standard',84, '40/6000+30:3000+30', '40/6000+30:3000+30', '10800', '100 min / 40 Züge + 50 min / Rest der Partie plus 30 sec / Zug ab dem 1. Zug ',0,6300,30,0,0,0,0,0,0, 1),
(28, 'Standard',86, '40/7200:3600', '40/7200:3600', '10800', '120 min / 40 Züge + 60 min / Rest der Partie ',40,7200,0,0,3600,0,0,0,0, 1),
(29, 'Standard',88, '40/7200:20/3600:1800', '40/7200:20/3600:1800', '10800', '120 min / 40 Züge + 60 min / 20 Züge + 30 min / Rest der Partie ',40,7200,0,20,3600,0,1800,0,0, 1),
(30, 'Standard',90, '40/7200:20/3600:1800', '40/7200:20/3600:1800', '10800', '120 min / 40 Züge + 60 min / 20 Züge + 60 min / Rest der Partie ',40,7200,0,20,3600,0,3600,0,0, 1)
;