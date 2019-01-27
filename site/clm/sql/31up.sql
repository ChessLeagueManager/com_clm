--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2019 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 3.6.5  Bye-Ergebnisse 
--
INSERT INTO `#__clm_ergebnis` (`id`, `eid`, `erg_text`) VALUES
(12, 11, '1--'),
(13, 12, '0,5--'),
(14, 13, '0--');
--
-- 3.6.5  Sonderpunkte für Einzelturniere 
--
ALTER TABLE `#__clm_turniere_tlnr` ADD `s_punkte` decimal(3,1) DEFAULT '0.0' AFTER `tlnrStatus`;
ALTER TABLE `#__clm_swt_turniere_tlnr` ADD `s_punkte` decimal(3,1) DEFAULT '0.0' AFTER `tlnrStatus`;


