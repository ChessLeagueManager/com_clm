<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerCheck extends JControllerLegacy
{
	/**
	 * Constructor
	 */
function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		//$this->registerTask( 'add','edit' );
	}

function display($cachable = false, $urlparams = array())
	{
	$mainframe	= JFactory::getApplication();
	$option 	= clm_core::$load->request_string('option');
	$section 	= clm_core::$load->request_string('section');
	$mainframe->redirect( 'index.php?option='. $option.'&section=info' );
	}

function edit()
	{
	$mainframe	= JFactory::getApplication();

	$db 		=JFactory::getDBO();
	$user 		=JFactory::getUser();
	$task 		= clm_core::$load->request_string('task');
	$cid = clm_core::$load->request_array_int('cid');
	$id  = clm_core::$load->request_int('id',0);
	if (is_null($cid)) {
		$cid[0] = $id; }
	$option 	= clm_core::$load->request_string('option');
	$section 	= clm_core::$load->request_string('section');
	$liga 		= clm_core::$load->request_int('liga');

	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$countryversion= $config->countryversion;

	// Rundendaten ermitteln
	$query = " SELECT a.nr,a.sid,a.datum,l.teil,l.id, l.stamm, l.name, l.runden, l.rang "
	 	." FROM #__clm_runden_termine as a "
	 	." LEFT JOIN #__clm_liga AS l ON ( l.id = a.liga ) "
		." WHERE a.id = ".$cid[0]
		;
	$db->setQuery( $query);
	$rnd=$db->loadObjectList();

	// illegaler Einbruchversuch über URL !
	// evtl. mitschneiden !?!
	$saison		=JTable::getInstance( 'saisons', 'TableCLM' );
	$saison->load( $rnd[0]->sid );
	if ($saison->archiv == "1") { 
		$app->enqueueMessage( JText::_( 'CHECK_RUNDEN' ), 'warning' );
		$mainframe->redirect( 'index.php?option='. $option.'&section=info' );
	}

	// aktuellen Durchgang bestimmen um Runde zu bestimmen (notwendig für Doppelrunden)
	if ( $rnd[0]->nr > $rnd[0]->runden) {
		$dg = 2;
		$runde = ($rnd[0]->nr)-($rnd[0]->runden);
						}
	else {
		$dg = 1;
		$runde = $rnd[0]->nr;
		}
	// Daten der Paarungen ermitteln
	$query = " SELECT a.paar,a.brett,a.spieler,a.PKZ,a.gegner,a.gPKZ,a.ergebnis,a.kampflos, m.name as man_heim, m.zps as hzps, d.Spielername as hname,  ";
		if ($rnd[0]->rang =="0" ) { $query = $query.' s.snr as hnr , s.mnr as rmnr'; }
		else { $query = $query.' s.Rang as hnr,s.man_nr as rmnr '; }
		$query = $query
			." FROM #__clm_rnd_spl as a "
			." LEFT JOIN #__clm_mannschaften AS m ON ( m.tln_nr = a.tln_nr AND m.liga = a.lid) ";
        if ($countryversion == "de")
			$query .= " LEFT JOIN #__clm_dwz_spieler AS d ON ( d.Mgl_Nr = a.spieler AND d.ZPS = m.zps AND d.sid = a.sid) ";
        else
			$query .= " LEFT JOIN #__clm_dwz_spieler AS d ON ( d.PKZ = a.PKZ AND d.ZPS = m.zps AND d.sid = a.sid) ";
 		if ($rnd[0]->rang =="0" ) { 
			if ($countryversion == "de")
				$query .= " LEFT JOIN #__clm_meldeliste_spieler AS s ON ( s.mnr = m.man_nr AND s.zps = a.zps AND s.mgl_nr = a.spieler AND s.sid = a.sid AND s.lid = a.lid)  "; 
			else
				$query .= " LEFT JOIN #__clm_meldeliste_spieler AS s ON ( s.mnr = m.man_nr AND s.zps = a.zps AND s.PKZ = a.PKZ AND s.sid = a.sid AND s.lid = a.lid)  "; }
			else { $query = $query." LEFT JOIN #__clm_rangliste_spieler AS s ON ( s.ZPS = a.zps AND s.Mgl_Nr = a.spieler AND s.sid = a.sid) "; }
		$query = $query
			." WHERE a.runde =".$runde
			." AND a.lid =".$rnd[0]->id
			." AND a.sid = ".$rnd[0]->sid
			." AND a.dg = ".$dg
			." ORDER BY a.paar ASC, a.heim DESC , a.brett ASC "
			;
	$db->setQuery( $query);
	$dat=$db->loadObjectList();

	require_once(JPATH_COMPONENT.DS.'views'.DS.'check.php');
	CLMViewCheck::check( $row, $dat, $rnd, $liga,$dg,$runde );
	}

public static function check_d_spl($zps,$spieler,$PKZ,$runde,$dg,$rnd)    
	// Durchzählen wie oft Spieler am Spieltag eingesetzt ist
	{
	$sid = $rnd[0]->sid;
	$rt_date = $rnd[0]->datum;
	
	$db 	=JFactory::getDBO();
	$query = " SELECT COUNT(*) as count "
		." FROM #__clm_rnd_spl as a "
		." LEFT JOIN #__clm_liga as l ON a.lid = l.id "
	 	." LEFT JOIN #__clm_runden_termine AS rt ON ( a.lid = rt.liga AND (a.runde + (a.dg - 1) * l.runden) = rt.nr ) "  
		." WHERE a.zps = '$zps'"
		." AND a.sid = ".$sid
		." AND rt.datum = '$rt_date'";
	if ($spieler > 0)
		$query .= " AND a.spieler = ".$spieler;
	else
		$query .= " AND a.PKZ = '".$PKZ."'";
	$db->setQuery( $query);
	$data=$db->loadObjectList();
	$count = $data[0]->count;
	
	return $count;
	}

public static function show_d_spl($zps,$spieler,$PKZ,$runde,$dg,$rnd)    
   	// Wo wurde Spieler eingesetzt ?
	{
	$sid = $rnd[0]->sid;
	$rt_date = $rnd[0]->datum;
	
	$db 	=JFactory::getDBO();
	$query = " SELECT l.name, a.lid, a.paar, a.brett, rt.datum"
		." FROM #__clm_rnd_spl as a "
		." LEFT JOIN #__clm_liga as l ON a.lid = l.id "
		." LEFT JOIN #__clm_runden_termine AS rt ON ( a.lid = rt.liga AND (a.runde + (a.dg - 1) * l.runden) = rt.nr ) "  
		." WHERE a.zps = '$zps'"
		." AND a.sid = ".$sid      
		." AND rt.datum = '$rt_date'";  
	if ($spieler > 0)
		$query .= " AND a.spieler = ".$spieler
			   ." ORDER BY a.lid ASC, a.brett ASC ";
	else
		$query .= " AND a.PKZ = '".$PKZ."'"
			   ." ORDER BY a.lid ASC, a.brett ASC ";
	$db->setQuery( $query);
	$liga=$db->loadObjectList();
	return $liga;
	}

public static function check_r_spl($zps,$spieler,$PKZ,$runde,$dg,$rnd)    
	// Durchzählen wie oft Spieler in der Runde eingesetzt ist
	{
	$sid = $rnd[0]->sid;
	
	$db 	=JFactory::getDBO();
	$query = " SELECT COUNT(*) as count "
		." FROM #__clm_rnd_spl as a "
		." WHERE a.zps = '$zps'"
		." AND a.runde = ".$runde
		." AND a.dg = ".$dg
		." AND a.sid = ".$sid;       
	if ($spieler > 0)
		$query .= " AND a.spieler = ".$spieler;
	else
		$query .= " AND a.PKZ = '".$PKZ."'";
	$db->setQuery( $query);
	$data=$db->loadObjectList();
	$count = $data[0]->count;
	return $count;
	}

public static function show_r_spl($zps,$spieler,$PKZ,$runde,$dg,$rnd)    
   	// Wo wurde Spieler eingesetzt ?
	{
	$sid = $rnd[0]->sid;
	
	$db 	=JFactory::getDBO();
	$query = " SELECT l.name, a.lid, a.paar, a.brett, rt.datum"
		." FROM #__clm_rnd_spl as a "
		." LEFT JOIN #__clm_liga as l ON a.lid = l.id "
		." LEFT JOIN #__clm_runden_termine AS rt ON ( a.lid = rt.liga AND (a.runde + (a.dg - 1) * l.runden) = rt.nr ) "  
		." WHERE a.zps = '$zps'"
		." AND a.runde = ".$runde
		." AND a.dg = ".$dg
		." AND a.sid = ".$sid;      
	if ($spieler > 0)
		$query .= " AND a.spieler = ".$spieler
			   ." ORDER BY a.lid ASC, a.brett ASC ";
	else
		$query .= " AND a.PKZ = '".$PKZ."'"
			   ." ORDER BY a.lid ASC, a.brett ASC ";
	$db->setQuery( $query);
	$liga=$db->loadObjectList();
	return $liga;
	}
}





 
