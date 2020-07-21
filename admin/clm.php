<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
if(isset($_GET["view"]) && $_GET["view"]=="forceUpdate") {
	JToolBarHelper::title('forceUpdate');
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_clm".DIRECTORY_SEPARATOR."installer.php");
	$installer = new com_clmInstallerScript();
	if($installer->preflight("install", null)) {
		if($installer->install(null)) {
			echo "The DB should work!";
		}
	}
} else if(isset($_GET["view"]) && $_GET["view"]=="forceFullUpdate") {
	JToolBarHelper::title('forceFullUpdate');
	require_once (JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_clm".DIRECTORY_SEPARATOR."clm".DIRECTORY_SEPARATOR."index.php");
	clm_core::$db->config()->db_config = 0; // eingetragene Version zurücksetzen
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_clm".DIRECTORY_SEPARATOR."installer.php");
	$installer = new com_clmInstallerScript();
	if($installer->preflight("install", null)) {
		if($installer->install(null)) {
			echo "The DB should work!!";
		}
	}
} else {

// no direct access
defined('_JEXEC') or die('Restricted access');
// Bei Standalone Verbindung wird das Backend Login verwendet
$_GET["clm_backend"]=1;
// Bei Standalone Verbindung wird die Anmeldesprache aus Joomla verwendet ?!?
$jlang = JFactory::getLanguage(); 
$_GET["session_language"] = $jlang->getTag();

if(substr(JVERSION,0,1)>2) {
	$GLOBALS["clm"]["grid.checkall"] = JHtml::_('grid.checkall');
} else {
	$GLOBALS["clm"]["grid.checkall"] = '<input type="checkbox" name="toggle" value="" onclick="checkAll(this);" />';
}


// erstellt DS und kümmert sich um die Rechteverwaltung
require_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_clm".DIRECTORY_SEPARATOR."clm".DIRECTORY_SEPARATOR."index.php");
// lädt Funktion zum sichern vor SQL-Injektion
require_once(JPATH_SITE.DS."components".DS."com_clm".DS."includes".DS."escape.php");

// Fix für empfindliche Server //
$query = "SET SQL_BIG_SELECTS=1";
clm_core::$db->query($query);
// Fix für empfindliche Server //

// Fix assets - group Manager - Adminzugriff erlauben (Joomla und CLM)
	$query = "SELECT * FROM #__assets WHERE name = 'com_clm' AND parent_id = 1 ";
	$clm_assets	= clm_core::$db->loadObjectList($query);

	$rules_test = '{"core.admin":[],"core.manage":[],"core.manage.clm":[],"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[]}'; 
	if ((count($clm_assets) == 1) AND (($clm_assets[0]->rules == '{}') OR ($clm_assets[0]->rules == $rules_test))) 
	{
		$query = "UPDATE #__assets SET rules='".'{\"core.admin\":[],\"core.manage\":{\"6\":1},\"core.manage.clm\":{\"6\":1},\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'."' WHERE name = 'com_clm' AND parent_id = 1 ";
		clm_core::$db->query($query);
	}


if (clm_core::$access->getSeason() != -1) {
	
$app            = JFactory::getApplication();
$template = $app->getTemplate('template')->template;	
$config = clm_core::$db->config();

if($config->isis_remove_sidebar>0 && ($config->isis_remove_sidebar==2 || $template=="isis")) {
	clm_core::$load->load_css("isis_fix");
}
if($config->isis>0 && ($config->isis==2 || $template=="isis"))
{
	$document = JFactory::getDocument();
	$document->addStyleSheet("../components/com_clm/includes/clm_isis.css");
}

// Pfad zum JS-Verzeichnis
DEFINE ('CLM_PATH_JAVASCRIPT', 'components'.DS.'com_clm'.DS.'javascript'.DS);
// Set the table directory
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'tables');


$clmAccess = clm_core::$access;

// Parameter auslesen 
$config = clm_core::$db->config();
$val=$config->menue;
$countryversion = $config->countryversion;

	// Joomla-Version ermitteln
	$version = new JVersion();
	$joomlaVersion = $version->getShortVersion();

if (substr($joomlaVersion,0,1) > 3) {  ?>
		<div id="j-sidebar-container" >
			<div>
				<ul id="submenu" class="nav nav-list">
					<li>
						<a href="index.php?option=com_clm&amp;view=info">Info&nbsp;&nbsp;</a>
					</li>
					<li>
						<a href="index.php?option=com_clm&amp;section=saisons">Saison&nbsp;&nbsp;</a>
					</li>
					<li>
						<a href="index.php?option=com_clm&amp;view=terminemain">Veranstaltungen&nbsp;&nbsp;</a>
					</li>
					<li>
						<a href="index.php?option=com_clm&amp;view=view_tournament">Turniere&nbsp;&nbsp;</a>
					</li>
					<li>
						<a href="index.php?option=com_clm&amp;view=view_tournament_group&amp;liga=1">Ligen&nbsp;&nbsp;</a>
					</li>
					<li>
						<a href="index.php?option=com_clm&amp;view=view_tournament_group&amp;liga=0">Mannschaftsturniere&nbsp;&nbsp;</a>
					</li>
					<li>
						<a href="index.php?option=com_clm&amp;section=vereine">Vereine&nbsp;&nbsp;</a>
					</li>
					<li>
						<a href="index.php?option=com_clm&amp;section=mannschaften">Mannschaften&nbsp;&nbsp;</a>
					</li>
					<li>
						<a href="index.php?option=com_clm&amp;section=users">Benutzer&nbsp;&nbsp;</a>
					</li>
					<li>
						<a href="index.php?option=com_clm&amp;view=swt">Importe&nbsp;&nbsp;</a>
					</li>
					<li>
						<a href="index.php?option=com_clm&amp;view=auswertung">DeWIS&nbsp;&nbsp;</a>
					</li>
					<li>
						<a href="index.php?option=com_clm&amp;view=db">Datenbank&nbsp;&nbsp;</a>
					</li>
					<li>
						<a href="index.php?option=com_clm&amp;view=view_logging">Logging&nbsp;&nbsp;</a>
					</li>
					<li>
						<a href="index.php?option=com_clm&amp;view=view_config">Einstellungen</a>
					</li>
				</ul>
			</div>
		</div> <?php 
}
if (substr($joomlaVersion,0,1) < 4) {
JSubMenuHelper::addEntry(JText::_('INFO'), 'index.php?option=com_clm&view=info', (clm_core::$load->request_string('view')) == 'info'?true:false);
				
if ($val == 0) {
	JSubMenuHelper::addEntry(JText::_('ERGEBNISSE'),  'index.php?option=com_clm&section=ergebnisse', (clm_core::$load->request_string('section')) == 'ergebnisse'?true:false); 
}
if($clmAccess->access('BE_season_general')) {
	JSubMenuHelper::addEntry(JText::_('SAISON'), 'index.php?option=com_clm&section=saisons', (clm_core::$load->request_string('section')) == 'saisons'?true:false);
}
if($clmAccess->access('BE_event_general')) {
	JSubMenuHelper::addEntry(JText::_('TERMINE'), 'index.php?option=com_clm&view=terminemain', (clm_core::$load->request_string('view')) == 'terminemain'?true:false);
}
//if ($countryversion =="de") {
if($clmAccess->access('BE_tournament_general')) {
	JSubMenuHelper::addEntry(JText::_('TURNIERE'), 'index.php?option=com_clm&view=view_tournament', (clm_core::$load->request_string('view')) == 'turmain'?true:false);
}  //}
if($clmAccess->access('BE_league_general')) {
	JSubMenuHelper::addEntry(JText::_('LIGEN'), 'index.php?option=com_clm&view=view_tournament_group&liga=1', (clm_core::$load->request_string('section')) == 'ligen'?true:false);
}
if($clmAccess->access('BE_teamtournament_general')) {
	JSubMenuHelper::addEntry(JText::_('MTURNIERE'), 'index.php?option=com_clm&view=view_tournament_group&liga=0', (clm_core::$load->request_string('section')) == 'mturniere'?true:false); //mtmt
}
if ($val == 0) {
	JSubMenuHelper::addEntry(JText::_('SPIELTAGE'), 'index.php?option=com_clm&section=runden', (clm_core::$load->request_string('section')) == 'runden'?true:false);
}
if($clmAccess->access('BE_club_general')) {
	JSubMenuHelper::addEntry(JText::_('VEREINE'), 'index.php?option=com_clm&section=vereine', (clm_core::$load->request_string('section')) == 'vereine'?true:false);
}
if($clmAccess->access('BE_team_general')) {
	JSubMenuHelper::addEntry(JText::_('MANNSCHAFTEN'), 'index.php?option=com_clm&section=mannschaften', (clm_core::$load->request_string('section')) == 'mannschaften'?true:false);
}
if($clmAccess->access('BE_user_general')) {
	JSubMenuHelper::addEntry(JText::_('USER'), 'index.php?option=com_clm&section=users', (clm_core::$load->request_string('section')) == 'users'?true:false);
}
//if ($countryversion =="de") {
if($clmAccess->access('BE_swt_general')) {
	JSubMenuHelper::addEntry(JText::_('SWT'), 'index.php?option=com_clm&view=swt', (clm_core::$load->request_string('view')) == 'swt'?true:false);
} //}
if ($countryversion =="de") {
if($clmAccess->access('BE_dewis_general')) {
	JSubMenuHelper::addEntry(JText::_('DeWIS'), 'index.php?option=com_clm&view=auswertung', (clm_core::$load->request_string('view')) == 'auswertung'?true:false);
}}
if ($countryversion =="en") {
if($clmAccess->access('BE_dewis_general')) {
	JSubMenuHelper::addEntry(JText::_('GRADING_EXPORT'), 'index.php?option=com_clm&view=auswertung', (clm_core::$load->request_string('view')) == 'auswertung'?true:false);
}}
if($clmAccess->access('BE_database_general')) {
	JSubMenuHelper::addEntry(JText::_('DATABASE'), 'index.php?option=com_clm&view=db', (clm_core::$load->request_string('view')) == 'db'?true:false);
}
if($clmAccess->access('BE_logfile_general')) {
	JSubMenuHelper::addEntry(JText::_('LOGFILE'), 'index.php?option=com_clm&view=view_logging', (clm_core::$load->request_string('view')) == 'view_logging'?true:false);
}
if($clmAccess->access('BE_config_general')) {
	JSubMenuHelper::addEntry(JText::_('CONFIG_TITLE'), 'index.php?option=com_clm&view=view_config', (clm_core::$load->request_string('view')) == 'view_config'?true:false);
}
}
// diese Seiten sind mit jeglichem Zugang möglich (clm_core::$access->getType() != "0")
$arrayAccessSimple = array('ergebnisse', 'runden', 'vereine', 'meldelisten', 'ranglisten', 'gruppen', 'mannschaften', 'users', 'check');

$controllerName = clm_core::$load->request_string('section');
if (in_array($controllerName, $arrayAccessSimple)) { // jeglicher Zugang
	if (clm_core::$access->getType() != "") {
		$controllerName = $controllerName;
	} else {
		$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
		$controllerName = 'info';
	}

} else {
 
switch ($controllerName) {
	case 'ergebnisse':
		if(!$clmAccess->access('BE_league_edit_result')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;
	case 'saisons':
		if(!$clmAccess->access('BE_season_general')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;
	case 'ligen';
		if(!$clmAccess->access('BE_league_general')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;
	case 'mturniere';
		if(!$clmAccess->access('BE_teamtournament_general')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;
	case 'paarung';
		if(!$clmAccess->access('BE_league_edit_fixture')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;
	case 'pairingdates';
		if(!$clmAccess->access('BE_league_edit_fixture')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;
	case 'paarungsliste';
		if(!$clmAccess->access('BE_league_edit_fixture')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;
	case 'dewis';
		if(!$clmAccess->access('BE_dewis_general')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;
	case 'runden';
		if(!$clmAccess->access('BE_league_edit_round')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;
	case 'vereine';
		if(!$clmAccess->access('BE_league_edit_round')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;
  case 'meldelisten';
		if(!$clmAccess->access('BE_team_registration_list')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;
  case 'ranglisten';
		if(!$clmAccess->access('BE_club_edit_ranking')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;
  case 'gruppen';
		if(!$clmAccess->access('BE_club_edit_ranking')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;

  case 'mannschaften':
		if(!$clmAccess->access('BE_team_registration')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;
	case 'users';
		if(!$clmAccess->access('BE_user_general')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;
	case 'dwz';
		if(!$clmAccess->access('BE_database_general')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;
	case 'swt';
		if(!$clmAccess->access('BE_swt_general')) {		
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
	}
		break;
	case 'konfiguration';
		$controllerName = 'info';
		break;
	case 'check';
		if (clm_core::$access->getType() != "0") {
			$controllerName = 'check';
		} else {
			$app->enqueueMessage( JText::_( 'NO_PERMISSION' ),'warning' );
			$controllerName = 'info';
		}
		break;

    // die richtige Datei einbinden
	case 'info':
		// Temporary interceptor
		$task = clm_core::$load->request_string('task');
		if ($task == 'info') {	$controllerName = 'info';}
		break;
		// wenn nichts passt dann nimm dies
	default:
		$controllerName = 'info';
		break;

}
}

$view = clm_core::$load->request_string('view');
$section = clm_core::$load->request_string('section');
if($view == "view_config") {
	$fix = clm_core::$api->view_config(array());
	clm_core::$load->load_css("icons_images");
	JToolBarHelper::title(JText::_('CONFIG_TITLE'), 'clm_headmenu_einstellungen');
	echo '<div id="clm">';
	if($fix[0]) {
		echo $fix[2]; // array dereferencing fix php 5.3
	} else {
		$fix = clm_core::$load->load_view("notification",array($fix[1]));
		echo "<div class='clm'>".$fix[1]."</div>";
	}
	echo '</div>';
	return;
} else if($view == "view_tournament" || $view == "turmain") {
	$fix = clm_core::$api->view_tournament(array());
	clm_core::$load->load_css("icons_images");
	JToolBarHelper::title( JText::_('TITLE_INFO') );
	echo '<div id="clm">';
	if($fix[0]) {
		echo $fix[2]; // array dereferencing fix php 5.3
	} else {
		$fix = clm_core::$load->load_view("notification",array($fix[1]));
		echo "<div class='clm'>".$fix[1]."</div>";
	}
	echo "</div>";
	return;
} else if($view == "view_tournament_group") {
	if(!isset($_GET["liga"])) {
		$_GET["liga"]=2;
	}
	$fix = clm_core::$api->view_tournament_group($_GET["liga"]);
	echo '<div id="clm">';
	clm_core::$load->load_css("icons_images");
	JToolBarHelper::title( JText::_('TITLE_INFO') );
	if($fix[0]) {
		echo $fix[2]; // array dereferencing fix php 5.3
	} else {
		$fix = clm_core::$load->load_view("notification",array($fix[1]));
		echo "<div class='clm'>".$fix[1]."</div>";
	}
	echo "</div>";
	return;
} else if($view == "view_be_menu" || $view == "info" || $section == "info" || ($controllerName=="info" && $view == '')) {
	$fix = clm_core::$api->view_be_menu(array());
	clm_core::$load->load_css("icons_images");
	JToolBarHelper::title(JText::_('TITLE_INFO'), 'clm_logo_bg');
	echo '<div id="clm">';
	if($fix[0]) {
		echo $fix[2]; // array dereferencing fix php 5.3
	} else {
		$fix = clm_core::$load->load_view("notification",array($fix[1]));
		echo "<div class='clm'>".$fix[1]."</div>";
	}
	echo "</div>";
	return;
} else if($view == "view_logging") {
	$fix = clm_core::$api->view_logging(array());
	clm_core::$load->load_css("icons_images");
	JToolBarHelper::title(JText::_('TITLE_INFO'));
	echo '<div id="clm">';
	if($fix[0]) {
		echo $fix[2]; // array dereferencing fix php 5.3
	} else {
		$fix = clm_core::$load->load_view("notification",array($fix[1]));
		echo "<div class='clm'>".$fix[1]."</div>";
	}
	echo "</div>";
	return;
}

echo '<div id="clm"><div class="clm">';
jimport('joomla.filesystem.folder');

// lädt alle CLM-Klassen - quasi autoload
$classpath = dirname(__FILE__).DS.'classes';
foreach( JFolder::files($classpath) as $file ) {
	JLoader::register(str_replace('.class.php', '', $file), $classpath.DS.$file);
}

// alternative CLM-Struktur für Turniere & Termine
if ($viewName = clm_core::$load->request_string('view')) {
	
	$language = JFactory::getLanguage();
	$language->load('com_clm');
	if ( in_array($viewName, array('catform', 'catmain', 'turform', 'turinvite', 'turmain', 'turplayeredit',
						'turplayerform', 'turplayers', 'turroundform', 'turroundmatches','turrounds',
						'turregistrations', 'turregistrationedit','turplayersmail',
						'terminemain', 'termineform',
						'swtturnier', 'swtturnierinfo', 'swtturniertlnr', 'swtturniererg'))) {
		$language->load('com_clm.turnier');
	} elseif ( in_array($viewName, array('accessgroupsmain', 'accessgroupsform'))) {
		$language->load('com_clm.accessgroup');
	//} elseif ($viewName == 'config') {
	//	$language->load('com_clm.config');
	}
	if ( in_array($viewName, array('swt', 'swtturnier', 'swtturnierinfo', 'swtturniertlnr', 'swtturniererg',
						'swtliga', 'swtligainfo', 'swtligaman', 'swtligaerg', 'swtligasave', 'pgnimport', 'pgndata'))) {
		$language->load('com_clm.swtimport');
		clm_core::$load->load_js("submit");
	}
	
	// den Basis-Controller einbinden (com_*/controller.php)
	require_once (JPATH_COMPONENT.DS.'controller.php');
	
	// Require specific controller if requested (im hidden-field der adminForm!)
	if( $controller = clm_core::$load->request_string('controller')) {
	
		$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
		if (file_exists($path)) {
			require_once $path;
		} else {
			$controller = '';
		}
	
	}
	$classname  = 'CLMController'.$controller;
	$controller = new $classname( ); // Instanziert
	// alles was im Basis-Controller zur Verfügung steht, steht jetzt den entsprechenden Scripten zur Verfügung!
	
	
} else {
	//Sprachfile
	$language = JFactory::getLanguage();
	if (clm_core::$load->request_string('section', '') == "users")	$language->load('com_clm.accessgroup');
	// bisherige CLM-Architektur
	require_once( JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php' );
	$controllerName = 'CLMController'.$controllerName;

	// Create the controller
	$controller = new $controllerName();

}

// Perform the Request task
$controller->execute( clm_core::$load->request_string('task'));
// Redirect if set by the controller
$controller->redirect();

} else {
	$fix = clm_core::$load->load_view("notification", array("e_noSeasonBackend"));
	echo '<div id="clm">';
	echo "<div class='clm'>".$fix[1]."</div>";
	echo "</div>";
}
echo "</div></div>";
}
