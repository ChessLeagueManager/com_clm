--
-- @ Chess League Manager (CLM) Component
-- @Copyright (C) 2008-2025 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link https://chessleaguemanager.org

--
-- 4.3.1 Startgeldverwaltung
--

ALTER TABLE `#__clm_turniere_tlnr` ADD `waiting_list_nr` smallint(4) UNSIGNED DEFAULT NULL AFTER `perm_board`;
ALTER TABLE `#__clm_turniere_tlnr` MODIFY `waiting_list_nr` smallint(4) UNSIGNED DEFAULT NULL COMMENT 'Position auf der Warteliste';
ALTER TABLE `#__clm_turniere_tlnr` ADD `date_paid` date DEFAULT NULL AFTER `waiting_list_nr`;
ALTER TABLE `#__clm_turniere_tlnr` MODIFY `date_paid` date DEFAULT NULL COMMENT 'Datum der Einzahlung';
ALTER TABLE `#__clm_turniere_tlnr` ADD `amount_paid` decimal(6,2) DEFAULT NULL AFTER `date_paid`;
ALTER TABLE `#__clm_turniere_tlnr` MODIFY `amount_paid` decimal(6,2) DEFAULT NULL COMMENT 'eingezahlter Betrag';
ALTER TABLE `#__clm_turniere_tlnr` ADD `reason` varchar(100) DEFAULT NULL AFTER `amount_paid`;
ALTER TABLE `#__clm_turniere_tlnr` MODIFY `reason` varchar(100) DEFAULT NULL COMMENT 'Grund dir Differenz';

ALTER TABLE `#__clm_turniere` ADD `entry_fee` decimal(6,2) DEFAULT NULL AFTER `dateRegistration`;
ALTER TABLE `#__clm_turniere` MODIFY `entry_fee` decimal(6,2) DEFAULT NULL COMMENT 'Standard-Startgeld';
ALTER TABLE `#__clm_swt_turniere` ADD `dateRegistration` date NOT NULL DEFAULT '1970-01-01' AFTER `niederk`;
ALTER TABLE `#__clm_swt_turniere` MODIFY `dateRegistration` date NOT NULL DEFAULT '1970-01-01' COMMENT 'Datum der Registrierung';
ALTER TABLE `#__clm_swt_turniere` ADD `entry_fee` decimal(6,2) DEFAULT NULL AFTER `dateRegistration`;
ALTER TABLE `#__clm_swt_turniere` MODIFY `entry_fee` decimal(6,2) DEFAULT NULL COMMENT 'Standard-Startgeld';

--
-- 4.3.1 Ergänzung TRF-Ausgabe für Elo-Auswertung
--

ALTER TABLE `#__clm_turniere` ADD `FIDEcco` char(3) DEFAULT 'GER' AFTER `entry_fee`;
ALTER TABLE `#__clm_turniere` MODIFY `FIDEcco` char(3) DEFAULT 'GER' COMMENT 'Föderation des Veranstalters';
ALTER TABLE `#__clm_turniere` ADD `city` varchar(100) DEFAULT NULL AFTER `FIDEcco`;
ALTER TABLE `#__clm_turniere` MODIFY `city` varchar(100) DEFAULT NULL COMMENT 'Ort des Turniers';
ALTER TABLE `#__clm_swt_turniere` ADD `FIDEcco` char(3) DEFAULT 'GER' AFTER `entry_fee`;
ALTER TABLE `#__clm_swt_turniere` MODIFY `FIDEcco` char(3) DEFAULT 'GER' COMMENT 'Föderation des Veranstalters';
ALTER TABLE `#__clm_swt_turniere` ADD `city` varchar(100) DEFAULT NULL AFTER `FIDEcco`;
ALTER TABLE `#__clm_swt_turniere` MODIFY `city` varchar(100) DEFAULT NULL COMMENT 'Ort des Turniers';

--
-- 4.3.1 Anzeige Spiellokal
--

ALTER TABLE `#__clm_turniere` ADD `lokal` varchar(200) NOT NULL DEFAULT '' AFTER `city`;
ALTER TABLE `#__clm_turniere` MODIFY `lokal` varchar(200) NOT NULL DEFAULT '' COMMENT 'Spiellokal einschl. Anschrift';
ALTER TABLE `#__clm_turniere` ADD `lokal_coord` text DEFAULT NULL AFTER `lokal`;
ALTER TABLE `#__clm_turniere` MODIFY `lokal_coord` text DEFAULT NULL COMMENT 'Geokoordinaten des Spiellokal';
ALTER TABLE `#__clm_swt_turniere` ADD `lokal` varchar(200) NOT NULL DEFAULT '' AFTER `city`;
ALTER TABLE `#__clm_swt_turniere` MODIFY `lokal` varchar(200) NOT NULL DEFAULT '' COMMENT 'Spiellokal einschl. Anschrift';
ALTER TABLE `#__clm_swt_turniere` ADD `lokal_coord` text DEFAULT NULL AFTER `lokal`;
ALTER TABLE `#__clm_swt_turniere` MODIFY `lokal_coord` text DEFAULT NULL COMMENT 'Geokoordinaten des Spiellokal';

