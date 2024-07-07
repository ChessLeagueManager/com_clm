--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2024 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.2.X  Caching von Adresskoordinaten 
--

ALTER TABLE `#__clm_vereine` ADD COLUMN `lokal_coord` GEOMETRY NULL AFTER lokal;
ALTER TABLE `#__clm_mannschaften`  ADD COLUMN lokal_coord GEOMETRY NULL AFTER lokal;
