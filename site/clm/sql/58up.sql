--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2024 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.2.1  Sperren im Rangfolgesystem 
--

SET SESSION SQL_MODE='ALLOW_INVALID_DATES';
ALTER TABLE `#__clm_rangliste_name` ADD `anz_sgp` tinyint(1) NOT NULL DEFAULT 0 AFTER `published`;

UPDATE `#__clm_rangliste_name` SET anz_sgp = 1 WHERE anz_sgp = 0;