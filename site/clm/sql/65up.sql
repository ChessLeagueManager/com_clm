--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2024 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.2.2  Sperrkennzeichen  
--

ALTER TABLE `#__clm_dwz_spieler` ADD   `gesperrt` tinyint(1) UNSIGNED DEFAULT NULL AFTER `synflag`;
