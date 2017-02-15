--
-- 3.3.6  PKZ für Einzelturniere 
--
ALTER TABLE `#__clm_turniere_tlnr` ADD `PKZ` varchar(9) DEFAULT NULL AFTER `mgl_nr`;
ALTER TABLE `#__clm_swt_turniere_tlnr` ADD `PKZ` varchar(9) DEFAULT NULL AFTER `mgl_nr`;


