<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerPairingDates extends JControllerLegacy
{
	/**
	 * Constructor
	 */
function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask( 'add','edit' );
		$this->registerTask( 'apply','save' );
	}

function display($cachable = false, $urlparams = array())
	{
	$mainframe	= JFactory::getApplication();

	$db 		=JFactory::getDBO();
	$user 		=JFactory::getUser();
	$task 		= JRequest::getVar( 'task');
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );
	JArrayHelper::toInteger($cid, array(0));
	$row =JTable::getInstance( 'ligen', 'TableCLM' );
	// load the row from the db table
	$row->load( $cid[0] );

	// Prüfen ob User Berechtigung zum editieren hat
	if ($row->liga_mt == "0") {
		$mppoint = 'league';
		$csection = 'ligen';
	} else {
		$mppoint = 'teamtournament';
		$csection = 'mturniere';
	}
	$clmAccess = clm_core::$access;      
	// Prüfen ob User Berechtigung hat
	//if ( $liga->sl !== clm_core::$access->getJid() AND clm_core::$access->getType() !== 'admin') {
	if (( $row->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_'.$mppoint.'_edit_fixture') !== true) OR ($clmAccess->access('BE_'.$mppoint.'_edit_fixture') === false)) {
	//if ( $row->sl !== clm_core::$access->getJid() AND clm_core::$access->getType() !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'PAARUNG_LIGEN' ) );
		$link = 'index.php?option='.$option.'&section='.$csection;
		$mainframe->redirect( $link);
					}
	// Prüfen ob Runden erstellt sind
	if ( $row->rnd < 1) {
		JError::raiseWarning( 500, JText::_( 'PAARUNG_RUND' ) );
		$link = 'index.php?option='.$option.'&section='.$csection;
		$mainframe->redirect( $link);
		}

	$row->checkout( $user->get('id') );

	// Teilnehmer zusammenstellen
	$sql = "SELECT a.*, m.name as hname, m.tln_nr as htln, n.name as gname, n.tln_nr as gtln, rt.name as rname, rt.datum, rt.startzeit, rt.enddatum "
		." FROM #__clm_rnd_man as a"
		." LEFT JOIN #__clm_mannschaften as m ON m.tln_nr = a.tln_nr AND m.liga = a.lid AND m.sid = a.sid"
		." LEFT JOIN #__clm_mannschaften as n ON n.tln_nr = a.gegner AND n.liga = a.lid AND n.sid = a.sid"
		." LEFT JOIN #__clm_liga as l ON a.lid = l.id "
		." LEFT JOIN #__clm_runden_termine as rt ON rt.liga = a.lid AND rt.nr = (a.runde + (a.dg-1) * l.runden) "
		." WHERE a.sid = ".$row->sid
		." AND a.lid = ".$row->id
		." AND a.heim = 1"
		." ORDER BY a.dg ASC, a.runde ASC, a.paar ASC"
	;
	$db->setQuery( $sql );
	$paarung = $db->loadObjectList();

	// Mannschaftsliste
	$sql = "SELECT tln_nr, name, rankingpos FROM #__clm_mannschaften " //mtmt
		." WHERE sid = ".$row->sid." AND liga = ".$row->id
		." ORDER BY tln_nr ASC " ;
	$db->setQuery($sql);
	$man=$db->loadObjectList();

	// Mannschaftsliste
	$sql = "SELECT COUNT(tln_nr) as tln_nr FROM #__clm_mannschaften "
		." WHERE sid = ".$row->sid." AND liga = ".$row->id." ";
	$db->setQuery($sql);
	$count_man=$db->loadObjectList();

	// "spielfrei(e)" Mannschaft suchen				//mtmt
	$query = " SELECT COUNT(id) as anzahl FROM #__clm_mannschaften as a "
		." WHERE sid = ".$row->sid." AND liga = ".$row->id." "
		." AND a.name = 'spielfrei'"
		." ORDER BY a.tln_nr "
		;
	$db->setQuery($query);
	$spielfreiNumber = $db->loadObjectList();
	if ($spielfreiNumber[0]->anzahl > 1) {
		foreach ($man as $key => $value) {
			if ($value->name == 'spielfrei') $value->name .= " ".$value->tln_nr;
		}
	}

	require_once(JPATH_COMPONENT.DS.'views'.DS.'pairingdates.php');
	CLMViewPairingDates::pairingdates( $row, $paarung, $man, $count_man, $option, $cid, $lists );
	}

function cancel()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	
	$option		= JRequest::getCmd('option');
	$row 		=JTable::getInstance( 'ligen', 'TableCLM' );
	$id		= JRequest::getVar('id');	
	// load the row from the db table
	$row->load( $id );  //mtmt
	$msg = JText::_( 'PAARUNG_AENDERN');
	$mainframe->redirect( 'index.php?option='. $option.'&section=runden&liga='.$row->id, $msg );
	}

function save()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');

	$db 		=JFactory::getDBO();
	$task 		= JRequest::getVar( 'task');
	$user 		=JFactory::getUser();
	$meldung 	= $user->get('id');
	$row 		=JTable::getInstance( 'ligen', 'TableCLM' );
	$cid		=JRequest::getVar( 'id');
	$row->load( $cid);

	$sid		= $row->sid;
	$lid		= $row->id;
	$n1time = '00:00:00';
 
	if ($row->durchgang > 1) { $runden_counter = $row->durchgang * $row->runden; }
  		else { $runden_counter = $row->runden; }
	for ($x = 0; $x < $runden_counter; $x++ ) {
		if ($row->runden_modus == 4) $pairings = pow(2,($row->runden - 1 - $x));
		elseif ($row->runden_modus == 5) { $pairings = pow(2,($row->runden - 2 - $x));
										if ($pairings == 0) $pairings = 1; }
		else $pairings = $row->teil / 2; 
		if ($x+1 > (3 * $row->runden)) { 
			$dg = 4;
			$cnt = $x - (3 * $row->runden);
		} elseif ($x+1 > (2 * $row->runden)) { 
			$dg = 3;
			$cnt = $x - (2 * $row->runden);
		} elseif ($x+1 > $row->runden) { 
			$dg = 2;
			$cnt = $x - $row->runden;
		} else { 
			$dg = 1; 
			$cnt = $x;
			}
	for ($y = 0; $y < $pairings; $y++ ) {
		$ndate	= JRequest::getVar( 'D'.$dg.'R'.($cnt+1).'P'.($y+1).'Date');
		$ntime	= JRequest::getVar( 'D'.$dg.'R'.($cnt+1).'P'.($y+1).'Time');
		if ($ntime != '00:00:00' AND $n1time == '00:00:00') $n1time = $ntime;
		
		$query	= "UPDATE #__clm_rnd_man"
			." SET pdate = '".$ndate."'"
			." , ptime = '".$ntime."'"
			." WHERE sid = ".$sid
			." AND lid = ".$lid
			." AND runde = ".($cnt+1)
			." AND paar = ".($y+1)
			." AND dg = ".$dg
			." AND heim = 1 "
			;
		$db->setQuery($query);
		$db->query();

		$query	= "UPDATE #__clm_rnd_man"
			." SET pdate = '".$ndate."'"
			." , ptime = '".$ntime."'"
			." WHERE sid = ".$sid
			." AND lid = ".$lid
			." AND runde = ".($cnt+1)
			." AND paar = ".($y+1)
			." AND dg = ".$dg
			." AND heim = 0 "
			;
		$db->setQuery($query);
		$db->query();
	 }
		$query	= "UPDATE #__clm_rnd_man"
			." SET ptime = '".$n1time."'"
			." WHERE sid = ".$sid
			." AND lid = ".$lid
			." AND pdate > '1970-01-01' "
			." AND ptime = '00:00:00' "
			;
		$db->setQuery($query);
		$db->query();
	}

	switch ($task)
	{
		case 'apply':
		$msg = JText::_( 'PAARUNG_AENDERN_IST' );
		$link = 'index.php?option='.$option.'&section='.$section.'&cid[]='.$cid;
			break;
		case 'save':
		default:
		
		$msg = JText::_( 'PAARUNG_AENDERN_IST' );
		if ($row->liga_mt == 1) //mtmt
			//$link = 'index.php?option='.$option.'&section=mturniere';
			$link = 'index.php?option='.$option.'&view=view_tournament_group&liga=0';
		else
			//$link = 'index.php?option='.$option.'&section=ligen';
			$link = 'index.php?option='.$option.'&view=view_tournament_group&liga=1';
			break;
	}

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'PAARUNG_DT_LOG');
	$clmLog->params = array('sid' => $sid, 'lid' => $lid, 'cids' => $cid);
	$clmLog->write();

	$mainframe->redirect( $link, $msg);
	}
}
