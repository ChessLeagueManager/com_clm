--
-- @ Chess League Manager (CLM) Component
-- @Copyright (C) 2008-2026 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link https://chessleaguemanager.org


--
-- 5.1.2 Daten für Tabelle `#__clm_zeitmodus`
--
REPLACE INTO `#__clm_zeitmodus` 
(`id`, `typ`, `ordering`, `trf`, `pgn`, `time60`, `name`, `zuege_phase_1`, `sekunden_phase_1`, `increment_phase_1`, `zuege_phase_2`, `sekunden_phase_2`, `increment_phase_2`, `zuege_phase_3`, `sekunden_phase_3`, `increment_phase_3`, `published`) VALUES 
(34, 'Standard',59, '3600+15', '3600+15', '4500', '60 min für die Partie plus 30 sec / Zug ab dem 1. Zug ',0,3600,15,0,0,0,0,0,0, 1)
;


--
-- 5.1.2 Kein Ersatz aus bestimmten Mannschaften
--

ALTER TABLE `#__clm_mannschaften` ADD `noersatz` varchar(50) DEFAULT NULL AFTER `sg_zps`;


--
-- 5.1.2 neue Daten vom DSB
--

ALTER TABLE `#__clm_dwz_spieler` ADD `eloRapid` int UNSIGNED DEFAULT NULL AFTER `FIDE_Land`;
ALTER TABLE `#__clm_dwz_spieler` ADD `eloBlitz` int UNSIGNED DEFAULT NULL AFTER `eloRapid`;
ALTER TABLE `#__clm_dwz_spieler` ADD `wzVerein` int UNSIGNED DEFAULT NULL AFTER `eloBlitz`;
ALTER TABLE `#__clm_dwz_spieler` ADD `indexVerein` int UNSIGNED DEFAULT NULL AFTER `wzVerein`;
