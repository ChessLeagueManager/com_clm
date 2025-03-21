--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2025 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.2.4  Umstellung auf ENGINE = InnoDB
--

ALTER TABLE #__clm_categories  ENGINE = InnoDB;
ALTER TABLE #__clm_config  ENGINE = InnoDB;
ALTER TABLE #__clm_dwz_spieler  ENGINE = InnoDB;
ALTER TABLE #__clm_dwz_vereine  ENGINE = InnoDB;
ALTER TABLE #__clm_dwz_verbaende  ENGINE = InnoDB;
ALTER TABLE #__clm_ergebnis  ENGINE = InnoDB;
ALTER TABLE #__clm_liga  ENGINE = InnoDB;
ALTER TABLE #__clm_logging  ENGINE = InnoDB;
ALTER TABLE #__clm_mannschaften  ENGINE = InnoDB;
ALTER TABLE #__clm_meldeliste_spieler  ENGINE = InnoDB;
ALTER TABLE #__clm_online_registration  ENGINE = InnoDB;
ALTER TABLE #__clm_pgn  ENGINE = InnoDB;
ALTER TABLE #__clm_player_decode  ENGINE = InnoDB;
ALTER TABLE #__clm_rangliste_id  ENGINE = InnoDB;
ALTER TABLE #__clm_rangliste_name  ENGINE = InnoDB;
ALTER TABLE #__clm_rangliste_spieler  ENGINE = InnoDB;
ALTER TABLE #__clm_rnd_man  ENGINE = InnoDB;
ALTER TABLE #__clm_rnd_spl  ENGINE = InnoDB;
ALTER TABLE #__clm_runden_termine  ENGINE = InnoDB;
ALTER TABLE #__clm_saison  ENGINE = InnoDB;
ALTER TABLE #__clm_swt_dwz_spieler  ENGINE = InnoDB;
ALTER TABLE #__clm_swt_liga ENGINE = InnoDB;
ALTER TABLE #__clm_swt_mannschaften ENGINE = InnoDB;
ALTER TABLE #__clm_swt_meldeliste_spieler ENGINE = InnoDB;
ALTER TABLE #__clm_swt_rnd_man ENGINE = InnoDB;
ALTER TABLE #__clm_swt_rnd_spl ENGINE = InnoDB;
ALTER TABLE #__clm_swt_runden_termine ENGINE = InnoDB;
ALTER TABLE #__clm_swt_turniere ENGINE = InnoDB;
ALTER TABLE #__clm_swt_turniere_rnd_spl ENGINE = InnoDB;
ALTER TABLE #__clm_swt_turniere_rnd_termine ENGINE = InnoDB;
ALTER TABLE #__clm_swt_turniere_teams ENGINE = InnoDB;
ALTER TABLE #__clm_swt_turniere_tlnr ENGINE = InnoDB;
ALTER TABLE #__clm_swt_spl ENGINE = InnoDB;
ALTER TABLE #__clm_swt_man ENGINE = InnoDB;
ALTER TABLE #__clm_swt_spl_nach ENGINE = InnoDB;
ALTER TABLE #__clm_swt_spl_tmp ENGINE = InnoDB;
ALTER TABLE #__clm_termine ENGINE = InnoDB;
ALTER TABLE #__clm_turniere ENGINE = InnoDB;
ALTER TABLE #__clm_turniere_rnd_spl ENGINE = InnoDB;
ALTER TABLE #__clm_turniere_rnd_termine ENGINE = InnoDB;
ALTER TABLE #__clm_turniere_sonderranglisten ENGINE = InnoDB;
ALTER TABLE #__clm_turniere_teams ENGINE = InnoDB;
ALTER TABLE #__clm_turniere_tlnr ENGINE = InnoDB;
ALTER TABLE #__clm_user ENGINE = InnoDB;
ALTER TABLE #__clm_usertype ENGINE = InnoDB;
ALTER TABLE #__clm_vereine ENGINE = InnoDB;
ALTER TABLE #__clm_arbiter ENGINE = InnoDB;
ALTER TABLE #__clm_arbiterlicense ENGINE = InnoDB;
ALTER TABLE #__clm_arbiter_arbiterlicense ENGINE = InnoDB;
ALTER TABLE #__clm_arbiter_turnier ENGINE = InnoDB;