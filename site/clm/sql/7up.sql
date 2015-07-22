--
-- 3.1.14 Der SWT Import sollte auch deaktivierte Spieler als solche eintragen
--
ALTER TABLE `#__clm_swt_turniere_tlnr` ADD `tlnrStatus` TINYINT(1) UNSIGNED NOT NULL AFTER `rankingPos`;
