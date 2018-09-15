--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2018 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 3.6.2  Feldlängen erhöht 
--
ALTER TABLE `#__clm_dwz_spieler` MODIFY `Spielername` varchar(50) NOT NULL DEFAULT '';
ALTER TABLE `#__clm_dwz_spieler` MODIFY `Spielername_G` varchar(50) NOT NULL DEFAULT '';
ALTER TABLE `#__clm_dwz_spieler` MODIFY `FIDE_Titel` char(3) DEFAULT NULL;
ALTER TABLE `#__clm_dwz_vereine` MODIFY `Vereinname` varchar(60) NOT NULL DEFAULT '';


