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

jimport( 'joomla.application.component.controller' );

class CLMControllerDWZ extends JController
{
	/**
	 * Constructor
	 */
function __construct( $config = array() )
	{
		parent::__construct( $config );
	}

function display()
	{
	$mainframe	= JFactory::getApplication();
	$option 	= JRequest::getCmd( 'option' );
	$section	= JRequest::getVar('section');
	$db		=& JFactory::getDBO();

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

	// Filter
	// Saison
	$sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
	$db->setQuery($sql);
	$lists['saison']=$db->loadObjectList();
	// Saisonfilter
	$sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
	$db->setQuery($sql);
	$saisonlist[]	= JHTML::_('select.option',  '0', JText::_( 'DWZ_SAISON' ), 'id', 'name' );
	$saisonlist	= array_merge( $saisonlist, $db->loadObjectList() );
	//$lists['sid']	= JHTML::_('select.genericlist', $saisonlist, 'filter_sid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','id', 'name', intval( $filter_sid ) );

	// Vereinefilter laden
	require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'filter_vereine.php');
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
	require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'dwz.php');
	//CLMViewDWZ::DWZ( $spieler,$verein, $lists, $pageNav, $option );
	CLMViewDWZ::DWZ( $spieler,$verein, $lists, '', $option );
	}


function cancel()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');

	$msg = JText::_( 'DWZ_AKTION');
	$mainframe->redirect( 'index.php?option='. $option.'&section=vereine', $msg );
	}

function spieler($zps)
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	//JRequest::checkToken() or die( 'Invalid Token' );
	
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		= & JFactory::getDBO();

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

function nachmeldung_delete()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		= & JFactory::getDBO();
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

function nachmeldung()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		= & JFactory::getDBO();
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

function daten_edit()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		= & JFactory::getDBO();
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

function spieler_delete()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		= & JFactory::getDBO();
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$spieler	= JRequest::getVar('del_spieler');
	$sid		= JRequest::getVar('sid');

	// SL nicht zulassen !
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
	$clmAccess = new CLMAccess();
	//if (CLM_usertype != 'admin' AND CLM_usertype != 'dv' AND CLM_usertype != 'dwz') {
	$clmAccess->accesspoint = 'BE_database_general';
	if($clmAccess->access() === false) {
		JError::raiseWarning( 500, JText::_( 'DWZ_REFERENT').CLM_usertype);
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

function daten_dsb_API()
	// Prüft die Mitgliederdaten in der Tabelle gegen die DSB-Daten und übernimmt neue Mitglieder
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		= & JFactory::getDBO();
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$sid		= JRequest::getVar('sid');
	$zps		= JRequest::getVar('zps');
	$incl_pd	= JRequest::getVar('incl_pd');

	if ( $sid == 0 ) {
		JError::raiseWarning( 500, JText::_( 'DWZ_VEREIN') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}
	// Daten als Array laden (Zeichensatz UTF-8!) vom DSB
	$dsbdaten = unserialize(file_get_contents("http://www.schachbund.de/php/dewis/verein.php?zps=".$zps."&format=array"));	
	
  $c_update = 0;
  $c_insert = 0;
  $c_provided = 0;
  $msg = '';
foreach($dsbdaten as $key => $value)
{	$c_provided++;
    $dsbstatus = $value["status"];
	if ($incl_pd == 0 AND $dsbstatus == 'P') continue;
	// Array umbauen (nur relevante Spalten)    
	$dsbid = $value["id"];
    $dsbnachname = $value["nachname"];
    $dsbvorname = $value["vorname"];
    $dsbdwz = $value["dwz"];
    $dsbdwzindex = $value["dwzindex"];
    $dsbzps = $value["verein"];
    $dsbmglnr = $value["mglnr"];
    $dsbfideid = $value["fideid"];
    $dsbfideelo = $value["fideelo"];
    $dsbfidetitel = $value["fidetitel"];

	// Die Mitgliedsnummer müssen mindestens vierstellig sein, mit führenden Nullen auffüllen
	if (strlen ($dsbmglnr) == 1) {
	  $dsbmglnr= "00" . $dsbmglnr;
	  } elseif (strlen ($dsbmglnr) == 2) {
	  $dsbmglnr= "0" . $dsbmglnr;
	  }
	// Falls Namensänderungen anliegen (Heirat)
	$name = $dsbnachname.",".$dsbvorname;
	$name_g = strtoupper($name);
	$search = array("ä", "ö", "ü", "ß", "é");
	$replace = array("AE", "OE", "UE", "SS", "É");
	$name_g =  str_replace($search, $replace, $name_g);
	// Prüfen ob Mitgliedsnummer schon vergeben wurde
	$query	= "SELECT Mgl_Nr FROM #__clm_dwz_spieler "
		." WHERE ZPS ='$zps'"
		." AND sid = '$sid'"
		." AND Mgl_Nr = '$dsbmglnr'"
		;
	$db->setQuery($query);
	$mgl_exist = $db->loadObjectList();

	if(isset($mgl_exist[0])) {

	//DWZ-Updaten
		$query	= "UPDATE #__clm_dwz_spieler "
		." SET DWZ = '$dsbdwz' "
		." , DWZ_Index = '$dsbdwzindex' "
		." , PKZ = '$dsbid' "
		." , Spielername = '$name' "
		." , Spielername_G = '$name_g' "
		." , FIDE_Elo = '$dsbfideelo' "
		." , FIDE_Titel = '$dsbfidetitel' "
		." , FIDE_ID = '$dsbfideid' "
		." , Status = '$dsbstatus' "
		." WHERE ZPS = '$dsbzps' "
		." AND sid = '$sid' "
		." AND Mgl_Nr = '$dsbmglnr' "
		;
	$db->setQuery($query);
	$db->query();
	if (mysql_errno() == 0) $c_update++;
	}
    else {
    //Neu
    $query	= "INSERT INTO #__clm_dwz_spieler"
 		." ( `sid`,`ZPS`, `Mgl_Nr`, `PKZ`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`, `FIDE_Elo`, `FIDE_Titel`, `FIDE_ID`, `Status`) "
 		." VALUES ('$sid','$dsbzps','$dsbmglnr','$dsbid','$name','$dsbdwz','$dsbdwzindex','$name_g','$dsbfideelo','$dsbfidetitel','$dsbfideid','$dsbstatus')"
 		;
   	$db->setQuery($query);
 	$db->query();
	if (mysql_errno() == 0) {
		$c_insert++;
		$msg .= "<br>Neues Mitglied: ".$dsbmglnr. " - ".$name;
	}
	}
  }
 	// Log schreiben
 	$clmLog = new CLMLog();
 	$clmLog->aktion = "DWZ-Update Verein";
 	$clmLog->params = array('sid' => $sid, 'zps' => $zps, 'cids' => 'g:'.$c_provided.',u:'.$c_update.',i:'.$c_insert);
 	$clmLog->write();
 	
    $msg = JText::_( 'DWZ_SPIELER_UPDATE' ).$msg;
 	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg);
	}

function daten_dsb_SOAP()

	// Prüft die Mitgliederdaten in der Tabelle gegen die DSB-Daten und übernimmt neue Mitglieder
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		= & JFactory::getDBO();
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$sid		= JRequest::getVar('sid');
	$zps		= JRequest::getVar('zps');
	$incl_pd	= JRequest::getVar('incl_pd');

	if ( $sid == 0 ) {
		JError::raiseWarning( 500, JText::_( 'DWZ_VEREIN') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}
	
	// Dewis Tabelle leeren
	$sql = " DELETE FROM #__clm_dwz_dewis "
		." WHERE zps = '$zps'"
		;
	$db->setQuery( $sql);
	$db->query();

	// SOAP Webservice
	try {
		$client = new SOAPClient( "https://dwz.svw.info/services/files/dewis.wsdl" );
 
		// VKZ des Vereins --> Vereinsliste
		$unionRatingList = $client->unionRatingList($zps);
		
		// Detaildaten zu Mitgliedern lesen
		foreach ($unionRatingList->members as $m) {
			if ($incl_pd == 0 AND $m->state == 'P') continue;
			$tcard = $client->tournamentCardForId($m->pid);
			$sql = " INSERT INTO #__clm_dwz_dewis (`pkz`,`nachname`, `vorname`,`zps`,`mgl_nr`, `dwz` ,`dwz_index` ,`status` "
				." ,`geschlecht`,`geburtsjahr`,`fide_elo`,`fide_land`,`fide_id`) VALUES"
				." ('$m->pid','$m->surname','$m->firstname','$zps','$m->membership','$m->rating','$m->ratingIndex','$m->state' "
				." ,'".$tcard->member->gender."' "
				." ,'".$tcard->member->yearOfBirth."' ,'".$tcard->member->elo."' "
				." ,'".$tcard->member->fideNation."' ,'".$tcard->member->idfide."' "
				." )"
				;
				$db->setQuery($sql);
				$db->query();
			}
		}
	catch (SOAPFault $f) {  print $f->faultstring;  }
	 
	// Spieler aus der CLM DEWIS Tabelle holen
	$sql = " SELECT a.* FROM #__clm_dwz_dewis as a"
		." WHERE ZPS = '$zps'"
		;
	$db->setQuery( $sql);
	$dsbdaten=$db->loadObjectList();

  $c_update = 0;
  $c_insert = 0;
  $c_provided = 0;
  $msg = '';
foreach($dsbdaten as $value)
{	$c_provided++;
	// Array umbauen (nur relevante Spalten)    
	$dsbid = $value->pkz;
    $dsbnachname = $value->nachname;
    $dsbvorname = $value->vorname;
    $dsbdwz = $value->dwz;
    $dsbdwzindex = $value->dwz_index;
    $dsbzps = $value->zps;
    $dsbmglnr = $value->mgl_nr;
    $dsbstatus = $value->status;
    $dsbgeschlecht = $value->geschlecht;
    $dsbgeburtsjahr = $value->geburtsjahr;
    $dsbfideid = $value->fide_id;
    $dsbfideelo = $value->fide_elo;
    $dsbfideland = $value->fide_land;

	// Die Mitgliedsnummer müssen mindestens dreistellig sein, mit führenden Nullen auffüllen
	if (strlen ($dsbmglnr) == 1) {
	  $dsbmglnr= "00" . $dsbmglnr;
	  } elseif (strlen ($dsbmglnr) == 2) {
	  $dsbmglnr= "0" . $dsbmglnr;
	  }
	// Falls Namensänderungen anliegen (Heirat)
	$name = $dsbnachname.",".$dsbvorname;
	$name_g = strtoupper($name);
	$search = array("ä", "ö", "ü", "ß", "é");
	$replace = array("AE", "OE", "UE", "SS", "É");
	$name_g =  str_replace($search, $replace, $name_g);
	if ($dsbgeschlecht == 'm') $dsbgeschlecht = 'M';
	if ($dsbgeschlecht == 'f') $dsbgeschlecht = 'W';
	if ($dsbfideid == '' OR $dsbfideid == '0') $dsbfideland = '';
	
	// Prüfen ob Mitgliedsnummer schon vergeben wurde
	$query	= "SELECT Mgl_Nr FROM #__clm_dwz_spieler "
		." WHERE ZPS ='$zps'"
		." AND sid = '$sid'"
		." AND Mgl_Nr = '$dsbmglnr'"
		;
	$db->setQuery($query);
	$mgl_exist = $db->loadObjectList();

	if(isset($mgl_exist[0])) {
	//DWZ-Updaten
	  if ($dsbdwz != '0')
		$query	= "UPDATE #__clm_dwz_spieler "
		." SET DWZ = '$dsbdwz' "
		." , DWZ_Index = '$dsbdwzindex' "
		." , PKZ = '$dsbid' "
		." , Spielername = '$name' "
		." , Spielername_G = '$name_g' "
		." , Geschlecht = '$dsbgeschlecht' "
		." , Geburtsjahr = '$dsbgeburtsjahr' "
		." , FIDE_Elo = '$dsbfideelo' "
		." , FIDE_Land = '$dsbfideland' "
		." , FIDE_ID = '$dsbfideid' "
		." , Status = '$dsbstatus' "
		." WHERE ZPS = '$dsbzps' "
		." AND sid = '$sid' "
		." AND Mgl_Nr = '$dsbmglnr' "
		;
	  else
		$query	= "UPDATE #__clm_dwz_spieler "
		." SET DWZ = NULL "
		." , DWZ_Index = NULL "
		." , PKZ = '$dsbid' "
		." , Spielername = '$name' "
		." , Spielername_G = '$name_g' "
		." , Geschlecht = '$dsbgeschlecht' "
		." , Geburtsjahr = '$dsbgeburtsjahr' "
		." , FIDE_Elo = '$dsbfideelo' "
		." , FIDE_Land = '$dsbfideland' "
		." , FIDE_ID = '$dsbfideid' "
		." , Status = '$dsbstatus' "
		." WHERE ZPS = '$dsbzps' "
		." AND sid = '$sid' "
		." AND Mgl_Nr = '$dsbmglnr' "
		;
	$db->setQuery($query);
	$db->query();
	if (mysql_errno() == 0) $c_update++;
	}
    else {
    //Neu
	  if ($dsbdwz != '0')
		$query	= "INSERT INTO #__clm_dwz_spieler"
			." ( `sid`,`ZPS`, `Mgl_Nr`, `PKZ`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`"
			." , `Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`) "
			." VALUES ('$sid','$dsbzps','$dsbmglnr','$dsbid','$name','$dsbdwz','$dsbdwzindex','$name_g'"
			." ,'$dsbgeschlecht','$dsbgeburtsjahr','$dsbfideelo','$dsbfideland','$dsbfideid','$dsbstatus')"
			;
 	  else
		$query	= "INSERT INTO #__clm_dwz_spieler"
			." ( `sid`,`ZPS`, `Mgl_Nr`, `PKZ`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`"
			." , `Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`) "
			." VALUES ('$sid','$dsbzps','$dsbmglnr','$dsbid','$name',NULL,NULL,'$name_g'"
			." ,'$dsbgeschlecht','$dsbgeburtsjahr','$dsbfideelo','$dsbfideland','$dsbfideid','$dsbstatus')"
			;
   	$db->setQuery($query);
 	$db->query();
	if (mysql_errno() == 0) {
		$c_insert++;
		$msg .= "<br>Neues Mitglied: ".$dsbmglnr. " - ".$name;
	}
  }
  }
 	// Log schreiben
 	$clmLog = new CLMLog();
 	$clmLog->aktion = "DWZ-Update Verein";
 	$clmLog->params = array('sid' => $sid, 'zps' => $zps, 'cids' => 'g:'.$c_provided.',u:'.$c_update.',i:'.$c_insert);
 	$clmLog->write();
 	
    $msg = JText::_( 'DWZ_SPIELER_UPDATE' ).$msg;
 	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg);
	}

}
