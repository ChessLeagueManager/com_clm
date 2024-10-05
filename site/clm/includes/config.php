<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');
// aus clm_function_make_valid entnommen
// 0 -> Ganzzahl
// 1 -> keine Ganzzahl
// 2 -> Alle Zahlen
// 3 -> gültiger Timestamp
// 4 -> Farben für CSS wie fff oder af24df ohne #
// 5 -> Opacity für CSS (Zahl zwischen 0 und 1, auch Kommazahlen)
// 6 -> escaping der jeweiligen Datenbank (nur Standard falls null)
// 7 -> XSS Filter, HTML Tags filtern
// 8 -> zusätzlich Sonderzeichen mit HTML-Entsprechung ersetzen
// 9 -> ist eines der Elemente der Auswahl (Vordefiniert)
//10 -> Datum für die Datenbank
//11 -> ist eines der Elemente der Auswahl (API)
//12 -> E-Mail
//13 -> Länge
//14 -> URL
// Bei ungültigen Typ wird stets der Standardwert zurückgegeben!
$config["countryversion"]=array(0,9,"de",array("de","en"));
$config["lv"]=array(1,11,"000",array("db_units",array(true,true)));
$config["menue"]=array(2,9,1,array(0,1));
$config["dropdown"]=array(3,9,1,array(0,1));
$config["vereineliste"]=array(4,9,0,array(0,1));
$config["verein_sort"]=array(5,9,1,array(0,1));
//$config["logfile"]=array(6,9,1,array(0,1));
//$config["dwz_fe"]=array(8,9,1,array(0,1));
$config["liga_saison"]=array(9,9,0,array(0,1));
$config["sl_mail"]=array(11,9,0,array(0,1));
$config["meldeliste"]=array(12,9,0,array(0,1));
$config["rangliste"]=array(13,9,0,array(0,1,2));
$config["kommentarfeld"]=array(14,9,0,array(0,1,2));
$config["email_from"]=array(15,12,"",null);
$config["email_bcc"]=array(16,12,"",null);
$config["email_fromname"]=array(17,7,"",null);
$config["org_logo"]=array(18,8,"",null);
$config["fe_pgn_show"]=array(19,4,"666666",null);
$config["fe_pgn_moveFont"]=array(20,4,"666666",null);
$config["upload_swt"]=array(21,9,0,array(0,1));
$config["execute_swt"]=array(22,9,0,array(0,1));
$config["conf_ergebnisse"]=array(23,9,0,array(0,1));
$config["meldung_heim"]=array(24,9,0,array(0,1));
$config["meldung_verein"]=array(25,9,0,array(0,1));
$config["conf_meldeliste"]=array(26,9,0,array(0,1));
$config["pdf_meldelisten"]=array(27,9,0,array(0,1));
$config["fe_submenu"]=array(28,9,0,array(0,1));
$config["fe_submenu_t"]=array(29,9,0,array(0,1));
$config["conf_vereinsdaten"]=array(30,9,0,array(0,1));
$config["man_manleader"]=array(31,9,1,array(0,1));
$config["man_mail"]=array(32,9,1,array(0,1));
$config["man_tel"]=array(33,9,1,array(0,1));
$config["man_mobil"]=array(34,9,1,array(0,1));
$config["man_spiellokal"]=array(35,9,1,array(0,1));
$config["man_spielplan"]=array(36,9,1,array(0,1));
$config["man_showdwz"]=array(37,9,1,array(0,1));
$config["fe_vereinsliste_vs"]=array(38,9,1,array(0,1));
$config["fe_vereinsliste_hpage"]=array(39,9,1,array(0,1));
$config["fe_vereinsliste_dwz"]=array(40,9,1,array(0,1));
$config["fe_vereinsliste_elo"]=array(41,9,1,array(0,1));
$config["fe_runde_rang"]=array(42,9,1,array(0,1));
$config["fe_runde_aktuell"]=array(43,9,1,array(0,1));
$config["fe_runde_tln"]=array(44,9,1,array(0,1));
$config["fe_pgn_style"]=array(46,9,"png",array("default","kent","png","zurich"));
$config["fixth_msch"]=array(47,9,1,array(0,1));
$config["fixth_dwz"]=array(48,9,1,array(0,1));
$config["fixth_tkreuz"]=array(49,9,1,array(0,1));
$config["fixth_ttab"]=array(50,9,1,array(0,1));
$config["fixth_ttln"]=array(51,9,1,array(0,1));
$config["fe_display_lose_by_default"]=array(52,9,0,array(0,1));
$config["googlemaps"]=array(53,9,0,array(0,1));
$config["googlemaps_api"]=array(54,8,"",null);
$config["googlemaps_rtype"]=array(55,9,0,array(0,1,2,3));
$config["googlemaps_ver"]=array(56,9,1,array(0,1,3));
$config["googlemaps_vrout"]=array(57,9,1,array(0,1));
$config["googlemaps_msch"]=array(58,9,1,array(0,1,3));
$config["googlemaps_mrout"]=array(59,9,1,array(0,1));
$config["googlecharts"]=array(60,9,0,array(0,1));
$config["tourn_linkclub"]=array(61,9,1,array(0,1));
$config["tourn_showtlok"]=array(62,9,0,array(0,1));
$config["cl_config"]=array(63,8,"0.0.0",null);
$config["db_config"]=array(64,8,"0",null);
$config["template"]=array(66,9,1,array(0,1));
$config["lesehilfe"]=array(67,9,1,array(0,1));
$config["border_length"]=array(68,13,"1px",null); 
$config["tableth"]=array(70,4,"333333",null); 
$config["subth"]=array(71,4,"F5F5F5",null); 
$config["zeile1"]=array(72,4,"FFFFFF",null); 
$config["zeile2"]=array(73,4,"F3F3F3",null); 
$config["re_col"]=array(74,4,"FFFFCC",null); 
$config["tableth_s1"]=array(75,4,"FFFFFF",null); 
$config["tableth_s2"]=array(76,4,"666666",null); 
$config["rang_auf"]=array(77,4,"C0DF82",null); 
$config["rang_auf_evtl"]=array(78,4,"DCEDBA",null); 
$config["rang_ab"]=array(79,4,"E7A9A9",null); 
$config["rang_ab_evtl"]=array(80,4,"F3D6D6",null); 
$config["wrong1"]=array(81,4,"FFFF99",null); 
$config["wrong2_length"]=array(82,13,"1px",null); 
$config["msch_nr"]=array(83,13,"45px",null); 
$config["msch_dwz"]=array(84,13,"40px",null); 
$config["msch_rnd"]=array(85,13,"30px",null); 
$config["msch_punkte"]=array(86,13,"35px",null); 
$config["msch_spiele"]=array(87,13,"35px",null); 
$config["msch_prozent"]=array(89,13,"35px",null); 	
$config["fe_pgn_commentFont"]=array(90,4,"888888",null);
$config["wrong2_style"]=array(91,9,"solid",array("none","hidden","dotted","dashed","solid","double","groove","ridge","inset","outset","initial","inherit")); 
$config["wrong2_color"]=array(92,4,"FFCC66",null); 
$config["border_style"]=array(93,9,"solid",array("none","hidden","dotted","dashed","solid","double","groove","ridge","inset","outset","initial","inherit"));
$config["border_color"]=array(94,4,"CCCCCC",null); 
$config["cellin_left"]=array(95,13,"0.1em",null); 
$config["cellin_right"]=array(96,13,"0.1em",null); 
$config["cellin_bottom"]=array(97,13,"0.2em",null);
$config["cellin_top"]=array(98,13,"0.2em",null); 
$config["isis"]=array(99,9,1,array(0,1,2));
$config["div"]=array(100,9,1,array(0,1));
$config["favicon"]=array(101,14,"",null);
$config["status_cache"]=array(102,0,0,null);
$config["status_cache_content"]=array(103,6,"",null);
$config["app_security"]=array(104,9,0,array(0,1,2));
$config["isis_remove_sidebar"]=array(105,9,1,array(0,1,2));
$config["table_pageLength"]=array(106,9,"50",array("10","25","50","100","-1"));
$config["button_style"]=array(107,9,0,array(0,1));
$config["table_fontSize"]=array(108,13,"13px",null);
$config["tourn_comment_parse"]=array(109,9,0,array(0,1)); 
$config["tourn_seed"]=array(110,0,0,null);
$config["soap_safe"]=array(111,9,1,array(0,1));
$config["dsb_import_amount"]=array(112,0,100,null);
$config["log_error"]=array(113,9,1,array(0,1));
$config["log_warning"]=array(114,9,0,array(0,1)); 
$config["log_notice"]=array(115,9,0,array(0,1)); 
$config["log_unknown"]=array(116,9,0,array(0,1));
$config["log_info"]=array(117,9,1,array(0,1));  
$config["log"]=array(118,9,1,array(0,1));
$config["database_safe"]=array(119,9,1,array(0,1));
$config["fe_runde_subuml"]=array(120,9,0,array(0,1));
$config["email_type"]=array(121,9,1,array(0,1));
$config["upload_pgn"]=array(122,9,0,array(0,1));
$config["import_pgn"]=array(123,9,0,array(0,1));
$config["test_button"]=array(124,9,0,array(0,1)); 
$config["view_archive"]=array(125,9,0,array(0,1,2));
$config["user_member"]=array(126,9,0,array(0,1)); 
$config["privacy_notice"]=array(127,14,"",null);
$config["trial_and_error"]=array(128,9,0,array(0,1)); 
$config["dewis_import_delay"]=array(129,0,2500,null);
$config["maps_resolver"]=array(130,9,2,array(1,2));
$config["maps_zoom"]=array(131,9,15,array(11,12,13,14,15,16));
$config["ikommentarfeld"]=array(132,9,0,array(0,1,2));
$config["show_sl_mail"]=array(133,9,1,array(0,1));
$config["tourn_ch_system"]=array(134,9,0,array(0,1,2,3));
$config["tourn_ch_server"]=array(135,7,"",null);
$config["tourn_ch_url"]=array(136,7,"",null);
$config["tourn_ch_key"]=array(137,7,"",null);
$config["tourn_ch_ip"]=array(138,7,"",null);
$config["tourn_ch_mail"]=array(139,7,"",null);
$config["verein_mail"]=array(140,9,0,array(0,1));
$config["verein_tel"]=array(141,9,0,array(0,1));
$config["verein_fe_mail"]=array(142,9,0,array(0,1));
$config["field_search"]=array(143,9,0,array(0,1));
$config["email_suppress"]=array(144,9,0,array(0,1,2));
$config["email_replace"]=array(145,12,"",null);
?>
