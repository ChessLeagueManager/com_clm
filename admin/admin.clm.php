<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2014 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

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

// current season
	$db = JFactory::getDbo();
	$db->setQuery("SELECT id FROM #__clm_saison WHERE published = 1 AND archiv = 0 ORDER BY name DESC LIMIT 1 ");
	$c_season = $db->loadObject()->id;
	DEFINE ('CLM_SEASON', $c_season);
	
// Make sure the user is authorized to view this page
$user = & JFactory::getUser();
$jid = $user->get('id');
DEFINE ('CLM_ID', $jid);

// CLM Userstatus auslesen
$query = "SELECT a.usertype, a.user_clm FROM #__clm_user as a"
	." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
	." WHERE a.jid = ".$jid
	." AND a.published = 1 "
	." AND s.published = 1 AND s.archiv = 0 ";
$db	= & JFactory::getDBO();
$db->setQuery($query);
if ($userdata = $db->loadObjectList()) {
// if ($userdata[0]->usertype != '') {
	DEFINE ('CLM_admin', $userdata[0]->usertype);
	DEFINE ('CLM_usertype', $userdata[0]->usertype);
	DEFINE ('CLM_user', $userdata[0]->user_clm);
} else { 
	DEFINE ('CLM_admin', 'NO');
	DEFINE ('CLM_usertype', 'NO');
	DEFINE ('CLM_user', 'NO');
}
// Pfad zum JS-Verzeichnis
DEFINE ('CLM_PATH_JAVASCRIPT', 'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'javascript'.DIRECTORY_SEPARATOR);

// Set the table directory
JTable::addIncludePath(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'tables');

// init der Berechtigungen:

// diese Seiten sind mit jeglichem Zugang möglich (CLM_admin != "0")
$arrayAccessSimple = array('ergebnisse', 'runden', 'vereine', 'meldelisten', 'ranglisten', 'gruppen', 'mannschaften', 'users', 'check');

// diese Seiten sind verschiedenen Admin-Rängen vorbehalten
$arrayAccessMulti = array();
$arrayAccessMulti['saisons'] = array('admin');
$arrayAccessMulti['swt'] = array('admin');
$arrayAccessMulti['ligen'] = array('admin', 'dwz', 'dv', 'sl');
$arrayAccessMulti['mturniere'] = array('admin', 'dwz', 'dv', 'tl'); //mtmt
$arrayAccessMulti['paarung'] = array('admin', 'dwz', 'dv', 'sl');
$arrayAccessMulti['paarungsliste'] = array('admin', 'dv', 'sl');
$arrayAccessMulti['db'] = array('admin', 'dwz');
$arrayAccessMulti['elobase'] = array('admin', 'dwz');
$arrayAccessMulti['dwz'] = array('admin', 'dwz', 'dv');
$arrayAccessMulti['logfile'] = array('admin', 'dwz', 'dv', 'sl', 'spl');
$arrayAccessMulti['config'] = array('admin');

// Parameter auslesen 
$config = &JComponentHelper::getParams( 'com_clm' );
$val=$config->get('menue',1);

require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
$clmAccess = new CLMAccess();

JSubMenuHelper::addEntry(JText::_('INFO'), 'index.php?option=com_clm&section=info', (JRequest::getVar('section')) == 'info'?true:false);

if ($val == 0) {
	JSubMenuHelper::addEntry(JText::_('ERGEBNISSE'),  'index.php?option=com_clm&section=ergebnisse', (JRequest::getVar('section')) == 'ergebnisse'?true:false); 
}

//if (in_array(CLM_admin, $arrayAccessMulti['saisons'])) 
// Nur CLM-Admin darf hier zugreifen
	//if (JFactory::getUser()->authorise('core.manage.clm', 'com_clm')) 	{  
// if (CLM_admin === 'admin' ) {
$clmAccess->accesspoint = 'BE_season_general';
if($clmAccess->access() !== false) {
	JSubMenuHelper::addEntry(JText::_('SAISON'), 'index.php?option=com_clm&section=saisons', (JRequest::getVar('section')) == 'saisons'?true:false);
}
$clmAccess->accesspoint = 'BE_event_general';
if($clmAccess->access() !== false) {
//if(CLM_admin != "NO") {
	JSubMenuHelper::addEntry(JText::_('TERMINE'), 'index.php?option=com_clm&view=terminemain', (JRequest::getVar('view')) == 'terminemain'?true:false);
}
$clmAccess->accesspoint = 'BE_tournament_general';
if($clmAccess->access() !== false) {
//if(CLM_admin != "NO") {
	JSubMenuHelper::addEntry(JText::_('TURNIERE'), 'index.php?option=com_clm&view=turmain', (JRequest::getVar('view')) == 'turmain'?true:false);
}
$clmAccess->accesspoint = 'BE_league_general';
if($clmAccess->access() !== false) {
	JSubMenuHelper::addEntry(JText::_('LIGEN'), 'index.php?option=com_clm&section=ligen', (JRequest::getVar('section')) == 'ligen'?true:false);
}
$clmAccess->accesspoint = 'BE_teamtournament_general';
if($clmAccess->access() !== false) {
	JSubMenuHelper::addEntry(JText::_('MTURNIERE'), 'index.php?option=com_clm&section=mturniere', (JRequest::getVar('section')) == 'mturniere'?true:false); //mtmt
}

if ($val == 0) {
	JSubMenuHelper::addEntry(JText::_('SPIELTAGE'), 'index.php?option=com_clm&section=runden', (JRequest::getVar('section')) == 'runden'?true:false);
}

// Nur CLM-Admin darf hier zugreifen
	//if (JFactory::getUser()->authorise('core.manage.clm', 'com_clm')) 	{  
$clmAccess->accesspoint = 'BE_club_general';
if($clmAccess->access() !== false) {
	JSubMenuHelper::addEntry(JText::_('VEREINE'), 'index.php?option=com_clm&section=vereine', (JRequest::getVar('section')) == 'vereine'?true:false);
}
$clmAccess->accesspoint = 'BE_team_general';
if($clmAccess->access() !== false) {
	JSubMenuHelper::addEntry(JText::_('MANNSCHAFTEN'), 'index.php?option=com_clm&section=mannschaften', (JRequest::getVar('section')) == 'mannschaften'?true:false);
}
$clmAccess->accesspoint = 'BE_user_general';
if($clmAccess->access() !== false) {
	JSubMenuHelper::addEntry(JText::_('USER'), 'index.php?option=com_clm&section=users', (JRequest::getVar('section')) == 'users'?true:false);
}

//if (in_array(CLM_admin, $arrayAccessMulti['swt'])) {
// if (CLM_admin === 'admin') {
$clmAccess->accesspoint = 'BE_swt_general';
if($clmAccess->access() !== false) {
	//JSubMenuHelper::addEntry(JText::_('SWT'), 'index.php?option=com_clm&section=swt', (JRequest::getVar('section')) == 'swt'?true:false);
	JSubMenuHelper::addEntry(JText::_('SWT'), 'index.php?option=com_clm&view=swt', (JRequest::getVar('view')) == 'swt'?true:false);
}

//if (in_array(CLM_admin, $arrayAccessMulti['elobase'])) {
// if (CLM_admin === 'admin' || CLM_admin === 'dwz') {
$clmAccess->accesspoint = 'BE_elobase_general';
if($clmAccess->access() !== false) {
	//JSubMenuHelper::addEntry(JText::_('ELOBASE'), 'index.php?option=com_clm&section=elobase', (JRequest::getVar('section')) == 'elobase'?true:false);
	JSubMenuHelper::addEntry(JText::_('DeWIS'), 'index.php?option=com_clm&view=auswertung', (JRequest::getVar('view')) == 'auswertung'?true:false);
}

//if (in_array(CLM_admin, $arrayAccessMulti['db'])) {
// if (CLM_admin === 'admin' || CLM_admin === 'dwz') {
$clmAccess->accesspoint = 'BE_database_general';
if($clmAccess->access() !== false) {
	//JSubMenuHelper::addEntry(JText::_('DATABASE'), 'index.php?option=com_clm&section=db', (JRequest::getVar('section')) == 'db'?true:false);
	JSubMenuHelper::addEntry(JText::_('DATABASE'), 'index.php?option=com_clm&view=db', (JRequest::getVar('view')) == 'db'?true:false);
}

//if (in_array(CLM_admin, $arrayAccessMulti['logfile'])) {
// if (CLM_admin === 'admin' || CLM_admin === 'dwz' || CLM_admin === 'dv' || CLM_admin === 'spl') {
$clmAccess->accesspoint = 'BE_logfile_general';
if($clmAccess->access() == true) {
	JSubMenuHelper::addEntry(JText::_('LOGFILE'), 'index.php?option=com_clm&view=logmain', (JRequest::getVar('view')) == 'logmain'?true:false);
}

//if (in_array(CLM_admin, $arrayAccessMulti['config'])) {
// if (CLM_admin === 'admin') {
$clmAccess->accesspoint = 'BE_config_general';
if($clmAccess->access() == true) {
	JSubMenuHelper::addEntry(JText::_('CONFIG_TITLE'), 'index.php?option=com_clm&view=config', (JRequest::getVar('view')) == 'config'?true:false);
}


// Berechtigungen via Controllername checken
$controllerName = JRequest::getCmd( 'section');
// Zugangscheck
if (in_array($controllerName, $arrayAccessSimple)) { // jeglicher Zugang
	if (CLM_admin != "NO") {
		$controllerName = $controllerName;
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}

} else {
/* if (array_key_exists($controllerName, $arrayAccessMulti)) { // verschiedene Zugänge
	// dieser Zugang vorgesehen?
	if (in_array(CLM_admin, $arrayAccessMulti[$controllerName])) {
		$controllerName = $controllerName;
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
} else {
	// default
	$controllerName = 'info';
}
*/
 
switch ($controllerName) {
	
	// die folgenden Bereiche kennen wir
	case 'ergebnisse':
		//if (CLM_admin != "0") {
		//	$controllerName = 'ergebnisse';
		//} else {
		$clmAccess->accesspoint = 'BE_league_edit_result';
		if($clmAccess->access() == false) {		
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'saisons':
		//if (CLM_admin === 'admin') {
		//	$controllerName = 'saisons';
		//} else {
		$clmAccess->accesspoint = 'BE_season_general';
		if($clmAccess->access() == false) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'ligen';
		//if (CLM_admin === 'admin' || CLM_admin === 'dwz' || CLM_admin === 'dv' || CLM_admin === 'sl') {
		//	$controllerName = 'ligen';
		//} else {
		$clmAccess->accesspoint = 'BE_league_general';
		if($clmAccess->access() == false) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'mturniere';
		//if (CLM_admin === 'admin' || CLM_admin === 'dwz' || CLM_admin === 'dv' || CLM_admin === 'sl') {
		//	$controllerName = 'ligen';
		//} else {
		$clmAccess->accesspoint = 'BE_teamtournament_general';
		if($clmAccess->access() == false) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'paarung';
		//if (CLM_admin === 'admin' || CLM_admin === 'dv' || CLM_admin === 'dwz' || CLM_admin === 'sl') {
		//$controllerName = 'paarung';
		//} else {
		$clmAccess->accesspoint = 'BE_league_edit_fixture';
		if($clmAccess->access() == false) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'paarungsliste';
		//if (CLM_admin === 'admin' || CLM_admin === 'dv' || CLM_admin === 'sl') {
		//$controllerName = 'paarungsliste';
		//} else {
		$clmAccess->accesspoint = 'BE_league_edit_fixture';
		if($clmAccess->access() == false) {		
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;
/*  case 'db';
		//if (CLM_admin === 'admin' || CLM_admin === 'dwz' ) {
		//$controllerName = 'db';
		//} else {
		$clmAccess->accesspoint = 'BE_database_general';
		if($clmAccess->access() == false) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break; */
	case 'elobase';
		//if (CLM_admin === 'admin' OR CLM_admin === 'dwz') {
		//$controllerName = 'elobase';
		//} else {
		$clmAccess->accesspoint = 'BE_elobase_general';
		if($clmAccess->access() == false) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'runden';
		//if (CLM_admin != "0") {
		//$controllerName = 'runden';
		//} else {
		$clmAccess->accesspoint = 'BE_league_edit_round';
		if($clmAccess->access() == false) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'vereine';
		//if (CLM_admin != "0") {
		//$controllerName = 'vereine';
		//} else {
		$clmAccess->accesspoint = 'BE_league_edit_round';
		if($clmAccess->access() == false) {		
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;
  case 'meldelisten';
		//if (CLM_admin != "0") {
		//$controllerName = 'meldelisten';
		//} else {
		$clmAccess->accesspoint = 'BE_team_registration_list';
		if($clmAccess->access() == false) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
  case 'ranglisten';
		//if (CLM_admin != "0") {
		//$controllerName = 'ranglisten';
		//} else {
		$clmAccess->accesspoint = 'BE_club_edit_ranking';
		if($clmAccess->access() == false) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
  case 'gruppen';
		//if (CLM_admin != "0") {
		//$controllerName = 'gruppen';
		//} else {
		$clmAccess->accesspoint = 'BE_club_edit_ranking';
		if($clmAccess->access() == false) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
	}
		break;

  case 'mannschaften':
		//if (CLM_admin != "0") {
		//$controllerName = 'mannschaften';
		//} else {
		$clmAccess->accesspoint = 'BE_team_registration';
		if($clmAccess->access() == false) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'users';
		//if (CLM_admin != "0") {
		//$controllerName = 'users';
		//} else {
		$clmAccess->accesspoint = 'BE_user_general';
		if($clmAccess->access() == false) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'dwz';
		//if (CLM_admin === 'admin' || CLM_admin === 'dwz' || CLM_admin === 'dv') {
		//$controllerName = 'dwz';
		//} else {
		$clmAccess->accesspoint = 'BE_database_general';
		if($clmAccess->access() == false) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'swt';
		//if (CLM_admin === 'admin') {
		//$controllerName = 'swt';
		//} else {
		$clmAccess->accesspoint = 'BE_swt_general';
		if($clmAccess->access() == false) {		
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
	}
		break;
  case 'konfiguration';
	$controllerName = 'info';
		break;
  case 'check';
	if (CLM_admin != "0") {
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

// lädt alle CLM-Klassen - quasi autoload
$classpath = dirname(__FILE__).DIRECTORY_SEPARATOR.'classes';
foreach( JFolder::files($classpath) as $file ) {
	JLoader::register(str_replace('.class.php', '', $file), $classpath.DIRECTORY_SEPARATOR.$file);
}

// alternative CLM-Struktur für Turniere & Termine
if ($viewName = JRequest::getCmd('view')) {
	
	
	$language =& JFactory::getLanguage();
	$language->load('com_clm');
	if ( in_array($viewName, array('catform', 'catmain', 'logmain', 'turform', 'turinvite', 'turmain', 'turplayeredit',
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
	}
	
	// den Basis-Controller einbinden (com_*/controller.php)
	require_once (JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controller.php');
	
	// Require specific controller if requested (im hidden-field der adminForm!)
	if( $controller = JRequest::getWord('controller') ) {
	
		$path = JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controller.'.php';
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
	
	// bisherige CLM-Architektur
	require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controllerName.'.php' );
	$controllerName = 'CLMController'.$controllerName;

	// Create the controller
	$controller = new $controllerName();

}

// Perform the Request task
$controller->execute( JRequest::getCmd('task') );

// Redirect if set by the controller
$controller->redirect();
	

?>
