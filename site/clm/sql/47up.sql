--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2022 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 3.9.10  Korrektur Deadlinetime Default 
--

ALTER TABLE `#__clm_runden_termine` MODIFY `deadlinetime` time NOT NULL DEFAULT '00:00:00';
ALTER TABLE `#__clm_swt_runden_termine` MODIFY `deadlinetime` time NOT NULL DEFAULT '00:00:00';

--
-- 3.9.10  neue zusätzliche Indizes 
--

ALTER TABLE #__clm_saison ADD INDEX `archiv` (`archiv`);
ALTER TABLE #__clm_runden_termine ADD INDEX `liga` (`liga`);
ALTER TABLE #__clm_meldeliste_spieler ADD INDEX `lid` (`lid`);
ALTER TABLE #__clm_rangliste_id ADD INDEX `gid_sid_zps` (`gid`,`sid`,`zps`);
ALTER TABLE #__clm_rangliste_spieler ADD INDEX `sid_ZPS_mannr_mglnr` (`sid`,`ZPS`,`man_nr`,`Mgl_Nr`);

--
-- 3.9.10  Ergänzung swt-Tabelle Liga
--

ALTER TABLE `#__clm_swt_liga` ADD `ersatz_regel` tinyint(1) unsigned DEFAULT '0' AFTER `tiebr3`; 
ALTER TABLE `#__clm_swt_liga` ADD `anzeige_ma` tinyint(1) unsigned DEFAULT '0' AFTER `ersatz_regel`; 
