--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2026 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link https://chessleaguemanager.org

--
-- 5.1.1 Ranglisten mit Stichtag
--

ALTER TABLE `#__clm_rangliste_name` ADD `stichtag_regel` varchar(1) DEFAULT NULL AFTER `geschlecht`;
ALTER TABLE `#__clm_rangliste_name` ADD `stichtag` date DEFAULT NULL AFTER `stichtag_regel`;
