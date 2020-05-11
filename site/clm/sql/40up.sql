--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2020 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 3.8.3  Joomla 4 Code Anpassungen - Joomla-User-ID angepasst 
--
ALTER TABLE `#__clm_categories`		MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00';
ALTER TABLE `#__clm_categories` 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_liga`			MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00';
ALTER TABLE `#__clm_liga` 			MODIFY `sl` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_liga` 			MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_mannschaften`	MODIFY `datum` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
									MODIFY `edit_datum` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
									MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00';
ALTER TABLE `#__clm_mannschaften` 	MODIFY `liste` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_mannschaften` 	MODIFY `edit_liste` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_mannschaften` 	MODIFY `mf` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_mannschaften` 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_rangliste_id`	MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00';
ALTER TABLE `#__clm_rangliste_id` 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_rangliste_name`	MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00';
ALTER TABLE `#__clm_rangliste_name` MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_rnd_man`		MODIFY `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
									MODIFY `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
									MODIFY `dwz_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
									MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
									MODIFY `pdate` date NOT NULL DEFAULT '1970-01-01';
ALTER TABLE `#__clm_rnd_man` 		MODIFY `gemeldet` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_rnd_man` 		MODIFY `editor` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_rnd_man` 		MODIFY `dwz_editor` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_rnd_man` 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_rnd_spl` 		MODIFY `gemeldet` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_rnd_spl` 		MODIFY `dwz_editor` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_runden_termine`	MODIFY `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
									MODIFY `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
									MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
									MODIFY `datum` date NOT NULL DEFAULT '1970-01-01',
									MODIFY `deadlineday` date NOT NULL DEFAULT '1970-01-01',
									MODIFY `enddatum` date NOT NULL DEFAULT '1970-01-01';
ALTER TABLE `#__clm_runden_termine` MODIFY `gemeldet` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_runden_termine` MODIFY `editor` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_runden_termine` MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_saison`			MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
									MODIFY `datum` date NOT NULL DEFAULT '1970-01-01';
ALTER TABLE `#__clm_saison` 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_swt_liga`		MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00';
ALTER TABLE `#__clm_swt_liga` 		MODIFY `sl` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_liga` 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_swt_mannschaften`	MODIFY `datum` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
										MODIFY `edit_datum` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
										MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00';
ALTER TABLE `#__clm_swt_mannschaften` 	MODIFY `liste` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_swt_mannschaften` 	MODIFY `edit_liste` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_swt_mannschaften` 	MODIFY `mf` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_mannschaften` 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_swt_rnd_man`		MODIFY `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
										MODIFY `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
										MODIFY `dwz_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
										MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00';
ALTER TABLE `#__clm_swt_rnd_man` 		MODIFY `gemeldet` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_rnd_man` 		MODIFY `editor` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_rnd_man` 		MODIFY `dwz_editor` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_rnd_man` 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_swt_rnd_spl` 		MODIFY `gemeldet` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_rnd_spl` 		MODIFY `dwz_editor` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_turniere`		MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00';
ALTER TABLE `#__clm_swt_turniere` 		MODIFY `tl` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_turniere` 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_swt_turniere_rnd_termine`	MODIFY `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
												MODIFY `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
												MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
												MODIFY `datum` date NOT NULL DEFAULT '1970-01-01';
ALTER TABLE `#__clm_swt_turniere_rnd_termine` 	MODIFY `gemeldet` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_turniere_rnd_termine` 	MODIFY `editor` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_turniere_rnd_termine` 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_swt_turniere_tlnr`	MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00';
ALTER TABLE `#__clm_swt_turniere_tlnr` 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_termine`		MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
									MODIFY `startdate` date NOT NULL DEFAULT '1970-01-01',
									MODIFY `enddate` date NOT NULL DEFAULT '1970-01-01';
ALTER TABLE `#__clm_termine` 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_turniere`		MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00';
ALTER TABLE `#__clm_turniere` 		MODIFY `tl` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_turniere` 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_turniere_rnd_termine`		MODIFY `zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
												MODIFY `edit_zeit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
												MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
												MODIFY `datum` date NOT NULL DEFAULT '1970-01-01';
ALTER TABLE `#__clm_turniere_rnd_termine` 		MODIFY `gemeldet` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_turniere_rnd_termine` 		MODIFY `editor` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_turniere_rnd_termine` 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_turniere_sonderranglisten`	MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00';
ALTER TABLE `#__clm_turniere_sonderranglisten` 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_turniere_tlnr`	MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00';
ALTER TABLE `#__clm_turniere_tlnr` 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_user`			MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00';
ALTER TABLE `#__clm_user`	 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_vereine`		MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00';
ALTER TABLE `#__clm_vereine`	 	MODIFY `vl` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_vereine`	 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
