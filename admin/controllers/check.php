<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class CLMControllerCheck extends JController
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

function display()
	{
	$mainframe	= JFactory::getApplication();
	$option 	= JRequest::getCmd( 'option' );
	$section = JRequest::getVar('section');
	$mainframe->redirect( 'index.php?option='. $option.'&section=info', $msg );
	}

function edit()
	{
	$mainframe	= JFactory::getApplication();

	$db 		=& JFactory::getDBO();
	$user 		=& JFactory::getUser();
	$task 		= JRequest::getVar( 'task');
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );
	$liga 		= JRequest::getVar( 'liga');
	JArrayHelper::toInteger($cid, array(0));

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
	$saison		=& JTable::getInstance( 'saisons', 'TableCLM' );
	$saison->load( $rnd[0]->sid );
	if ($saison->archiv == "1") { // AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'CHECK_RUNDEN' ));
		$mainframe->redirect( 'index.php?option='. $option.'&section=info', $msg );
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
	$query = " SELECT a.paar,a.brett,a.spieler,a.gegner,a.ergebnis,a.kampflos, m.name as man_heim, m.zps as hzps, d.Spielername as hname,  ";
		if ($rnd[0]->rang =="0" ) { $query = $query.' s.snr as hnr , s.mnr as rmnr'; }
			else { $query = $query.' s.Rang as hnr,s.man_nr as rmnr '; }
		$query = $query
	 	." FROM #__clm_rnd_spl as a "
	 	." LEFT JOIN #__clm_mannschaften AS m ON ( m.tln_nr = a.tln_nr AND m.liga = a.lid) "
         	." LEFT JOIN #__clm_dwz_spieler AS d ON ( d.Mgl_Nr = a.spieler AND d.ZPS = m.zps AND d.sid = a.sid) ";
 		if ($rnd[0]->rang =="0" ) { $query = $query." LEFT JOIN #__clm_meldeliste_spieler AS s ON ( s.mnr = m.man_nr AND s.zps = a.zps AND s.mgl_nr = a.spieler AND s.sid = a.sid AND s.lid = a.lid)  "; }
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

	require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'check.php');
	CLMViewCheck::check( $row, $dat, $rnd, $liga,$dg,$runde );
	}

function check_d_spl($zps,$spieler,$runde,$dg,$rnd)    
	// Durchzählen wie oft Spieler am Spieltag eingesetzt ist
	{
	$sid = $rnd[0]->sid;
	$rt_date = $rnd[0]->datum;
	
	$db 	=& JFactory::getDBO();
	$query = " SELECT COUNT(*) as count "
		." FROM #__clm_rnd_spl as a "
		." LEFT JOIN #__clm_liga as l ON a.lid = l.id "
	 	." LEFT JOIN #__clm_runden_termine AS rt ON ( a.lid = rt.liga AND (a.runde + (a.dg - 1) * l.runden) = rt.nr ) "  
		." WHERE a.zps = '$zps'"
		." AND a.spieler = ".$spieler
		." AND a.sid = ".$sid
		." AND rt.datum = '$rt_date'"
		;
	$db->setQuery( $query);
	$data=$db->loadObjectList();
	$count = $data[0]->count;
	
	return $count;
	}

function show_d_spl($zps,$spieler,$runde,$dg,$rnd)    
   	// Wo wurde Spieler eingesetzt ?
	{
	$sid = $rnd[0]->sid;
	$rt_date = $rnd[0]->datum;
	
	$db 	=& JFactory::getDBO();
	$query = " SELECT l.name, a.lid, a.paar, a.brett, rt.datum"
		." FROM #__clm_rnd_spl as a "
		." LEFT JOIN #__clm_liga as l ON a.lid = l.id "
		." LEFT JOIN #__clm_runden_termine AS rt ON ( a.lid = rt.liga AND (a.runde + (a.dg - 1) * l.runden) = rt.nr ) "  
		." WHERE a.zps = '$zps'"
		." AND a.spieler = ".$spieler
		." AND a.sid = ".$sid      
		." AND rt.datum = '$rt_date'"  
		." ORDER BY a.lid ASC, a.brett ASC "
		;
	$db->setQuery( $query);
	$liga=$db->loadObjectList();
	return $liga;
	}

function check_r_spl($zps,$spieler,$runde,$dg,$rnd)    
	// Durchzählen wie oft Spieler in der Runde eingesetzt ist
	{
	$sid = $rnd[0]->sid;
	
	$db 	=& JFactory::getDBO();
	$query = " SELECT COUNT(*) as count "
		." FROM #__clm_rnd_spl as a "
		." WHERE a.zps = '$zps'"
		." AND a.spieler = ".$spieler
		." AND a.runde = ".$runde
		." AND a.dg = ".$dg
		." AND a.sid = ".$sid       
		;
	$db->setQuery( $query);
	$data=$db->loadObjectList();
	$count = $data[0]->count;
	return $count;
	}

function show_r_spl($zps,$spieler,$runde,$dg,$rnd)    
   	// Wo wurde Spieler eingesetzt ?
	{
	$sid = $rnd[0]->sid;
	
	$db 	=& JFactory::getDBO();
	$query = " SELECT l.name, a.lid, a.paar, a.brett, rt.datum"
		." FROM #__clm_rnd_spl as a "
		." LEFT JOIN #__clm_liga as l ON a.lid = l.id "
		." LEFT JOIN #__clm_runden_termine AS rt ON ( a.lid = rt.liga AND (a.runde + (a.dg - 1) * l.runden) = rt.nr ) "  
		." WHERE a.zps = '$zps'"
		." AND a.spieler = ".$spieler
		." AND a.runde = ".$runde
		." AND a.dg = ".$dg
		." AND a.sid = ".$sid      
		." ORDER BY a.lid ASC, a.brett ASC "
		;
	$db->setQuery( $query);
	$liga=$db->loadObjectList();
	return $liga;
	}
}





 
