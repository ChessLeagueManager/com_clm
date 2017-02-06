--
-- 3.3.6  Spieler Attribut 
--
ALTER TABLE `#__clm_meldeliste_spieler` ADD `attr` varchar(3) DEFAULT NULL AFTER `gesperrt`;
ALTER TABLE `#__clm_swt_meldeliste_spieler` ADD `attr` varchar(3) DEFAULT NULL AFTER `gesperrt`;


