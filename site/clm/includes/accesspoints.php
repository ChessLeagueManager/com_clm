<?php
defined('clm') or die('Restricted access');
// Alle Zugriffspunkte
//        0 / 1 / 2
// =0 -> Nein/Ja (ehemals NY)
// =1 -> Nein/Ja/Liga (ehemals NYL)
// =2 -> Nein/Ja/Turnier (ehemals NYT)
$accesspoints["BE_general_general"]=0;
$accesspoints["BE_season_general"]=0;
$accesspoints["BE_event_general"]=0;
$accesspoints["BE_event_delete"]=0;
$accesspoints["BE_tournament_general"]=0;
$accesspoints["BE_tournament_create"]=0;
$accesspoints["BE_tournament_edit_detail"]=2;
$accesspoints["BE_tournament_delete"]=0;
$accesspoints["BE_tournament_edit_round"]=2;
$accesspoints["BE_tournament_edit_result"]=2;
$accesspoints["BE_tournament_edit_fixture"]=2;
$accesspoints["BE_league_general"]=0;
$accesspoints["BE_league_create"]=0;
$accesspoints["BE_league_edit_detail"]=1;
$accesspoints["BE_league_delete"]=0;
$accesspoints["BE_league_edit_round"]=1;
$accesspoints["BE_league_edit_result"]=1;
$accesspoints["BE_league_edit_fixture"]=1;
$accesspoints["BE_teamtournament_general"]=0;
$accesspoints["BE_teamtournament_create"]=0;
$accesspoints["BE_teamtournament_edit_detail"]=2;
$accesspoints["BE_teamtournament_delete"]=0;
$accesspoints["BE_teamtournament_edit_round"]=2;
$accesspoints["BE_teamtournament_edit_result"]=2;
$accesspoints["BE_teamtournament_edit_fixture"]=2;
$accesspoints["BE_club_general"]=0;
$accesspoints["BE_club_create"]=0;
$accesspoints["BE_club_edit_member"]=0;
$accesspoints["BE_club_copy"]=0;
$accesspoints["BE_club_edit_ranking"]=0;
$accesspoints["BE_team_general"]=0;
$accesspoints["BE_team_create"]=0;
$accesspoints["BE_team_edit"]=1;
$accesspoints["BE_team_delete"]=0;
$accesspoints["BE_team_registration_list"]=1;
$accesspoints["BE_user_general"]=0;
$accesspoints["BE_user_copy"]=0;
$accesspoints["BE_accessgroup_general"]=0;
$accesspoints["BE_swt_general"]=0;
$accesspoints["BE_pgn_general"]=0;
$accesspoints["BE_elobase_general"]=0;
$accesspoints["BE_database_general"]=0;
$accesspoints["BE_logfile_general"]=0;
$accesspoints["BE_logfile_delete"]=0;
$accesspoints["BE_config_general"]=0;
?>
