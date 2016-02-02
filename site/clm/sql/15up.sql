--
-- 3.2.3  DB-Erweiterung für Englische Version
--
-- Erweiterung Stammtabellen - !! Spalte Allocation wird später hinzugefügt 
ALTER TABLE `#__clm_dwz_verbaende` CHANGE `Verband` `Verband` varchar(4) NOT NULL DEFAULT '';
ALTER TABLE `#__clm_dwz_verbaende` CHANGE `Uebergeordnet` `Uebergeordnet` varchar(4) NOT NULL DEFAULT '';
ALTER TABLE `#__clm_dwz_vereine` CHANGE `Verband` `Verband` varchar(4) NOT NULL DEFAULT '';
--
-- Erweiterung da PKZ neuer Key anstelle Mitgliedsnummer im Verein
ALTER TABLE `#__clm_meldeliste_spieler` ADD `PKZ` varchar(9) DEFAULT NULL AFTER `mgl_nr`;
ALTER TABLE `#__clm_rnd_spl` ADD `PKZ` varchar(9) DEFAULT NULL AFTER `spieler`;
ALTER TABLE `#__clm_rnd_spl` ADD `gPKZ` varchar(9) DEFAULT NULL AFTER `gegner`;
