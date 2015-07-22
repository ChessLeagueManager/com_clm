<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerDWZ extends JControllerLegacy
{
	/**
	 * Constructor
	 */
function __construct( $config = array() )
	{
		parent::__construct( $config );
	}

function display($cachable = false, $urlparams = array())
	{
	$mainframe	= JFactory::getApplication();
	$option 	= JRequest::getCmd( 'option' );
	$section	= JRequest::getVar('section');
	$db		=JFactory::getDBO();

	$filter_vid		= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' );
	$filter_mgl		= $mainframe->getUserStateFromRequest( "$option.filter_mgl",'filter_mgl',0,'int' );
	$filter_sort		= $mainframe->getUserStateFromRequest( "$option.filter_sort",'filter_sort',0,'string' );

	// Wenn Verein und Spieler gewählt wurden dann Daten für Anzeige laden
	if($filter_vid !="0" AND $filter_mgl !="0"){
	$sql = 'SELECT * FROM #__clm_dwz_spieler as a'
		.' LEFT JOIN #__clm_saison AS s ON s.id = a.sid'
		." WHERE s.archiv = 0"
		. " AND ZPS ='$filter_vid'"
		. " AND Mgl_Nr =".$filter_mgl
		;
	$db->setQuery( $sql );
	$spieler=$db->loadObjectList();
	}
	else $spieler = array();
 
	// Wenn Verein gewählt wurden dann Daten für Anzeige laden
	if($filter_vid !="0" ){
	$sql = 'SELECT * FROM #__clm_dwz_spieler as a'
		.' LEFT JOIN #__clm_saison AS s ON s.id = a.sid'
		." WHERE s.archiv = 0"
		." AND ZPS ='$filter_vid'";
	if($filter_sort !="0") {
		$sql = $sql. " ORDER BY ".$filter_sort;
		}
	else {
		$sql = $sql. " ORDER BY Spielername ASC ";
		}
	$db->setQuery( $sql );
	$verein=$db->loadObjectList();
	}
	// Vereinefilter laden
	$vlist = CLMFilterVerein::vereine_filter(0);
	$lists['vid']	= JHTML::_('select.genericlist', $vlist, 'filter_vid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','zps', 'name', $filter_vid );
	

	// Spielerfilter
	//if ($filter_zps !="0" ) {
	if ($filter_vid !="0" ) {

	$sql = 'SELECT Mgl_Nr, Spielername FROM #__clm_dwz_spieler as a'
		.' LEFT JOIN #__clm_saison AS s ON s.id = a.sid'
		." WHERE s.archiv = 0 "
		." AND ZPS ='$filter_vid'"
		." ORDER BY Spielername ASC"
		;
	$db->setQuery($sql);
	$mlist[]	= JHTML::_('select.option',  '0', JText::_( 'DWZ_SPIELER' ), 'Mgl_Nr', 'Spielername' );
	$mlist		= array_merge( $mlist, $db->loadObjectList() );
	$lists['mgl']	= JHTML::_('select.genericlist', $mlist, 'filter_mgl', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','Mgl_Nr', 'Spielername', $filter_mgl );
	}

	if (!isset($verein)) $verein = array();
	require_once(JPATH_COMPONENT.DS.'views'.DS.'dwz.php');
	CLMViewDWZ::DWZ( $spieler,$verein, $lists, '', $option );
	}


static function cancel()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');

	$msg = JText::_( 'DWZ_AKTION');
	$mainframe->redirect( 'index.php?option='. $option.'&section=vereine', $msg );
	}

static function spieler($zps)
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	//JRequest::checkToken() or die( 'Invalid Token' );
	
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		= JFactory::getDBO();

	$query	= "SELECT a.Spielername, a.Mgl_Nr, a.ZPS FROM #__clm_dwz_spieler as a "
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE a.ZPS ='$zps'"
		." AND a.Status = 'N' "
		." AND s.archiv = 0"
		;
	$db->setQuery($query);
	$spieler=$db->loadObjectList();

	return $spieler;
	}

static function nachmeldung_delete()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		= JFactory::getDBO();
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$spieler	= JRequest::getVar('spieler');
	$sid		= JRequest::getVar('sid');

	if ( $spieler == 0 ) {
		JError::raiseWarning( 500, JText::_( 'DWZ_SPIELER_LOESCH') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	$zps	= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' );

	$query	= "DELETE FROM #__clm_dwz_spieler"
		." WHERE ZPS = '$zps'"
		." AND Mgl_Nr = ".$spieler
		." AND sid =".$sid
		;
	$db->setQuery($query);
	$db->query();

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "Nachmeldung gelöscht";
	$clmLog->params = array('sid' => $sid, 'zps' => $zps, 'mgl_nr' => $spieler, 'cids' => $cid);
	$clmLog->write();
	
	$msg = JText::_( 'DWZ_SPIELER_MITGLIED').' '.$spieler.' '.JText::_('DWZ_LOESCH' );
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg);
	}

static function nachmeldung()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		= JFactory::getDBO();
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$sid		= JRequest::getVar('sid');

	if ( $sid == 0 ) {
		JError::raiseWarning( 500, JText::_( 'DWZ_VEREIN') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	$name 		= JRequest::getVar('name');
	$mglnr		= JRequest::getVar('mglnr');
	$dwz 		= JRequest::getVar('dwz');
	$dwz_index 	= JRequest::getVar('dwz_index');
	$geschlecht	= JRequest::getVar('geschlecht');
	$geburtsjahr	= JRequest::getVar('geburtsjahr');
	$zps		= JRequest::getVar('zps');
	$status		= JRequest::getVar('status');	
	if (!isset($status) OR $status == "") $status = "N";
	// Prüfen ob Name und Mitgliedsnummer angegeben wurden
	if ( $name == "" OR $mglnr =="" OR $mglnr=="0" ) {
		JError::raiseWarning( 500, JText::_( 'DWZ_NAME_NR') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	// Prüfen ob Mitgliedsnummer schon vergeben wurde
	$filter_mgl	= $mainframe->getUserStateFromRequest( "$option.filter_mgl",'filter_mgl',0,'int' );

	$query	= "SELECT Mgl_Nr FROM #__clm_dwz_spieler "
		." WHERE ZPS ='$zps'"
		." AND sid = '$sid'"
		." AND Mgl_Nr = '$mglnr'"
		;
	$db->setQuery($query);
	$mgl_exist = $db->loadObjectList();

	if ($filter_mgl == $mglnr) {
		JError::raiseWarning( 500, JText::_( 'DWZ_SPIELER_AUSWAHL') );
		JError::raiseNotice( 6000,  JText::_( 'DWZ_DATEN_AENDERN' ));
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	if($mgl_exist[0]->Mgl_Nr !="") {
		JError::raiseWarning( 500, JText::_( 'DWZ_EXISTIERT') );
		JError::raiseNotice( 6000,  JText::_( 'DWZ_DATEN_AENDERN' ));
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	// Prüfen ob DWZ vorhanden ist
	if (!$dwz) {
	$query	= "INSERT INTO #__clm_dwz_spieler"
		." ( `sid`,`ZPS`, `Mgl_Nr`, `Status`, `Spielername`, `Geschlecht`, `Geburtsjahr` ) "
		." VALUES ('".clm_escape($sid)."','".clm_escape($zps)."','".clm_escape($mglnr)."','".clm_escape($status)."','".clm_escape($name)."','".clm_escape($geschlecht)."','".clm_escape($geburtsjahr)."')"
		;
		}
	else {
	$query	= "INSERT INTO #__clm_dwz_spieler"
		." ( `sid`,`ZPS`, `Mgl_Nr`, `Status`, `Spielername`, `Geschlecht`, `Geburtsjahr`, `DWZ`, `DWZ_Index`) "
		." VALUES ('".clm_escape($sid)."', '".clm_escape($zps)."','".clm_escape($mglnr)."','".clm_escape($status)."','".clm_escape($name)."','".clm_escape($geschlecht)."',"
		." '".clm_escape($geburtsjahr)."','".clm_escape($dwz)."','".clm_escape($dwz_index)."')"
		;
		}
	$db->setQuery($query);
	$db->query();

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "Nachmeldung";
	//$clmLog->params = array('sid' => $sid, 'zps' => $zps, 'mgl_nr' => $spieler);
	$clmLog->params = array('sid' => $sid, 'zps' => $zps, 'mgl_nr' => $mglnr);
	$clmLog->write();
	
	$msg = JText::_( 'DWZ_SPIELER_SPEICHERN' );
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg);
	}

static function daten_edit()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		= JFactory::getDBO();
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');

	$sid		= JRequest::getVar('sid');
	$name 		= JRequest::getVar('name');
	$mglnr		= JRequest::getVar('mglnr');
	$dwz 		= JRequest::getVar('dwz');
	$dwz_index 	= JRequest::getVar('dwz_index');
	$geschlecht	= JRequest::getVar('geschlecht');
	$geburtsjahr	= JRequest::getVar('geburtsjahr');
	$zps		= JRequest::getVar('zps');
	$status		= JRequest::getVar('status');	

	// Prüfen ob Name und Mitgliedsnummer angegeben wurden
	if ( $name == "" OR $mglnr =="" OR $mglnr=="0" ) {
		JError::raiseWarning( 500, JText::_( 'DWZ_NAME_NR') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	// Prüfen ob Mitgliedsnummer existiert
	$filter_mgl	= $mainframe->getUserStateFromRequest( "$option.filter_mgl",'filter_mgl',0,'int' );

	$query	= "SELECT Mgl_Nr FROM #__clm_dwz_spieler "
		." WHERE ZPS ='$zps'"
		." AND sid = '$sid'"
		." AND Mgl_Nr = '$mglnr'"
		;
	$db->setQuery($query);
	$mgl_exist = $db->loadObjectList();

	if (!$mgl_exist) {
		JError::raiseWarning( 500, JText::_( 'DWZ_SPIELER_NO') );
		JError::raiseNotice( 6000,  JText::_( 'DWZ_NACHM' ));
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	// Datensatz updaten
	$query	= "UPDATE #__clm_dwz_spieler "
		." SET Spielername = '".clm_escape($name)."' "
		." , Mgl_Nr = '".clm_escape($mglnr)."' "
		." , DWZ = '".clm_escape($dwz)."' "
		." , DWZ_Index = '".clm_escape($dwz_index)."' "
		." , Geschlecht = '".clm_escape($geschlecht)."' "
		." , Geburtsjahr = '".clm_escape($geburtsjahr)."' "
		." , Status = '".clm_escape($status)."' "
		." WHERE ZPS = '".clm_escape($zps)."' "
		." AND sid = '".clm_escape($sid)."'"
		." AND Mgl_Nr = '".clm_escape($mglnr)."'"
		;
	$db->setQuery($query);
	$db->query();

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "Spielerdaten geändert";
	$clmLog->params = array('sid' => $sid, 'zps' => $zps, 'mgl_nr' => $mglnr);
	$clmLog->write();
	
	$msg = JText::_( 'DWZ_SPIELER_AENDERN' );
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg);
	}

static function spieler_delete()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		= JFactory::getDBO();
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$spieler	= JRequest::getVar('del_spieler');
	$sid		= JRequest::getVar('sid');

	// SL nicht zulassen !
	$clmAccess = clm_core::$access;
	if($clmAccess->access('BE_database_general') === false) {
		JError::raiseWarning( 500, JText::_( 'DWZ_REFERENT').clm_core::$access->getType());
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	// Spieler muß ausgewählt sein
	if ( $spieler == 0 ) {
		JError::raiseWarning( 500, JText::_( 'DWZ_SPIELER_LOESCH') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	$zps	= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' );

	$query	= "DELETE FROM #__clm_dwz_spieler"
		." WHERE ZPS = '$zps'"
		." AND Mgl_Nr = ".$spieler
		." AND sid =".$sid
		;
	$db->setQuery($query);
	$db->query();

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "Spielerdaten gelöscht";
	$clmLog->params = array('sid' => $sid, 'zps' => $zps, 'mgl_nr' => $spieler);
	$clmLog->write();
	
	$msg = JText::_( 'DWZ_SPIELER_MITGLIED').' '.$spieler.' '.JText::_('DWZ_LOESCH' );
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg);
	}
}
