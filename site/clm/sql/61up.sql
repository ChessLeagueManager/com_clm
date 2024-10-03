--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2024 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.2.1  Caching von Adresskoordinaten (TEXT)
--

ALTER TABLE `#__clm_vereine` ADD COLUMN `lokal_coord` text NULL AFTER lokal;
ALTER TABLE `#__clm_mannschaften`  ADD COLUMN `lokal_coord` text NULL AFTER lokal;

ALTER TABLE `#__clm_turniere_tlnr`  ADD COLUMN `birthDay` date DEFAULT NULL AFTER birthYear; 