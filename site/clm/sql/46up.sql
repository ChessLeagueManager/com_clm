--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2022 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 3.9.8  pgn-Import grösserer Dateien 
--

ALTER TABLE `#__clm_pgn` 	MODIFY `brett` smallint(8) unsigned DEFAULT NULL;

--
-- 3.9.8  Ergebnismeldung mit interner Bemerkung 
--

ALTER TABLE `#__clm_rnd_man` ADD `icomment` text NOT NULL  AFTER `comment`;


