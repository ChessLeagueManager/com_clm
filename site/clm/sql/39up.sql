--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2020 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 3.8.2: Erweiterung SWT-Import Ligen/Mannschaftsturniere
--

--
-- Tabellenstruktur für Tabelle `#__clm_swt_meldeliste_spieler` erweitert, Anpassung an `#__clm_meldeliste_spieler`
--
ALTER TABLE `#__clm_swt_meldeliste_spieler` ADD `PKZ` varchar(9) DEFAULT NULL AFTER `mgl_nr`;
ALTER TABLE `#__clm_swt_meldeliste_spieler` ADD `start_dwz` smallint(4) unsigned DEFAULT NULL AFTER `ordering`;
ALTER TABLE `#__clm_swt_meldeliste_spieler` ADD `start_I0` smallint(4) unsigned DEFAULT NULL AFTER `start_dwz`;
ALTER TABLE `#__clm_swt_meldeliste_spieler` ADD `FIDEelo` smallint(4) unsigned DEFAULT NULL AFTER `start_I0`;
