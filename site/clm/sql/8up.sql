--
-- 3.1.15 ELO kann als DWZ bei Spielern ohne DWZ genommen werden
--
ALTER TABLE `#__clm_meldeliste_spieler` ADD `FIDEelo` SMALLINT(4) UNSIGNED AFTER `start_I0`;
