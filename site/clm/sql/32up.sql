--
-- 3.6.6 Leistungssteigerung der DB-Zugriffe
--
ALTER TABLE #__clm_rnd_spl DROP INDEX sid_zps_spieler;
ALTER TABLE #__clm_rnd_spl ADD INDEX `lid_zps_spieler` (`lid`,`zps`,`spieler`);
ALTER TABLE #__clm_rnd_spl ADD INDEX `lid_dg_runde_paar` (`lid`,`dg`,`runde`,`paar`);

ALTER TABLE #__clm_rnd_man ADD INDEX `lid_dg_runde_paar` (`lid`,`dg`,`runde`,`paar`);

ALTER TABLE #__clm_meldeliste_spieler DROP INDEX sid_zps_mglnr;
ALTER TABLE #__clm_meldeliste_spieler ADD INDEX `lid_zps_mglnr` (`lid`,`zps`,`mgl_nr`);
