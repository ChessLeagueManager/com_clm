--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2024 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.2.2  Ergänzung SWT-Import 
--

ALTER TABLE `#__clm_swt_liga` ADD `catidAlltime` smallint(6) UNSIGNED NOT NULL DEFAULT 0 AFTER `sid`;
ALTER TABLE `#__clm_swt_liga` ADD `catidEdition` smallint(6) UNSIGNED NOT NULL DEFAULT 0 AFTER `catidAlltime`;
