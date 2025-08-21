--
-- @ Chess League Manager (CLM) Component
-- @Copyright (C) 2008-2024 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.2.5 Inoffizielle Fide-Elo-Auswertung
--

ALTER TABLE `#__clm_turniere_tlnr` CHANGE `K` `Fide_Kf` smallint(4) UNSIGNED DEFAULT NULL;
ALTER TABLE `#__clm_meldeliste_spieler` CHANGE `K` `Fide_Kf` smallint(4) UNSIGNED DEFAULT NULL;
ALTER TABLE `#__clm_dwz_spieler` CHANGE `K` `Fide_Kf` smallint(4) UNSIGNED DEFAULT NULL;


ALTER TABLE `#__clm_turniere_tlnr` ADD `perm_board` smallint(4) UNSIGNED DEFAULT NULL AFTER `Fide_Kf`;

