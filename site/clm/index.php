<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.chessleaguemanager.de
*/
// CLM als aktiv markieren, Major.Minor.Patch:Datenbankversion
// WICHTIG: Gibt es eine neue Datenbankversion,
// müssen die Änderungen auch in der install.sql eingebracht werden.
define("clm", "4.1.3:55");
if (!defined("DS")) {
	define('DS', DIRECTORY_SEPARATOR);
}
// Absoluter Pfad zum Einbinden weiterer Dateien
$path = dirname(__FILE__);

// URL zum Einbinden von CSS, JS und Bildern
$url = strpos($_SERVER['PHP_SELF'], "administrator/index.php");
if (!$url) {
	$url = strpos($_SERVER['PHP_SELF'], "index.php");
	if (!$url) {
		$url = - 1;
	}
}
$url = ((empty($_SERVER['HTTPS'])) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, $url) . ((defined('_JEXEC')) ? 'components/com_clm/clm/' : '');

//$path = "/home/mlink/.owncloud/www/schachbezirk-siegerland/components/com_clm/clm/";
//$url = "http://localhost/~mlink/schachbezirk-siegerland/components/com_clm/clm/";

// CLM - Core initialisieren
require ("core.php");
new clm_core($url, $path);
if (!defined("clm_install")) {
	// Standalone - kein Joomla aktiv
	if (clm_core::$cms->getStatus() == 0) {
		// Automatisches Login durchführen
		if (isset($_GET["name"]) && isset($_GET["password"]) && $_GET["name"] != "" && $_GET["password"] != "") {
			if (!clm_core::$cms->login($_GET["name"], $_GET["password"])) {
				$login_failed = clm_core::$load->load_view("notification", array("e_loginFailed"));
				$title = clm_core::$cms->getTitle();
				$head = clm_core::$cms->getStyleScriptHead();
				$body = '<div class="clm">' . $login_failed[1] . '<div>';
				$fix = clm_core::$load->load_view("html", array($title, $head, $body), false);
				echo $fix[1];
				return;
			}
		}
		// Falls diese Datei direkt aufgerufen wird (ohne cms im Hintergrund) werden falls vorhanden Befehle usgeführt.
		if (isset($_POST["command"])) {
			echo clm_core::$load->execute_command();
		} else if (isset($_GET["view"])) {
			echo clm_core::$load->execute_view();
		}
	}
}

	//var_dump( clm_core::$api->db_tournament_updateDWZ(19,false));
	//var_dump(clm_core::$api->db_tournament_genDWZ(19,false));
	//var_dump(clm_core::$api->db_tournament_delDWZ(14,false));
	//var_dump(clm_core::$api->db_tournament_genDWZ(14,false));
	//clm_class_category::addCatToName
