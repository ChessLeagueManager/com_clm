--
-- @ Chess League Manager (CLM) Component
-- @Copyright (C) 2008-2026 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link https://chessleaguemanager.org


--
-- 5.1.1 Daten für Tabelle `#__clm_zeitmodus`
--
REPLACE INTO `#__clm_zeitmodus` 
(`id`, `typ`, `ordering`, `trf`, `pgn`, `time60`, `name`, `zuege_phase_1`, `sekunden_phase_1`, `increment_phase_1`, `zuege_phase_2`, `sekunden_phase_2`, `increment_phase_2`, `zuege_phase_3`, `sekunden_phase_3`, `increment_phase_3`, `published`) VALUES 
(33, 'Standard',69, '36/5400:1800', '36/5400:1800', '7200', '90 min / 36 Züge + 30 min / Rest der Partie ',36,5400,0,0,1800,0,0,0,0, 1)
;

-- --------------------------------------------------------

--
-- 5.1.1 Daten für Tabelle `#__clm_turniere_tlnr_wl` - Warteliste Einzelturnier
--

CREATE TABLE IF NOT EXISTS `#__clm_turniere_tlnr_wl` (
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
  `PKZ` varchar(12) DEFAULT NULL,
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
  `Fide_Kf` smallint(4) UNSIGNED DEFAULT NULL,
  `perm_board` smallint(4) UNSIGNED DEFAULT NULL,
  `waiting_list_nr` smallint(4) UNSIGNED DEFAULT NULL COMMENT 'Position auf der Warteliste',  
  `date_paid` date DEFAULT NULL COMMENT 'Datum der Einzahlung',
  `amount_paid` decimal(6,2) DEFAULT NULL COMMENT 'eingezahlter Betrag',
  `reason` varchar(100) DEFAULT NULL COMMENT 'Grund der Differenz',
  PRIMARY KEY (`id`,`zps`,`mgl_nr`,`status`),
  KEY `turnier_snr` (`turnier`,`snr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

