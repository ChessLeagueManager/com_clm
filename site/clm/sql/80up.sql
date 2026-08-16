--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2026 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link https://chessleaguemanager.org

--
-- 5.1.1 Turnierteilnehmer mit Anmeldezeit
--

ALTER TABLE `#__clm_turniere_tlnr` ADD `regtime` datetime DEFAULT current_timestamp() AFTER `reason`;
ALTER TABLE `#__clm_turniere_tlnr` ADD `pcomment` text DEFAULT NULL  AFTER `regtime`;
ALTER TABLE `#__clm_turniere_tlnr_wl` ADD `regtime` datetime DEFAULT current_timestamp() AFTER `reason`;
ALTER TABLE `#__clm_turniere_tlnr_wl` ADD `pcomment` text DEFAULT NULL  AFTER `regtime`;

--
-- 5.1.1 Geburtstag für Vereinsspieler
--

ALTER TABLE `#__clm_dwz_spieler` ADD `Geburtstag` date DEFAULT NULL AFTER `Geburtsjahr`;
