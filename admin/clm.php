<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team. All rights reserved
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

// Fix für empfindliche Server //
$db = JFactory::getDbo();
$db->setQuery("SET SQL_BIG_SELECTS=1");
$db->query();
// Fix für empfindliche Server //

// erstellt DS und kümmert sich um die Rechteverwaltung
require_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_clm".DIRECTORY_SEPARATOR."clm".DIRECTORY_SEPARATOR."index.php");
// lädt Funktion zum sichern vor SQL-Injektion
require_once(JPATH_SITE.DS."components".DS."com_clm".DS."includes".DS."escape.php");

// Fix assets - group Manager - Adminzugriff erlauben (Joomla und CLM)
	$db = JFactory::getDbo();
	$db->setQuery("SELECT * FROM #__assets WHERE name = 'com_clm' AND parent_id = 1 ");
	$clm_assets = $db->loadObjectList();
	$rules_test = '{"core.admin":[],"core.manage":[],"core.manage.clm":[],"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[]}'; 
	if ((count($clm_assets) == 1) AND (($clm_assets[0]->rules == '{}') OR ($clm_assets[0]->rules == $rules_test))) 
	{
		$db->setQuery("UPDATE #__assets SET rules='".'{\"core.admin\":[],\"core.manage\":{\"6\":1},\"core.manage.clm\":{\"6\":1},\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'."' WHERE name = 'com_clm' AND parent_id = 1 ");
		$db->query();
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

JSubMenuHelper::addEntry(JText::_('INFO'), 'index.php?option=com_clm&view=info', (JRequest::getVar('view')) == 'info'?true:false);

if ($val == 0) {
	JSubMenuHelper::addEntry(JText::_('ERGEBNISSE'),  'index.php?option=com_clm&section=ergebnisse', (JRequest::getVar('section')) == 'ergebnisse'?true:false); 
}
if($clmAccess->access('BE_season_general')) {
	JSubMenuHelper::addEntry(JText::_('SAISON'), 'index.php?option=com_clm&section=saisons', (JRequest::getVar('section')) == 'saisons'?true:false);
}
if($clmAccess->access('BE_event_general')) {
	JSubMenuHelper::addEntry(JText::_('TERMINE'), 'index.php?option=com_clm&view=terminemain', (JRequest::getVar('view')) == 'terminemain'?true:false);
}
//if ($countryversion =="de") {
if($clmAccess->access('BE_tournament_general')) {
	JSubMenuHelper::addEntry(JText::_('TURNIERE'), 'index.php?option=com_clm&view=view_tournament', (JRequest::getVar('view')) == 'turmain'?true:false);
}  //}
if($clmAccess->access('BE_league_general')) {
	JSubMenuHelper::addEntry(JText::_('LIGEN'), 'index.php?option=com_clm&view=view_tournament_group&liga=1', (JRequest::getVar('section')) == 'ligen'?true:false);
}
if($clmAccess->access('BE_teamtournament_general')) {
	JSubMenuHelper::addEntry(JText::_('MTURNIERE'), 'index.php?option=com_clm&view=view_tournament_group&liga=0', (JRequest::getVar('section')) == 'mturniere'?true:false); //mtmt
}
if ($val == 0) {
	JSubMenuHelper::addEntry(JText::_('SPIELTAGE'), 'index.php?option=com_clm&section=runden', (JRequest::getVar('section')) == 'runden'?true:false);
}
if($clmAccess->access('BE_club_general')) {
	JSubMenuHelper::addEntry(JText::_('VEREINE'), 'index.php?option=com_clm&section=vereine', (JRequest::getVar('section')) == 'vereine'?true:false);
}
if($clmAccess->access('BE_team_general')) {
	JSubMenuHelper::addEntry(JText::_('MANNSCHAFTEN'), 'index.php?option=com_clm&section=mannschaften', (JRequest::getVar('section')) == 'mannschaften'?true:false);
}
if($clmAccess->access('BE_user_general')) {
	JSubMenuHelper::addEntry(JText::_('USER'), 'index.php?option=com_clm&section=users', (JRequest::getVar('section')) == 'users'?true:false);
}
if ($countryversion =="de") {
if($clmAccess->access('BE_swt_general')) {
	JSubMenuHelper::addEntry(JText::_('SWT'), 'index.php?option=com_clm&view=swt', (JRequest::getVar('view')) == 'swt'?true:false);
}}
if ($countryversion =="de") {
if($clmAccess->access('BE_dewis_general')) {
	JSubMenuHelper::addEntry(JText::_('DeWIS'), 'index.php?option=com_clm&view=auswertung', (JRequest::getVar('view')) == 'auswertung'?true:false);
}}
if ($countryversion =="en") {
if($clmAccess->access('BE_dewis_general')) {
	JSubMenuHelper::addEntry(JText::_('GRADING_EXPORT'), 'index.php?option=com_clm&view=auswertung', (JRequest::getVar('view')) == 'auswertung'?true:false);
}}
if($clmAccess->access('BE_database_general')) {
	JSubMenuHelper::addEntry(JText::_('DATABASE'), 'index.php?option=com_clm&view=db', (JRequest::getVar('view')) == 'db'?true:false);
}
if($clmAccess->access('BE_logfile_general')) {
	JSubMenuHelper::addEntry(JText::_('LOGFILE'), 'index.php?option=com_clm&view=view_logging', (JRequest::getVar('view')) == 'view_logging'?true:false);
}
if($clmAccess->access('BE_config_general')) {
	JSubMenuHelper::addEntry(JText::_('CONFIG_TITLE'), 'index.php?option=com_clm&view=view_config', (JRequest::getVar('view')) == 'view_config'?true:false);
}

// diese Seiten sind mit jeglichem Zugang möglich (clm_core::$access->getType() != "0")
$arrayAccessSimple = array('ergebnisse', 'runden', 'vereine', 'meldelisten', 'ranglisten', 'gruppen', 'mannschaften', 'users', 'check');

$controllerName = JRequest::getCmd( 'section');
if (in_array($controllerName, $arrayAccessSimple)) { // jeglicher Zugang
	if (clm_core::$access->getType() != "") {
		$controllerName = $controllerName;
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}

} else {
 
switch ($controllerName) {
	case 'ergebnisse':
		if(!$clmAccess->access('BE_league_edit_result')) {		
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'saisons':
		if(!$clmAccess->access('BE_season_general')) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'ligen';
		if(!$clmAccess->access('BE_league_general')) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'mturniere';
		if(!$clmAccess->access('BE_teamtournament_general')) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'paarung';
		if(!$clmAccess->access('BE_league_edit_fixture')) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'pairingdates';
		if(!$clmAccess->access('BE_league_edit_fixture')) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'paarungsliste';
		if(!$clmAccess->access('BE_league_edit_fixture')) {		
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;
	case 'dewis';
		if(!$clmAccess->access('BE_dewis_general')) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'runden';
		if(!$clmAccess->access('BE_league_edit_round')) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'vereine';
		if(!$clmAccess->access('BE_league_edit_round')) {		
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;
  case 'meldelisten';
		if(!$clmAccess->access('BE_team_registration_list')) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
  case 'ranglisten';
		if(!$clmAccess->access('BE_club_edit_ranking')) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
  case 'gruppen';
		if(!$clmAccess->access('BE_club_edit_ranking')) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
	}
		break;

  case 'mannschaften':
		if(!$clmAccess->access('BE_team_registration')) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'users';
		if(!$clmAccess->access('BE_user_general')) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'dwz';
		if(!$clmAccess->access('BE_database_general')) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'swt';
		if(!$clmAccess->access('BE_swt_general')) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
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
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;

    // die richtige Datei einbinden
	case 'info':
		// Temporary interceptor
		$task = JRequest::getCmd('task');
		if ($task == 'info') {	$controllerName = 'info';}
		break;
		// wenn nichts passt dann nimm dies
	default:
		$controllerName = 'info';
		break;

}
}
if(JRequest::getCmd('view') == "view_config") {
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
} else if(JRequest::getCmd('view') == "view_tournament" || JRequest::getCmd('view') == "turmain") {
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
} else if(JRequest::getCmd('view') == "view_tournament_group") {
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
} else if(JRequest::getCmd('view') == "view_be_menu" || JRequest::getCmd('view') == "info" || JRequest::getCmd('section') == "info" || ($controllerName=="info" && !JRequest::getCmd('view'))) {
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
} else if(JRequest::getCmd('view') == "view_logging") {
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
if ($viewName = JRequest::getCmd('view')) {
	
	
	$language = JFactory::getLanguage();
	$language->load('com_clm');
	if ( in_array($viewName, array('catform', 'catmain', 'turform', 'turinvite', 'turmain', 'turplayeredit',
						'turplayerform', 'turplayers', 'turroundform', 'turroundmatches','turrounds',
						'swtturnier', 'swtturnierinfo', 'swtturniertlnr', 'swtturniererg'))) {
		$language->load('com_clm.turnier');
	} elseif ( in_array($viewName, array('accessgroupsmain', 'accessgroupsform'))) {
		$language->load('com_clm.accessgroup');
	//} elseif ($viewName == 'config') {
	//	$language->load('com_clm.config');
	}
	if ( in_array($viewName, array('swt', 'swtturnier', 'swtturnierinfo', 'swtturniertlnr', 'swtturniererg',
						'swtliga', 'swtligainfo', 'swtligaman', 'swtligaerg', 'swtligasave'))) {
		$language->load('com_clm.swtimport');
		clm_core::$load->load_js("submit");
	}
	
	// den Basis-Controller einbinden (com_*/controller.php)
	require_once (JPATH_COMPONENT.DS.'controller.php');
	
	// Require specific controller if requested (im hidden-field der adminForm!)
	if( $controller = JRequest::getWord('controller') ) {
	
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
	if (JRequest::getCmd('section') == "users")	$language->load('com_clm.accessgroup');
	
	// bisherige CLM-Architektur
	require_once( JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php' );
	$controllerName = 'CLMController'.$controllerName;

	// Create the controller
	$controller = new $controllerName();

}

// Perform the Request task
$controller->execute( JRequest::getCmd('task') );

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
