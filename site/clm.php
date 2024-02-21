<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
 */
// kein direkter Zugriff
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_clm" . DIRECTORY_SEPARATOR . "clm" . DIRECTORY_SEPARATOR . "index.php");

// Fix für empfindliche Server //
$query = "SET SQL_BIG_SELECTS=1";
clm_core::$db->query($query);	
// Fix für empfindliche Server //

if (clm_core::$access->getSeason() != -1) {

	// lädt Funktion zum sichern vor SQL-Injektion
	require_once (JPATH_COMPONENT . DS . 'includes' . DS . 'escape.php');
	// lädt alle CLM-Klassen - quasi autoload
	$classpath = dirname(__FILE__) . DS . 'classes';
	jimport('joomla.filesystem.folder');
	foreach (JFolder::files($classpath) as $file) {
		JLoader::register(str_replace('.class.php', '', $file), $classpath . DS . $file);
	}

	$view = clm_core::$load->request_string('view', '-1');
	$format = clm_core::$load->request_string('format', '-1');
	// Ergebnismeldung abfangen
	if($view == "meldung"){	
		// API aufrufen, Parameter (GET) werden automatisch zugeordnet --> site/com_clm/clm/includes/bindings.php
		$fix = clm_core::$api->callStandalone("view_report");
			// Fehlerfall
			if(!$fix[0]) {
				// Spezialbehandlung, Umleitung auf die enstprechende Runde
				if($fix[1] == "e_reportUnpublished" || $fix[1] == "e_reportAlready") {	
					$error = clm_core::$load->load_view("notification", array($fix[1],false));	
					$link = "index.php?option=com_clm&view=paarungsliste&saison=".$fix[2][1]."&liga=".$fix[2][0];
					$mainframe	= JFactory::getApplication();
					$mainframe->redirect( $link, $error[1], $error[0]);
				// sonst Fehlermeldung ausgeben
				} else {
					$error = clm_core::$load->load_view("notification", array($fix[1]));	
					echo '<div class="clm">'.$error[1].'</div>';
				}
			} else {
				// Aufruf war erfolgreich
				echo $fix[2];
			}
			// View wurde bereits ausgegeben -> wir sind fertig
			return;
	} 
	elseif ($view == "paarungsliste" AND $format == "xls") {
			clm_core::$api->callStandalone("view_paarungsliste_xls");
			return;
	}
	elseif ($view == "terminliste" AND $format == "xls") {
			clm_core::$api->callStandalone("view_terminliste_xls");
			return;
	}
	elseif ($view == "termine" AND $format == "ics") {
			clm_core::$api->callStandalone("view_termine_ics");
			return;
	}
	elseif ($view == "schedule") {
			if ($format == "pdf") {
				clm_core::$api->callStandalone("view_schedule_pdf");
			} elseif ($format == "xls") {
				clm_core::$api->callStandalone("view_schedule_xls");
			} elseif ($format == "ics") {
				clm_core::$api->callStandalone("view_schedule_ics");
			} else {
				$fix = clm_core::$api->callStandalone("view_schedule");
				echo $fix[2]; 
			}
			return;
	}
	elseif ($view == "app_info"){	
			$fix = clm_core::$api->view_app_info();
			echo $fix[2];
			return;
	}

	// laden des Joomla! Basis Controllers
	require_once (JPATH_COMPONENT . DS . 'controller.php');
	$controller = clm_core::$load->request_string('controller', '');
	// laden von weiteren Controllern
	if ($controller = clm_core::$load->request_string('controller', '')) {
		$path = JPATH_COMPONENT . DS . 'controllers' . DS . $controller . '.php';
		if (file_exists($path)) {
			require_once $path;
		} else {
			$controller = '';
		}
	}

//	if((!isset($_GET["format"]) || $_GET["format"]!="pdf") &&
	if((!isset($_GET["format"]) || ($_GET["format"]!="pdf" && $_GET["format"]!="svg")) &&
		(!isset($_GET["pgn"]) || $_GET["pgn"]==0)) { echo '<div class="clm">'; }	
	// Erzeugen eines Objekts der Klasse controller
	$classname = 'CLMController' . ucfirst($controller);
	$controller = new $classname();
	// den request task ausleben
	$controller->execute(clm_core::$load->request_string('task'));
	// Redirect aus dem controller
	$controller->redirect();
//	if((!isset($_GET["format"]) || $_GET["format"]!="pdf") &&
	if((!isset($_GET["format"]) || ($_GET["format"]!="pdf" && $_GET["format"]!="svg")) &&
		(!isset($_GET["pgn"]) || $_GET["pgn"]==0)) { echo '</div>'; }
} else {
//	if((!isset($_GET["format"]) || $_GET["format"]!="pdf") &&
	if((!isset($_GET["format"]) || ($_GET["format"]!="pdf" && $_GET["format"]!="svg")) &&
		(!isset($_GET["pgn"]) || $_GET["pgn"]==0)) { echo '<div class="clm">'; }	
	$fix = clm_core::$load->load_view("notification", array("e_noSeason"));
	echo $fix[1];
//	if((!isset($_GET["format"]) || $_GET["format"]!="pdf") &&
	if((!isset($_GET["format"]) || ($_GET["format"]!="pdf" && $_GET["format"]!="svg")) &&
		(!isset($_GET["pgn"]) || $_GET["pgn"]==0)) { echo '</div>'; }
}
?>
