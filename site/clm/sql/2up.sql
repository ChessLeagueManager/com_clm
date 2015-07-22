--
-- Die Spalte wird nur im Backend als Information verwendet, ob eine Runde ausgewertet wurde.
-- Da bei jeder Änderung/neuen Partie im Turnier die Auswertung entfernt oder neu berechnet werden muss
-- und nicht nur über spezifische Runden gehen darf, macht dies keinen Sinn.
-- Ein Turnier mit allen aktuell gespielten Runden ist ausgewertet oder eben nicht!
-- Diese Spaltung wurde zudem nur von Mannschaftsturnieren/Ligen verwendet und nie für "normale" Turniere implementiert.
--
ALTER TABLE `#__clm_runden_termine` DROP `dwz`;

-- 
-- Vereinheitlichung zwischen Liga und Turnier, für inof. DWZ relevant
--
ALTER TABLE `#__clm_turniere_tlnr` CHANGE `NATrating` `start_dwz` SMALLINT(4);
ALTER TABLE `#__clm_turniere_tlnr` ADD `start_I0` SMALLINT(6) NOT NULL AFTER `start_dwz`;
ALTER TABLE `#__clm_swt_turniere_tlnr` CHANGE `NATrating` `start_dwz` SMALLINT(4);
ALTER TABLE `#__clm_swt_turniere_tlnr` ADD `start_I0` SMALLINT(6) NOT NULL AFTER `start_dwz`;
-- Bugfix für einen DeWIS Import Fehler
DELETE FROM `#__clm_dwz_vereine` WHERE LENGTH(ZPS) < 5;
