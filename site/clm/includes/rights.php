<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2018 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');
// API mit Rechten versehen
// array(0,"<ACCESS>",true/false)
// true => Rechtelevel 1
// false => Rechtelevel 1 oder 2
// ---
// array(1,"<ACCESS1>","<ACCESS2>","<ACCESS3>"<switch_index>,<id_index>
// typische Kontrolle bei der team/tournament API
// ---
// array(2,"<ACCESS>",<id_index>)
// typische Kontrolle bei der tournament API
// ---
// array(3,array("<ACCESS>" => true/false,"<ACCESS>" => true/false))
// Alle müssen in Ordnung sein
// ---
// array(4,array("<ACCESS>" => true/false,"<ACCESS>" => true/false))
// Einer muss in Ordnung sein

$rights["db_season_save"]=array(0,"BE_season_general",true);
$rights["db_season_enable"]=array(0,"BE_season_general",true);
$rights["db_season_delete"]=array(0,"BE_season_general",true);
$rights["db_season_delDWZ"]=array(0,"BE_season_general",true);
$rights["db_season_genDWZ"]=array(0,"BE_season_general",true);
$rights["db_season_array"]=array();
$rights["view_dates_display"]=array();
$rights["view_database"]=array();
$rights["db_clubs"]=array();
$rights["db_dewis_player"]=array(0,"BE_database_general",true);
$rights["db_dewis_club"]=array(0,"BE_database_general",true);
$rights["db_dsb_player"]=array(0,"BE_database_general",true);
$rights["db_dsb_club"]=array(0,"BE_database_general",true);
$rights["db_ecf_player"]=array(0,"BE_database_general",true);
$rights["db_ecf_club"]=array(0,"BE_database_general",true);
$rights["db_ecf_org"]=array(0,"BE_database_general",true);
$rights["db_units"]=array();
$rights["view_units_form"]=array();
$rights["view_config"]=array(0,"BE_config_general",true);
$rights["db_config_get"]=array(0,"BE_config_general",true);
$rights["db_config_save"]=array(0,"BE_config_general",true);
$rights["db_config_reset"]=array(0,"BE_config_general",true);
$rights["db_report"]=array();
$rights["db_report_save"]=array();
$rights["db_report_overview"]=array();
$rights["view_report"]=array();
$rights["db_schedule"]=array();
$rights["view_schedule"]=array();
$rights["view_schedule_pdf"]=array();
$rights["view_schedule_xls"]=array();
$rights["db_paarungsliste"]=array();
$rights["view_paarungsliste_xls"]=array();
$rights["db_be_menu"]=array();
$rights["view_be_menu"]=array();
$rights["view_app_info"]=array();
$rights["view_logging"]=array(0,"BE_logfile_general",true);
$rights["db_logging"]=array(0,"BE_logfile_general",true);
$rights["view_logging_new"]=array(0,"BE_logfile_general",true);
$rights["db_logging_new"]=array(0,"BE_logfile_general",true);
$rights["db_logging_del"]=array(0,"BE_logfile_general",true);
$rights["db_lineup_attr"]=array();

//************************
// Turniere
//************************
$rights["view_tournament"]=array(0,"BE_tournament_general",true);
$rights["db_tournament"]=array(0,"BE_tournament_general",true);
$rights["view_tournament_group"]=array(4, array("BE_league_general" => true, "BE_teamtournament_general" => true));
$rights["db_tournament_group"]=array(4, array("BE_league_general" => true, "BE_teamtournament_general" => true));

$rights["db_tournament_genDWZ"]=array(1,"BE_tournament_edit_round","BE_league_edit_round","BE_teamtournament_edit_round",1,0); // inoff. DWZ generieren
$rights["db_tournament_delDWZ"]=array(1,"BE_tournament_edit_round","BE_league_edit_round","BE_teamtournament_edit_round",1,0); // inoff. DWZ löschen
$rights["db_tournament_updateDWZ"]=array(1,"BE_tournament_edit_detail","BE_league_edit_detail","BE_teamtournament_edit_detail",1,0); // DWZ des Turniers aktualisieren
$rights["db_tournament_copy"]=array(5,"BE_tournament_create","BE_league_create","BE_teamtournament_create",1,0); // Kopieren
$rights["db_tournament_genRounds"]=array(1,"BE_tournament_edit_round","BE_league_edit_round","BE_teamtournament_edit_round",1,0); // Runden generieren
$rights["db_tournament_delRounds"]=array(1,"BE_tournament_edit_round","BE_league_edit_round","BE_teamtournament_edit_round",1,0); // Runden Löschen
$rights["db_tournament_del"]=array(1,"BE_tournament_delete","BE_league_delete","BE_teamtournament_delete",1,0); // Löschen
$rights["db_tournament_publish"]=array(1,"BE_tournament_edit_detail","BE_league_edit_detail","BE_teamtournament_edit_detail",2,0); // Veröffentlichen, Sperren
$rights["db_tournament_ranking"]=array(1,"BE_tournament_edit_detail","BE_league_edit_detail","BE_teamtournament_edit_detail",1,0); // Rangliste aktualisieren
$rights["db_tournament_sortByTWZ"]=array(1,"BE_tournament_edit_detail","BE_league_edit_detail","BE_teamtournament_edit_detail",1,0); // Sortierung aktualisieren (nicht bei allen Modis notwendig)
$rights["db_tournament_auto"]=array(1,"BE_tournament_edit_detail","BE_league_edit_detail","BE_teamtournament_edit_detail",1,0); // Automatische Aktualisierung von inoff. DWZ und Ranking
$rights["db_tournament_player_del"]=array(2,"BE_tournament_edit_detail",0); // Entfernen von unbenutzten Spielern

$rights["db_ordering"]=array(); // Ordering ändern
$rights["db_pgn_export"]=array(); // pgn-Datei erstellen
$rights["db_pgn_template"]=array(); // pgn-Template erstellen
$rights["db_swm_import"]=array(); // Swiss-Manager-Datei importieren
$rights["db_dewis_user"]=array(); // Online Club Check für User
$rights["db_check_season_user"]=array(); // Online Season Check für Ligen und Turniere
?>
