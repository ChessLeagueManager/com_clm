--
-- @ Chess League Manager (CLM) Component
-- @Copyright (C) 2008-2026 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link https://chessleaguemanager.org


--
-- 5.1.3 Spiellokal pro Runde (Einzelturnier)
--

ALTER TABLE `#__clm_turniere_rnd_termine` ADD `lokal` varchar(200) NOT NULL AFTER `ordering`;
ALTER TABLE `#__clm_turniere_rnd_termine` ADD `lokal_coord` text DEFAULT NULL AFTER `lokal`;
