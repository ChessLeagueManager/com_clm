--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2020 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 3.8.6  Umstellung NWZ in England
--
ALTER TABLE `#__clm_saison` ADD `rating_type` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `datum`;
ALTER TABLE `#__clm_dwz_spieler` ADD `Junior` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `Geburtsjahr`;
