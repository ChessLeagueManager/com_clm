--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2024 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.2.2  Nachbesserung Erstellen DeWIS-Datei zur DWZ-Berechnung 
--

ALTER TABLE `#__clm_ergebnis` ADD `dsb_w` char(1) NOT NULL DEFAULT '' AFTER `erg_text`;
ALTER TABLE `#__clm_ergebnis` ADD `dsb_s` char(1) NOT NULL DEFAULT '' AFTER `dsb_w`;
ALTER TABLE `#__clm_ergebnis` ADD `xml_w` varchar(3) NOT NULL DEFAULT '' AFTER `dsb_s`;
ALTER TABLE `#__clm_ergebnis` ADD `xml_s` varchar(3) NOT NULL DEFAULT '' AFTER `xml_w`;

REPLACE INTO `#__clm_ergebnis` (`id`, `eid`, `erg_text`, `dsb_w`, `dsb_s`, `xml_w`, `xml_s`) VALUES
(1, 0, '0-1',      '0','1','0:1','1:0'),
(2, 1, '1-0',      '1','0','1:0','0:1'),
(3, 2, '0,5-0,5',  'R','R','½:½','½:½'),
(4, 3, '0-0',      '0','-','0:0','0:0'),
(5, 4, '-/+',      '-','+','-:+','+:-'),
(6, 5, '+/-',      '+','-','+:-','-:+'),
(7, 6, '-/-',      ':',':','-:-','-:-'),
(8, 7, '---',      ':',':','',''),
(9, 8, 'spielfrei',':',':','+:-','-:+'),
(10, 9, '0-0,5',   '0','R','½:0','0:½'),
(11, 10, '0,5-0',  'R','0','0:½','½:0'),
(12, 11, '1--',    '+','-','+:-','-:+'),
(13, 12, '0,5--',  '=','-','½:0','0:½'),
(14, 13, '0--',    '-','-','-:-','-:-');

