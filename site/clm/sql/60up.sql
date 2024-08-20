--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2024 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.2.1  Caching von Adresskoordinaten (DROP GEOMETRY)
--

ALTER TABLE `#__clm_vereine` DROP COLUMN `lokal_coord`;
ALTER TABLE `#__clm_mannschaften`  DROP COLUMN `lokal_coord`;