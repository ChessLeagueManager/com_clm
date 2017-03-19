<?php
defined('clm') or die('Restricted access');
// Welche APIs sollen beim Aufruf geloggt werden?
$logs = array();
$logs[]="db_config_save";
$logs[]="db_dewis_club";
$logs[]="db_dewis_player";
$logs[]="db_dsb_club";
$logs[]="db_dsb_player";
$logs[]="db_ecf_club";
$logs[]="db_ecf_player";
$logs[]="db_ecf_org";
$logs[]="db_logging_del";
$logs[]="db_report_save";
$logs[]="db_season_enable";
$logs[]="db_season_delete";
$logs[]="db_tournament_del";
$logs[]="db_tournament_player_del";
$logs[]="db_tournament_genDWZ";
$logs[]="db_tournament_delDWZ";
$logs[]="db_tournament_updateDWZ";
$logs[]="db_tournament_genRounds";
$logs[]="db_tournament_delRounds";
$logs[]="db_tournament_copy";
$logs[]="db_pgn_export";
$logs[]="db_pgn_template";
?>
