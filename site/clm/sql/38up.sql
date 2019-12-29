--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2019 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 3.8.1  Joomla 4 Code Anpassungen - Joomla-User-ID angepasst 
--
ALTER TABLE `#__clm_categories` 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_liga` 			MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_mannschaften` 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_rangliste_id` 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_rangliste_name` MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_rnd_man` 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_runden_termine` MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_saison` 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_swt_liga` 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_swt_mannschaften` 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_swt_rnd_man` 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_swt_turniere` 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_swt_turniere_rnd_termine` 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_swt_turniere_tlnr` 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_termine` 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_turniere` 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_turniere_rnd_termine` 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_turniere_sonderranglisten` 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_turniere_tlnr` 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_user`	 		MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__clm_vereine`	 	MODIFY `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
