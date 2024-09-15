--
-- @ Chess League Manager (CLM) Component
-- @Copyright (C) 2008-2024 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.2.2 Inoffizielle Fide-Elo-Auswertung
--

ALTER TABLE `#__clm_turniere_tlnr` ADD `inofFIDEelo` smallint(4) UNSIGNED DEFAULT NULL AFTER `ordering`;
ALTER TABLE `#__clm_turniere_tlnr` ADD `K` smallint(4) UNSIGNED DEFAULT NULL AFTER `inofFIDEelo`;
ALTER TABLE `#__clm_meldeliste_spieler` ADD `inofFIDEelo` smallint(4) UNSIGNED DEFAULT NULL AFTER `attr`;
ALTER TABLE `#__clm_meldeliste_spieler` ADD `K` smallint(4) UNSIGNED DEFAULT NULL AFTER `inofFIDEelo`;
ALTER TABLE `#__clm_dwz_spieler` ADD `inofFIDEelo` smallint(4) UNSIGNED DEFAULT NULL AFTER `synflag`;
ALTER TABLE `#__clm_dwz_spieler` ADD `K` smallint(4) UNSIGNED DEFAULT NULL AFTER `inofFIDEelo`;
