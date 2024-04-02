--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2024 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.1.5  Ein- und Austrittsdatum während der Saison 
--

ALTER TABLE `#__clm_dwz_spieler` ADD `joiningdate` date NOT NULL DEFAULT '1970-01-01' AFTER `Niveau`;
ALTER TABLE `#__clm_dwz_spieler` ADD `leavingdate` date NOT NULL DEFAULT '1970-01-01' AFTER `joiningdate`;
ALTER TABLE `#__clm_dwz_spieler` ADD `synflag` boolean NOT NULL DEFAULT '0' AFTER `leavingdate`;

