--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2023 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.1.3  Sperren im Rangfolgesystem 
--

SET SESSION SQL_MODE='ALLOW_INVALID_DATES';
ALTER TABLE `#__clm_rangliste_spieler` ADD `gesperrt` tinyint(1) unsigned DEFAULT NULL AFTER `sid`;

