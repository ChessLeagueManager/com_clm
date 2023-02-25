<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerArenaTurnier extends JControllerLegacy
{
	function __construct() {		
		$this->app = JFactory::getApplication();
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		$_REQUEST['view'] = 'arenaturnier';
		parent::display(); 
	} 
	
	function update() {		

		$arena_code = clm_core::$load->request_string('arena_code', '');
		$group = false; 
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$tid = clm_core::$load->request_int('tid', 0);
		if ($tid == 0) {
			$msg = JText::_( 'ARENA_UPDATE_MISSING' );
			$result[0] = false;
		} else {
			$result = clm_core::$api->db_arena_import($arena_code,$sid,$tid,$group,true,false);
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
			$clmLog->aktion = 'lichess-Import - '.$msg;
			$clmLog->params = array('sid' => $sid, 'tid' => $tid, 'arena_code' => $arena_code);
			$clmLog->write();
			$msg .= " (ID = ".$tid.")"; 
		} else {
			if (isset($result[2]) AND $result[0] !== true AND $result[1] == 'e_ArenaCodeNoValid') {
				$msg = JText::_( 'ARENA_CODE_NOVALID' ).' <b>'.$result[2].'</b>'; 
			} elseif ($msg == '')  $msg = JText::_( 'SWT_STORE_ERROR' );
		}
		
		$adminLink = new AdminLink();
		$adminLink->more = array('sid' => $sid, 'tid' => $tid, 'arena_code' => $arena_code);
		$adminLink->view = "arenaturnier";
		$adminLink->makeURL();
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	
	}
	
	function add() {		
		$arena_code = clm_core::$load->request_string('arena_code', '');
		$group = false;
		$sid = clm_core::$load->request_int('filter_saison', 0);

		$result = clm_core::$api->db_arena_import($arena_code,$sid,0,$group,false,false);
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
		
		// Log schreiben
		if ($result[0] AND isset($result[2]) AND $result[2] > 0) {
			$msg = JText::_( 'SWT_STORE_SUCCESS' );
			$clmLog = new CLMLog();
			$clmLog->aktion = 'Arena-Import - '.$msg;
			$clmLog->params = array('sid' => $sid, 'tid' => $new_ID, 'arena_code' => $arena_code);
			$clmLog->write();
			$msg .= " (ID = ".$new_ID.")"; 
		} else {
			if ($result[0] !== true AND $result[1] == 'e_ArenaCodeNoValid') {
				$msg = JText::_( 'ARENA_CODE_NOVALID' ).' <b>'.$result[2].'</b>'; }
			else $msg = JText::_( 'SWT_STORE_ERROR' );
		}

		$adminLink = new AdminLink();
		$adminLink->more = array('arena_code' => $arena_code);
		$adminLink->view = "arenaturnier";
		$adminLink->makeURL();
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	
	}
	
	function test() {		
		$arena_code = clm_core::$load->request_string('arena_code', '');
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$tid = clm_core::$load->request_string('tid', '');

		$result = clm_core::$api->db_arena_import($arena_code,$sid,0,false,false,true);
		
		if ($result[0] !== true) {
			if (isset($result[1]) AND $result[1] == 'e_ArenaCodeNoValid') {
				$msg = JText::_( 'ARENA_CODE_NOVALID' ).' <b>'.$result[2].'</b>'; }
			else $msg = JText::_( 'SWT_STORE_ERROR' );
			echo "<br><br>".$msg."<br><br>";
		}
		$_REQUEST['view'] = 'arenaturnier';
		$_GET['tid'] = $tid;
		$_REQUEST['arena_code'] = $arena_code;
		parent::display(); 		
	
	}

	function cancel() {		
		$arena_code = clm_core::$load->request_string('arena_code', '');
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$tid = clm_core::$load->request_int('tid', 0);
		
		$adminLink = new AdminLink();
		$adminLink->view = "swt";
		$adminLink->more = array('sid' => $sid, 'tid' => $tid, 'arena_code' => $arena_code);
		$adminLink->makeURL();
		$this->app->enqueueMessage( JText::_( 'ARENA_ACTION_CANCEL' ) );
		$this->app->redirect($adminLink->url); 		
	
	}
}
?>