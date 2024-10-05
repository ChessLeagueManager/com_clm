<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');

$config_order = array(
			array("basics", 0, "countryversion", "lv", "menue", "dropdown", "vereineliste", "verein_sort", "liga_saison", "meldeliste", "rangliste","fe_submenu","fe_submenu_t","fe_display_lose_by_default","field_search","database_safe"),
			array("mail", 0,"email_from", "email_bcc", "email_fromname", "org_logo", "sl_mail", "email_type" , "show_sl_mail"),
			array("results", 0,"conf_ergebnisse", "meldung_heim", "meldung_verein", "kommentarfeld", "ikommentarfeld", "app_security"),
			array("register", 0, "conf_meldeliste", "pdf_meldelisten"),
			array("database", 0, "execute_swt", "upload_swt","import_pgn", "upload_pgn","dsb_import_amount","dewis_import_delay"),
			array("clubs", 0,  "verein_mail","verein_tel","conf_vereinsdaten","verein_fe_mail",
				array("club_list", 0, "fe_vereinsliste_vs","fe_vereinsliste_hpage","fe_vereinsliste_dwz","fe_vereinsliste_elo")),
			array("tournaments", 0, "tourn_linkclub", "tourn_showtlok"),

			array("singletournaments", 0, "tourn_ch_system",
				array("singletournaments_credentials", 0, "tourn_ch_server","tourn_ch_url","tourn_ch_key","tourn_ch_ip","tourn_ch_mail")),
			array("rounds", 0, "fe_runde_rang", "fe_runde_aktuell", "fe_runde_tln", "fe_runde_subuml"),
			array("teams", 0, "man_manleader", "man_mail", "man_tel", "man_mobil", "man_spiellokal", "man_spielplan", "man_showdwz"),
			array("externServices", 0, "googlemaps", 
				array("googlemaps", 0, "googlemaps_rtype", "googlemaps_ver", "googlemaps_vrout", "googlemaps_msch", "googlemaps_mrout", "maps_zoom", "maps_resolver", "googlemaps_api"), 
				array("googlecharts", 0, "googlecharts"),
				array("exp", 0, "tourn_comment_parse", "tourn_seed")),
			array("data_protection", 0, "privacy_notice", "view_archive", "user_member"),
			array("template", 0, "template","lesehilfe", "isis", "isis_remove_sidebar", "div", "cellin_top", "cellin_right", "cellin_bottom", "cellin_left", "border_length", "border_style", "border_color", "favicon",
				array("table", 0, "table_pageLength","table_fontSize", "button_style"),
				array("fix", 0, "fixth_msch", "fixth_dwz", "fixth_tkreuz", "fixth_ttab", "fixth_ttln"),
				array("team_overview", 0, "msch_nr", "msch_dwz", "msch_rnd", "msch_punkte", "msch_spiele", "msch_prozent"),
				array("background", 0, "tableth", "subth", "zeile1", "zeile2", "re_col"),
				array("fontcolor", 0, "tableth_s1", "tableth_s2"),
				array("pgn", 0, "fe_pgn_show", "fe_pgn_moveFont", "fe_pgn_commentFont", "fe_pgn_style"),
				array("upDown", 0,"rang_auf","rang_auf_evtl", "rang_ab", "rang_ab_evtl"),
				array("wrong", 0, "wrong1", "wrong2_length", "wrong2_style", "wrong2_color")),
			array("developer", 0, "soap_safe", "log", "log_error", "log_warning", "log_notice", "log_unknown", "log_info", "trial_and_error", "test_button", "email_suppress", "email_replace")
		);
?>
