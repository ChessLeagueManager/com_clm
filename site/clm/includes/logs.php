<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');
// Welche APIs sollen beim Aufruf geloggt werden?
$logs = array();
$logs[]="db_config_save";
$logs[]="db_dewis_club";
$logs[]="db_dewis_player";
$logs[]="db_ecfv2_club";
$logs[]="db_ecfv2_player";
$logs[]="db_dsb_club";
$logs[]="db_dsb_player";
$logs[]="db_ecf_club";
$logs[]="db_ecf_player";
$logs[]="db_ecf_org";
$logs[]="db_logging_del";
$logs[]="db_logging_del2";
$logs[]="db_report_save";
$logs[]="db_season_enable";
$logs[]="db_season_delete";
$logs[]="db_tournament_del";
$logs[]="db_tournament_player_del";
$logs[]="db_tournament_registration_del";
$logs[]="db_tournament_genDWZ";
$logs[]="db_tournament_delDWZ";
$logs[]="db_tournament_updateDWZ";
$logs[]="db_tournament_genRounds";
$logs[]="db_tournament_delRounds";
$logs[]="db_tournament_copy";
$logs[]="db_pgn_export";
$logs[]="db_pgn_template";
$logs[]="test_print";
$logs[]="db_trf_import";
$logs[]="db_swm_import";
$logs[]="db_swt_to_clm";
$logs[]="db_term_import";
$logs[]="db_mail_save";
?>
