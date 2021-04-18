<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerArenaTurnier extends JControllerLegacy
{
	function __construct() {		
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		$_REQUEST['view'] = 'arenaturnier';
		parent::display(); 
	} 
	
	function update() {		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		$arena = clm_core::$load->request_string('arena', '');
		$group = false; 
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$tid = clm_core::$load->request_int('turnier', 0);
		if ($tid == 0) {
			$msg = JText::_( 'ARENA_UPDATE_MISSING' );
			$result[0] = false;
		} else {
			$result = clm_core::$api->db_arena_import($arena,$sid,$tid,$group,true,false);
			if ($result[0] AND isset($result[2]) AND $result[2] > 0) {
				$new_ID = $result[2];
				If (!$group) { // Einzelturnier
					//inoffizielle DWZ-Ermittlung
//					clm_core::$api->direct("db_tournament_genDWZ",array($new_ID,false));
					$turnier = new CLMTournament($new_ID,true);
					//Punkte und Feinwertungen neu berechnen
					$turnier->calculateRanking();
					//Rangliste neu berechnen
					$turnier->setRankingPositions();	
				} else { 		// Mannschaftsturnier
					//inoffizielle DWZ-Ermittlung
//					clm_core::$api->direct("db_tournament_genDWZ",array($new_ID,true));
					//Punkte und Feinwertungen neu berechnen und Rangliste neu berechnen
					clm_core::$api->db_tournament_ranking($new_ID,true); 
				}
			}	
		}
		// Log schreiben
		if ($result[0] AND isset($result[2]) AND $result[2] > 0) {
			$msg = JText::_( 'SWT_STORE_SUCCESS' );
			$clmLog = new CLMLog();
			$clmLog->aktion = 'Arena-Import - '.$msg;
			$clmLog->params = array('sid' => $sid, 'tid' => $tid, 'arena' => $arena);
			$clmLog->write();
			$msg .= " (ID = ".$tid.")"; 
		} else {
			if (isset($result[2]) AND $result[0] !== true AND $result[1] == 'e_ArenaCodeNoValid') {
				$msg = JText::_( 'ARENA_CODE_NOVALID' ).' <b>'.$result[2].'</b>'; 
			} elseif ($msg == '')  $msg = JText::_( 'SWT_STORE_ERROR' );
		}
		
		JFactory::getApplication()->enqueueMessage( $msg,'message' );
		$_REQUEST['view'] = 'swt';
		$_REQUEST['arena'] = $arena;		
		parent::display(); 		
	
	}
	
	function add() {		
		$arena = clm_core::$load->request_string('arena', '');
		$group = false;
		$sid = clm_core::$load->request_int('filter_saison', 0);
//		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		$result = clm_core::$api->db_arena_import($arena,$sid,0,$group,false,false);
//echo "<br>result:"; var_dump($result); //die();
		if ($result[0] AND isset($result[2]) AND $result[2] > 0) {
			$new_ID = $result[2];
			
			If (!$group) { // Einzelturnier
				//inoffizielle DWZ-Ermittlung
//				clm_core::$api->direct("db_tournament_genDWZ",array($new_ID,false));
				$turnier = new CLMTournament($new_ID,true);
				//Punkte und Feinwertungen neu berechnen
				$turnier->calculateRanking();
				//Rangliste neu berechnen
				$turnier->setRankingPositions();	
			} else { 		// Mannschaftsturnier
				//inoffizielle DWZ-Ermittlung
//				clm_core::$api->direct("db_tournament_genDWZ",array($new_ID,true));
				//Punkte und Feinwertungen neu berechnen und Rangliste neu berechnen
				clm_core::$api->db_tournament_ranking($new_ID,true); 
			}
		}
		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		// Log schreiben
		if ($result[0] AND isset($result[2]) AND $result[2] > 0) {
			$msg = JText::_( 'SWT_STORE_SUCCESS' );
			$clmLog = new CLMLog();
			$clmLog->aktion = 'Arena-Import - '.$msg;
			$clmLog->params = array('sid' => $sid, 'tid' => $new_ID, 'arena' => $arena);
			$clmLog->write();
			$msg .= " (ID = ".$new_ID.")"; 
		} else {
			if ($result[0] !== true AND $result[1] == 'e_ArenaCodeNoValid') {
				$msg = JText::_( 'ARENA_CODE_NOVALID' ).' <b>'.$result[2].'</b>'; }
			else $msg = JText::_( 'SWT_STORE_ERROR' );
		}
		JFactory::getApplication()->enqueueMessage( $msg,'message' );
		$_REQUEST['view'] = 'swt';
		$_REQUEST['arena'] = $arena;
		parent::display(); 		
	
	}
	
	function test() {		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		$arena = clm_core::$load->request_string('arena', '');
//echo "<br>0arena_controller"; var_dump($arena); //die();
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$uturnier = clm_core::$load->request_string('turnier', '');
//		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		$result = clm_core::$api->db_arena_import($arena,$sid,0,false,false,true);
		
		if ($result[0] !== true) {
			if (isset($result[1]) AND $result[1] == 'e_ArenaCodeNoValid') {
				$msg = JText::_( 'ARENA_CODE_NOVALID' ).' <b>'.$result[2].'</b>'; }
			else $msg = JText::_( 'SWT_STORE_ERROR' );
			echo "<br><br>".$msg."<br><br>";
		}
		$_REQUEST['view'] = 'arenaturnier';
		$_GET['turnier'] = $uturnier;
		$_REQUEST['arena'] = $arena;
		parent::display(); 		
	
	}

	function cancel() {		
		$arena = clm_core::$load->request_string('arena', '');
		$sid = clm_core::$load->request_int('filter_saison', 0);
		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		$_REQUEST['view'] = 'swt';
		JFactory::getApplication()->enqueueMessage( JText::_( 'ARENA_ACTION_CANCEL' ),'message' );
		$_REQUEST['arena'] = $arena;
		parent::display(); 		
	
	}
}
?>