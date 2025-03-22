--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2025 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.3.4  Benutzer-Tabelle erweitern
--

ALTER TABLE `#__clm_user` ADD `fideid` int(11) UNSIGNED DEFAULT NULL AFTER `PKZ`;


--
-- 4.3.4  ERgebnis-Tabelle korrigieren
--

REPLACE INTO `#__clm_ergebnis` (`id`, `eid`, `erg_text`, `dsb_w`, `dsb_s`, `xml_w`, `xml_s`) VALUES
(4, 3, '0-0',      '0','0','0:0','0:0');
