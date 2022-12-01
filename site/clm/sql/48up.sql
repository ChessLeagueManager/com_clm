--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2022 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.0.1  Ergebnismeldung mit interner Bemerkung (Rückwirkung auf Importe)
--

ALTER TABLE `#__clm_swt_rnd_man` ADD `icomment` text NOT NULL  AFTER `comment`;


