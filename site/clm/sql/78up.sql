--
-- @ Chess League Manager (CLM) Component
-- @Copyright (C) 2008-2026 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link https://chessleaguemanager.org

--
-- 5.0.1 Update clm_dwz_spieler, clm_dwz_vereine, clm_dwz_verbaende
--

UPDATE `#__clm_dwz_spieler` set Letzte_Auswertung = NULL;
ALTER TABLE `#__clm_dwz_spieler` CHANGE COLUMN PKZ PKZ VARCHAR(12);
ALTER TABLE `#__clm_dwz_spieler` CHANGE COLUMN Spielername Spielername VARCHAR(128);
ALTER TABLE `#__clm_dwz_spieler` CHANGE COLUMN Spielername_G Spielername_G VARCHAR(128);
ALTER TABLE `#__clm_dwz_spieler` CHANGE COLUMN Letzte_Auswertung Letzte_Auswertung DATE DEFAULT NULL;
ALTER TABLE `#__clm_dwz_vereine` CHANGE COLUMN Vereinname Vereinname VARCHAR(128);
ALTER TABLE `#__clm_dwz_verbaende` CHANGE COLUMN Verbandname Verbandname VARCHAR(128);
ALTER TABLE `#__clm_swt_dwz_spieler` CHANGE COLUMN PKZ PKZ VARCHAR(12);
ALTER TABLE `#__clm_swt_dwz_spieler` CHANGE COLUMN Spielername Spielername VARCHAR(128);
ALTER TABLE `#__clm_swt_dwz_spieler` CHANGE COLUMN Spielername_G Spielername_G VARCHAR(128);
ALTER TABLE `#__clm_swt_dwz_spieler` CHANGE COLUMN Letzte_Auswertung Letzte_Auswertung DATE DEFAULT NULL;
ALTER TABLE `#__clm_meldeliste_spieler` CHANGE COLUMN PKZ PKZ VARCHAR(12);
ALTER TABLE `#__clm_online_registration` CHANGE COLUMN PKZ PKZ VARCHAR(12);
ALTER TABLE `#__clm_rangliste_spieler` CHANGE COLUMN PKZ PKZ VARCHAR(12) DEFAULT NULL;
ALTER TABLE `#__clm_rnd_spl` CHANGE COLUMN PKZ PKZ VARCHAR(12);
ALTER TABLE `#__clm_rnd_spl` CHANGE COLUMN gPKZ gPKZ VARCHAR(12);
ALTER TABLE `#__clm_swt_meldeliste_spieler` CHANGE COLUMN PKZ PKZ VARCHAR(12);
ALTER TABLE `#__clm_swt_turniere_tlnr` CHANGE COLUMN PKZ PKZ VARCHAR(12);
ALTER TABLE `#__clm_turniere_tlnr` CHANGE COLUMN PKZ PKZ VARCHAR(12);
ALTER TABLE `#__clm_user` CHANGE COLUMN PKZ PKZ VARCHAR(12);

--
-- 5.0.1 FIDE-ID länger als 8 Stellen
--

ALTER TABLE `#__clm_dwz_spieler` CHANGE COLUMN FIDE_ID FIDE_ID int DEFAULT NULL;
ALTER TABLE `#__clm_online_registration` CHANGE COLUMN `FIDEid` `FIDEid` int DEFAULT NULL; 
ALTER TABLE `#__clm_swt_dwz_spieler` CHANGE COLUMN FIDE_ID FIDE_ID int DEFAULT NULL;
ALTER TABLE `#__clm_swt_turniere_tlnr` CHANGE COLUMN `FIDEid` `FIDEid` int DEFAULT NULL; 
ALTER TABLE `#__clm_turniere_tlnr` CHANGE COLUMN `FIDEid` `FIDEid` int DEFAULT NULL; 
ALTER TABLE `#__clm_swt_turniere_tlnr` ADD `birthDay` date DEFAULT NULL AFTER `birthYear`; 


--
-- 5.0.1 Stellgrößen DWZ-Ermittlung
--

CREATE TABLE IF NOT EXISTS `#__clm_dwz_parameter` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL COMMENT 'Gültig ab',
  `r` int NOT NULL COMMENT 'Korrektur-Faktor bei Erst-DWZ',
  `t` int NOT NULL COMMENT 'Divisor bei Jugendaufschlag',
  `u` int NOT NULL COMMENT 'Verschiebung beim Bremsfaktor b',
  `a1` int NOT NULL COMMENT 'Erfolgsaufschlag Junioren',
  `a2` int NOT NULL COMMENT 'Erfolgsaufschlag Erwachsene',
  `kmax` int NOT NULL COMMENT 'Höchstwert des K-Faktors',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4 AUTO_INCREMENT=1 ;
