<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
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
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
	
	$filter_vid		= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' );
	$filder_vid_to 	= "0";
	$filter_vid_from	= $mainframe->getUserStateFromRequest( "$option.filter_vid_from",'filter_vid_from',0,'var' );

	$filter_sort		= $mainframe->getUserStateFromRequest( "$option.filter_sort",'filter_sort',0,'string' );
	if ($countryversion == "de") {
		$filter_mgl		= $mainframe->getUserStateFromRequest( "$option.filter_mgl",'filter_mgl',0,'int' );
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
 	} else {
		$filter_PKZ		= $mainframe->getUserStateFromRequest( "$option.filter_PKZ",'filter_PKZ',0,'string' );
		// Wenn Verein und Spieler gewählt wurden dann Daten für Anzeige laden
		if($filter_vid !="0" AND $filter_PKZ !=""){
		$sql = 'SELECT * FROM #__clm_dwz_spieler as a'
			.' LEFT JOIN #__clm_saison AS s ON s.id = a.sid'
			." WHERE s.archiv = 0"
			. " AND ZPS ='$filter_vid'"
			. " AND PKZ =".$filter_PKZ
			;
		$db->setQuery( $sql );
		$spieler=$db->loadObjectList();
		}
		else $spieler = array();
	}
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
	// Wenn FROM-Verein gewählt wurden dann Daten für Anzeige laden
	if($filter_vid_from !="0" ){
	$sql = 'SELECT * FROM #__clm_dwz_spieler as a'
		.' LEFT JOIN #__clm_saison AS s ON s.id = a.sid'
		." WHERE s.archiv = 0"
		." AND ZPS ='$filter_vid_from'";
	if($filter_sort !="0") {
		$sql = $sql. " ORDER BY ".$filter_sort;
		}
	else {
		$sql = $sql. " ORDER BY Spielername ASC ";
		}
	$db->setQuery( $sql );
	$verein_from=$db->loadObjectList();
	}
	// Saison
	$sql = 'SELECT id, name FROM #__clm_saison WHERE published = 1 AND archiv = 0';
	$db->setQuery($sql);
	$lists['saison']=$db->loadObjectList();

	// Vereinefilter laden
	$vlist = CLMFilterVerein::vereine_filter(0);
	$lists['vid']	= JHTML::_('select.genericlist', $vlist, 'filter_vid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','zps', 'name', $filter_vid );
	$lists['vid_to']	= JHTML::_('select.genericlist', $vlist, 'filter_vid_to', 'class="inputbox" size="1" ','zps', 'name', $filter_vid_to );
	$lists['vid_from']	= JHTML::_('select.genericlist', $vlist, 'filter_vid_from', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','zps', 'name', $filter_vid_from );
	

	// Spielerfilter
	//if ($filter_zps !="0" ) {
	if ($filter_vid !="0" ) {
	  if ($countryversion == "de") {
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
	  } else {
		$sql = 'SELECT PKZ, Spielername FROM #__clm_dwz_spieler as a'
			.' LEFT JOIN #__clm_saison AS s ON s.id = a.sid'
			." WHERE s.archiv = 0 "
			." AND ZPS ='$filter_vid'"
			." ORDER BY Spielername ASC"
			;
		$db->setQuery($sql);
		$mlist[]	= JHTML::_('select.option',  '0', JText::_( 'DWZ_SPIELER' ), 'PKZ', 'Spielername' );
		$mlist		= array_merge( $mlist, $db->loadObjectList() );
		$lists['PKZ']	= JHTML::_('select.genericlist', $mlist, 'filter_PKZ', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','PKZ', 'Spielername', $filter_PKZ );
	  }	
	}

	if (!isset($verein)) $verein = array();
	if (!isset($verein_from)) $verein_from = array();
	require_once(JPATH_COMPONENT.DS.'views'.DS.'dwz.php');
	CLMViewDWZ::DWZ( $spieler,$verein,$verein_from, $lists, '', $option );
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

	$query	= "SELECT a.Spielername, a.Mgl_Nr, a.ZPS, a.PKZ FROM #__clm_dwz_spieler as a "
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
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;

	if ( $spieler == 0 OR $spieler == '') {
		JError::raiseWarning( 500, JText::_( 'DWZ_SPIELER_LOESCH') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	$zps	= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' );

	$query	= "DELETE FROM #__clm_dwz_spieler"
		." WHERE ZPS = '$zps'"
		." AND sid =".$sid;
	if ($countryversion =="de") {
		$query	.= " AND Mgl_Nr = ".$spieler;
	} else {
		$query	.= " AND PKZ = '".$spieler."'";
	}	
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
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;

	$name 		= JRequest::getVar('name');
	$mglnr		= JRequest::getVar('mglnr');
	$PKZ		= JRequest::getVar('PKZ');
	$dwz 		= JRequest::getVar('dwz');
	$dwz_index 	= JRequest::getVar('dwz_index');
	if (!isset($dwz_index)) $dwz_index = 0;
	$geschlecht	= JRequest::getVar('geschlecht');
	$geburtsjahr	= JRequest::getVar('geburtsjahr');
	$zps		= JRequest::getVar('zps');
	$status		= JRequest::getVar('status');	
	if (!isset($status) OR $status == "") $status = "N";
	// Prüfen ob Name und Mitgliedsnummer/PKZ angegeben wurden
	if ( $countryversion == "de" AND ($name == "" OR $mglnr =="" OR $mglnr=="0") ) {
		JError::raiseWarning( 500, JText::_( 'DWZ_NAME_NR') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}
	if ( $countryversion == "en" AND ($name == "" OR $PKZ =="" OR $PKZ=="0") ) {
		JError::raiseWarning( 500, JText::_( 'DWZ_NAME_PKZ') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	// Prüfen ob Mitgliedsnummer schon vergeben wurde
	if ( $countryversion == "de") {
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
	} else {
		$filter_PKZ	= $mainframe->getUserStateFromRequest( "$option.filter_PKZ",'filter_PKZ','','string' );
		$query	= "SELECT PKZ FROM #__clm_dwz_spieler "
			." WHERE ZPS ='$zps'"
			." AND sid = '$sid'"
			." AND PKZ = '$PKZ'"
			;
		$db->setQuery($query);
		$PKZ_exist = $db->loadObjectList();
		if ($filter_PKZ == $PKZ) {
			JError::raiseWarning( 500, JText::_( 'DWZ_SPIELER_AUSWAHL') );
			JError::raiseNotice( 6000,  JText::_( 'DWZ_DATEN_AENDERN' ));
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link, $msg );
		}
		if($PKZ_exist[0]->PKZ !="") {
			JError::raiseWarning( 500, JText::_( 'DWZ_EXISTIERT_PKZ') );
			JError::raiseNotice( 6000,  JText::_( 'DWZ_DATEN_AENDERN' ));
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link, $msg );
		}
	}
	// Prüfen ob DWZ vorhanden ist
	if (!$dwz) {
	$query	= "INSERT INTO #__clm_dwz_spieler"
		." ( `sid`,`ZPS`, `Mgl_Nr`, `PKZ`, `Status`, `Spielername`, `Geschlecht`, `Geburtsjahr` ) "
		." VALUES ('".clm_escape($sid)."','".clm_escape($zps)."','".clm_escape($mglnr)."','".clm_escape($PKZ)."','".clm_escape($status)."','".clm_escape($name)."','".clm_escape($geschlecht)."','".clm_escape($geburtsjahr)."')"
		;
		}
	else {
	$query	= "INSERT INTO #__clm_dwz_spieler"
		." ( `sid`,`ZPS`, `Mgl_Nr`, `PKZ`, `Status`, `Spielername`, `Geschlecht`, `Geburtsjahr`, `DWZ`, `DWZ_Index`) "
		." VALUES ('".clm_escape($sid)."', '".clm_escape($zps)."','".clm_escape($mglnr)."','".clm_escape($PKZ)."','".clm_escape($status)."','".clm_escape($name)."','".clm_escape($geschlecht)."',"
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
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;

	$db 		= JFactory::getDBO();
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');

	$sid		= JRequest::getVar('sid');
	$name 		= JRequest::getVar('name');
	$mglnr		= JRequest::getVar('mglnr');
	$PKZ		= JRequest::getVar('PKZ');
	$dwz 		= JRequest::getVar('dwz');
	$dwz_index 	= JRequest::getVar('dwz_index');
	$geschlecht	= JRequest::getVar('geschlecht');
	$geburtsjahr	= JRequest::getVar('geburtsjahr');
	$zps		= JRequest::getVar('zps');
	$status		= JRequest::getVar('status');	

	// Prüfen ob Name und Mitgliedsnummer/PKZ angegeben wurden
	if ( $countryversion == "de" AND ($name == "" OR $mglnr =="" OR $mglnr=="0") ) {
		JError::raiseWarning( 500, JText::_( 'DWZ_NAME_NR') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}
	if ( $countryversion == "en" AND ($name == "" OR $PKZ =="" OR $PKZ=="0") ) {
		JError::raiseWarning( 500, JText::_( 'DWZ_NAME_PKZ') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	// Prüfen ob PKZ existiert
	if ( $countryversion == "de") {
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
	} else {
		$filter_PKZ	= $mainframe->getUserStateFromRequest( "$option.filter_PKZ",'filter_PKZ',0,'string' );
		$query	= "SELECT PKZ FROM #__clm_dwz_spieler "
			." WHERE ZPS ='$zps'"
			." AND sid = '$sid'"
			." AND PKZ = '$PKZ'"
			;
		$db->setQuery($query);
		$PKZ_exist = $db->loadObjectList();
		if (!$PKZ_exist) {
			JError::raiseWarning( 500, JText::_( 'DWZ_SPIELER_NO') );
			JError::raiseNotice( 6000,  JText::_( 'DWZ_NACHM' ));
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link, $msg );
		}
	}
	// Datensatz updaten
	$query	= "UPDATE #__clm_dwz_spieler "
		." SET Spielername = '".clm_escape($name)."' "
		." , Mgl_Nr = '".clm_escape($mglnr)."' "
		." , PKZ = '".clm_escape($PKZ)."' "
		." , DWZ = '".clm_escape($dwz)."' "
		." , DWZ_Index = '".clm_escape($dwz_index)."' "
		." , Geschlecht = '".clm_escape($geschlecht)."' "
		." , Geburtsjahr = '".clm_escape($geburtsjahr)."' "
		." , Status = '".clm_escape($status)."' "
		." WHERE ZPS = '".clm_escape($zps)."' "
		." AND sid = '".clm_escape($sid)."' ";
	if ( $countryversion == "de") {
		$query .= " AND Mgl_Nr = '".clm_escape($mglnr)."' ";
	} else {
		$query .= " AND PKZ = '".clm_escape($PKZ)."' ";
	}
	$db->setQuery($query);
	$db->query();

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "Spielerdaten geändert";
	$clmLog->params = array('sid' => $sid, 'zps' => $zps, 'mgl_nr' => $mglnr, 'PKZ' => $PKZ);
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
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;

	// SL nicht zulassen !
	$clmAccess = clm_core::$access;
	if($clmAccess->access('BE_database_general') === false) {
		JError::raiseWarning( 500, JText::_( 'DWZ_REFERENT').clm_core::$access->getType());
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	// Spieler muß ausgewählt sein
	if ( $spieler == 0 OR $spieler == '') {
		JError::raiseWarning( 500, JText::_( 'DWZ_SPIELER_LOESCH') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	$zps	= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' );

	$query	= "DELETE FROM #__clm_dwz_spieler"
		." WHERE ZPS = '$zps'"
		." AND sid =".$sid;
	if ($countryversion =="de") {
		$query	.= " AND Mgl_Nr = ".$spieler;
	} else {
		$query	.= " AND PKZ = '".$spieler."'";
	}
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

static function player_move_to()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		= JFactory::getDBO();
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$spieler	= JRequest::getVar('spieler_to');
	$newclub	= JRequest::getVar('filter_vid_to');
	$sid		= JRequest::getVar('sid');
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;

	// SL nicht zulassen !
	$clmAccess = clm_core::$access;
	if($clmAccess->access('BE_database_general') === false) {
		JError::raiseWarning( 500, JText::_( 'DWZ_REFERENT').clm_core::$access->getType());
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	// Spieler muß ausgewählt sein
	if ( $spieler == 0 OR $spieler == '') {
		JError::raiseWarning( 500, JText::_( 'DWZ_PLAYER_MISSING') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}
	// neuer Verein muß ausgewählt sein
	if ( $newclub == 0 OR $newclub == '') {
		JError::raiseWarning( 500, JText::_( 'DWZ_NEWCLUB_MISSING') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	$zps	= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' );
	// Player auslesen alter Verein
	$query	= "SELECT * FROM #__clm_dwz_spieler "
			." WHERE ZPS ='$zps'"
			." AND sid = '$sid'"
			." AND PKZ = '$spieler'"
			;
	$db->setQuery($query);
	$pl_data = $db->loadObjectList();
	if (!$pl_data) {
			JError::raiseWarning( 500, JText::_( 'DWZ_PLAYER_CLUB') );
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link, $msg );
	}
	// Player bereits im neuen Verein
	$query	= "SELECT * FROM #__clm_dwz_spieler "
			." WHERE ZPS ='$newclub'"
			." AND sid = '$sid'"
			." AND PKZ = '$spieler'"
			;
	$db->setQuery($query);
	$pl_check = $db->loadObjectList();
	if ($pl_check) {
			JError::raiseWarning( 500, JText::_( 'DWZ_PLAYER_CLUB_TO') );
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link, $msg );
	}
	// Player check gespielt in alten Verein
	$query	= "SELECT * FROM #__clm_meldeliste_spieler "
			." WHERE ZPS ='$zps'"
			." AND sid = '$sid'"
			." AND PKZ = '$spieler'"
			;
	$db->setQuery($query);
	$pl_check = $db->loadObjectList();
	if ($pl_check) {
			JError::raiseWarning( 500, JText::_( 'DWZ_PLAYER_CLUB_PLAIED') );
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link, $msg );
	}
	
	// Übernehmen in neuen Verein
	$query	= "INSERT INTO #__clm_dwz_spieler"
		." ( `sid`,`ZPS`, `Mgl_Nr`, `PKZ`, `Status`, `Spielername`, `Geschlecht`, `Geburtsjahr`, `DWZ`) "
		." VALUES ('".$pl_data[0]->sid."', '".$newclub."', 0 ,'".$spieler."','".$pl_data[0]->Status."','".$pl_data[0]->Spielername."','".$pl_data[0]->Geschlecht."',"
		." '".$pl_data[0]->Geburtsjahr."','".$pl_data[0]->DWZ."')"
		;

	$db->setQuery($query);
	$db->query();

	$query	= "DELETE FROM #__clm_dwz_spieler"
		." WHERE ZPS = '$zps'"
		." AND sid =".$sid;
	if ($countryversion =="de") {
		$query	.= " AND Mgl_Nr = ".$spieler;
	} else {
		$query	.= " AND PKZ = '".$spieler."'";
	}
	$db->setQuery($query);
	$db->query();

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'DWZ_PLAYER_MOVE_OUT');
	$clmLog->params = array('sid' => $sid, 'zps' => $zps, 'mgl_nr' => $spieler, 'to' => $newclub);
	$clmLog->write();
	
	$msg = JText::_( 'DWZ_PLAYER_MOVE_OUT').' '.$spieler;
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg);
}

static function player_move_from()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		= JFactory::getDBO();
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$spieler	= JRequest::getVar('spieler_from');
	$oldclub	= JRequest::getVar('filter_vid_from');
	$sid		= JRequest::getVar('sid');
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;

	// SL nicht zulassen !
	$clmAccess = clm_core::$access;
	if($clmAccess->access('BE_database_general') === false) {
		JError::raiseWarning( 500, JText::_( 'DWZ_REFERENT').clm_core::$access->getType());
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	// Spieler muß ausgewählt sein
	if ( $spieler == 0 OR $spieler == '') {
		JError::raiseWarning( 500, JText::_( 'DWZ_PLAYER_MISSING') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}
	// alter Verein muß ausgewählt sein
	if ( $oldclub == 0 OR $oldclub == '') {
		JError::raiseWarning( 500, JText::_( 'DWZ_OLDCLUB_MISSING') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	$zps	= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' );
	// Player auslesen im alten Verein
	$query	= "SELECT * FROM #__clm_dwz_spieler "
			." WHERE ZPS ='$oldclub'"
			." AND sid = '$sid'"
			." AND PKZ = '$spieler'"
			;
	$db->setQuery($query);
	$pl_data = $db->loadObjectList();
	if (!$pl_data) {
			JError::raiseWarning( 500, JText::_( 'DWZ_PLAYER_CLUB_FROM') );
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link, $msg );
	}
	// Player bereits im neuen Verein
	$query	= "SELECT * FROM #__clm_dwz_spieler "
			." WHERE ZPS ='$zps'"
			." AND sid = '$sid'"
			." AND PKZ = '$spieler'"
			;
	$db->setQuery($query);
	$pl_check = $db->loadObjectList();
	if ($pl_check) {
			JError::raiseWarning( 500, JText::_( 'DWZ_PLAYER_CLUB_ALREADY') );
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link, $msg );
	}
	// Player check gespielt in alten Verein
	$query	= "SELECT * FROM #__clm_meldeliste_spieler "
			." WHERE ZPS ='$oldclub'"
			." AND sid = '$sid'"
			." AND PKZ = '$spieler'"
			;
	$db->setQuery($query);
	$pl_check = $db->loadObjectList();
	if ($pl_check) {
			JError::raiseWarning( 500, JText::_( 'DWZ_PLAYER_CLUB_PLAIED') );
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link, $msg );
	}
	
	// Übernehmen in neuen Verein
	$query	= "INSERT INTO #__clm_dwz_spieler"
		." ( `sid`,`ZPS`, `Mgl_Nr`, `PKZ`, `Status`, `Spielername`, `Geschlecht`, `Geburtsjahr`, `DWZ`) "
		." VALUES ('".$pl_data[0]->sid."', '".$zps."', 0 ,'".$spieler."','".$pl_data[0]->Status."','".$pl_data[0]->Spielername."','".$pl_data[0]->Geschlecht."',"
		." '".$pl_data[0]->Geburtsjahr."','".$pl_data[0]->DWZ."')"
		;

	$db->setQuery($query);
	$db->query();

	$query	= "DELETE FROM #__clm_dwz_spieler"
		." WHERE ZPS = '$oldclub'"
		." AND sid =".$sid;
	if ($countryversion =="de") {
		$query	.= " AND Mgl_Nr = ".$spieler;
	} else {
		$query	.= " AND PKZ = '".$spieler."'";
	}
	$db->setQuery($query);
	$db->query();

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'DWZ_PLAYER_MOVE_IN');
	$clmLog->params = array('sid' => $sid, 'zps' => $zps, 'mgl_nr' => $spieler, 'from' => $oldclub);
	$clmLog->write();
	
	$msg = JText::_( 'DWZ_PLAYER_MOVE_IN').' '.$spieler;
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg);
	}
}
