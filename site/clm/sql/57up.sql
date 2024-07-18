--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2024 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de

--
-- 4.2.1  Anpassung checked_out_time wegen Globalen Freigeben
--

SET SESSION SQL_MODE='ALLOW_INVALID_DATES';
ALTER TABLE `#__clm_categories` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_categories` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_categories` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_liga` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_liga` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_liga` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_mannschaften` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_mannschaften` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_mannschaften` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_rangliste_id` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_rangliste_id` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_rangliste_id` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_rangliste_name` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_rangliste_name` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_rangliste_name` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_rnd_man` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_rnd_man` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_rnd_man` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_runden_termine` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_runden_termine` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_runden_termine` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_saison` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_saison` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_saison` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_swt_liga` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_liga` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_swt_liga` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_swt_mannschaften` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_mannschaften` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_swt_mannschaften` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_swt_rnd_man` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_rnd_man` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_swt_rnd_man` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_swt_runden_termine` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_runden_termine` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_swt_runden_termine` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_swt_turniere` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_turniere` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_swt_turniere` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_swt_turniere_rnd_termine` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_turniere_rnd_termine` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_swt_turniere_rnd_termine` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_swt_turniere_tlnr` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_swt_turniere_tlnr` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_swt_turniere_tlnr` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_termine` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_termine` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_termine` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_turniere` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_turniere` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_turniere` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_turniere_rnd_termine` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_turniere_rnd_termine` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_turniere_rnd_termine` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_turniere_sonderranglisten` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_turniere_sonderranglisten` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_turniere_sonderranglisten` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_turniere_tlnr` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_turniere_tlnr` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_turniere_tlnr` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_user` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_user` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_user` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';
ALTER TABLE `#__clm_vereine` MODIFY `checked_out` int(11) unsigned DEFAULT NULL;
ALTER TABLE `#__clm_vereine` MODIFY `checked_out_time` datetime DEFAULT NULL;
UPDATE `#__clm_vereine` SET checked_out_time = NULL, checked_out = NULL WHERE checked_out_time = '0000-00-00 00:00:00';



