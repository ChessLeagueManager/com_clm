--
-- @ Chess League Manager (CLM) Component
-- @Copyright (C) 2008-2026 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link https://chessleaguemanager.org


--
-- 4.3.3 Daten für Tabelle `#__clm_zeitmodus`
--
REPLACE INTO `#__clm_zeitmodus` 
(`id`, `typ`, `ordering`, `trf`, `pgn`, `time60`, `name`, `zuege_phase_1`, `sekunden_phase_1`, `increment_phase_1`, `zuege_phase_2`, `sekunden_phase_2`, `increment_phase_2`, `zuege_phase_3`, `sekunden_phase_3`, `increment_phase_3`, `published`) VALUES 
(21, 'Standard',70, '6300+30', '6300+30', '8100', '105 min plus 30 sec / Zug ab dem 1. Zug ',0,6300,30,0,0,0,0,0,0, 1),
(31, 'Standard',92, '7800+30', '7800+30', '9600', '130 min plus 30 sec / Zug ab dem 1. Zug ',0,7800,30,0,0,0,0,0,0, 1),
(32, 'Rapid',37, '900+5', '900+5', '1200', '15 min plus 5 sec / Zug ab dem 1. Zug ',0,900,5,0,0,0,0,0,0, 1)
;