--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2023 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.0.4  Sonderranglisten als Wertungsgruppen
--

ALTER TABLE `#__clm_turniere_sonderranglisten` ADD `shortname` varchar(10) NOT NULL DEFAULT '' AFTER `name`;


