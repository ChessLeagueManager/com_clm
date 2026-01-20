--
-- @ Chess League Manager (CLM) Component
-- @Copyright (C) 2008-2026 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link https://www.chessleaguemanager.org

--
-- 4.3.2 Übernahme DSB-Daten mit Setzen Ein-/Austrittsdatum
--

ALTER TABLE `#__clm_dwz_spieler` ADD `created` timestamp DEFAULT current_timestamp() AFTER `Fide_Kf`;
ALTER TABLE `#__clm_dwz_spieler` ADD `updated` timestamp DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP() AFTER `created`;
ALTER TABLE `#__clm_dwz_spieler` ADD INDEX `created` (`created`);
ALTER TABLE `#__clm_dwz_spieler` ADD INDEX `updated` (`updated`);


--
-- 4.3.2 Sperrkennzeicchen Mannschaft
--

ALTER TABLE `#__clm_meldeliste_spieler` ADD `gesperrtm` tinyint(1) UNSIGNED DEFAULT NULL AFTER `gesperrt`;
ALTER TABLE `#__clm_swt_meldeliste_spieler` ADD `gesperrtm` tinyint(1) UNSIGNED DEFAULT NULL AFTER `gesperrt`;

