--
-- 3.1.9 Durchgang wird Ordnungsbegriff bei Einzelturnieren analog 1.5.4 in J!2.5
--
ALTER TABLE #__clm_swt_turniere_rnd_termine DROP INDEX turnier_runde;
ALTER TABLE #__clm_swt_turniere_rnd_termine ADD UNIQUE INDEX `turnier_dg_runde` (`swt_tid`,`dg`,`nr`);

ALTER TABLE #__clm_swt_turniere_rnd_spl DROP INDEX turnier_runde_brett_heim;
ALTER TABLE #__clm_swt_turniere_rnd_spl ADD UNIQUE INDEX `turnier_dg_runde_brett_heim` (`swt_tid`,`dg`,`runde`,`brett`,`heim`);
