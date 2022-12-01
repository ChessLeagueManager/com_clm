<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerSWMTurnier extends JControllerLegacy
{
	function __construct() {		
		$this->app = JFactory::getApplication();
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		$_REQUEST['view'] = 'swmturnier';
		parent::display(); 
	} 
	
	function update() {		
		$swm = clm_core::$load->request_string('swm', '');
		if (strtolower(JFile::getExt($swm) ) == 'tumx' OR strtolower(JFile::getExt($swm) ) == 'tutx') {
			$group = true; 
		} else { $group = false; }
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$tid = clm_core::$load->request_int('turnier', 0);
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		$result = clm_core::$api->db_swm_import($path.$swm,$sid,$tid,false,true,false);
		if (isset($result[2]) AND $result[2] > 0) {
			$new_ID = $result[2];
			If (!$group) { // Einzelturnier
				$turnier = new CLMTournament($new_ID,true);
				//Punkte und Feinwertungen neu berechnen
				$turnier->calculateRanking();
				//Rangliste neu berechnen
				$turnier->setRankingPositions();	
				//inoffizielle DWZ-Ermittlung
				clm_core::$api->direct("db_tournament_genDWZ",array($new_ID,false));
			} else { 		// Mannschaftsturnier
				//Punkte und Feinwertungen neu berechnen und Rangliste neu berechnen
				clm_core::$api->db_tournament_ranking($new_ID,true); 
				//inoffizielle DWZ-Ermittlung
				clm_core::$api->direct("db_tournament_genDWZ",array($new_ID,true));
			}
		}
		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		// Log schreiben
		$msg = JText::_( 'SWT_STORE_SUCCESS' );
		$clmLog = new CLMLog();
		$clmLog->aktion = 'SWM-Import - '.$msg;
		$clmLog->params = array('sid' => $sid, 'tid' => $tid, 'swm' => $swm);
		$clmLog->write();

//		$_REQUEST['view'] = 'swt';
		if (isset($result[2]) AND $result[2] > 0) { $htext = " (ID = ".$new_ID.")"; } else $htext = ""; 
		$this->app->enqueueMessage( JText::_( 'SWT_STORE_SUCCESS' ).$htext,'message' );
//		$_REQUEST['swm'] = $swm;
		$adminLink = new AdminLink();
		$adminLink->more = array('swm_file' => $swm_file, 'swm' => $swm );
		$adminLink->view = "swt";
		$adminLink->makeURL();
		$this->app->redirect($adminLink->url); 				
//		parent::display(); 		
	
	}
	
	function add() {		
		$swm = clm_core::$load->request_string('swm', '');
		if (strtolower(JFile::getExt($swm) ) == 'tumx' OR strtolower(JFile::getExt($swm) ) == 'tutx') {
			$group = true; 
		} else { $group = false; }
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		$result = clm_core::$api->db_swm_import($path.$swm,$sid,0,$group,false,false);
//echo "<br>result:"; var_dump($result); //die();
		if (isset($result[2]) AND $result[2] > 0) {
			$new_ID = $result[2];
			
			If (!$group) { // Einzelturnier
				$turnier = new CLMTournament($new_ID,true);
				//Punkte und Feinwertungen neu berechnen
				$turnier->calculateRanking();
				//Rangliste neu berechnen
				$turnier->setRankingPositions();	
				//inoffizielle DWZ-Ermittlung
				clm_core::$api->direct("db_tournament_genDWZ",array($new_ID,false));
			} else { 		// Mannschaftsturnier
				//Punkte und Feinwertungen neu berechnen und Rangliste neu berechnen
				clm_core::$api->db_tournament_ranking($new_ID,true); 
				//inoffizielle DWZ-Ermittlung
				clm_core::$api->direct("db_tournament_genDWZ",array($new_ID,true));
			}
		}
		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		// Log schreiben
		$msg = JText::_( 'SWT_STORE_SUCCESS' );
		$clmLog = new CLMLog();
		$clmLog->aktion = 'SWM-Import - '.$msg;
		$clmLog->params = array('sid' => $sid, 'tid' => $new_ID, 'swm' => $swm);
		$clmLog->write();

//		$_REQUEST['view'] = 'swt';
		if (isset($result[2]) AND $result[2] > 0) { $htext = " (ID = ".$new_ID.")"; } else $htext = ""; 
		$this->app->enqueueMessage( JText::_( 'SWT_STORE_SUCCESS' ).$htext,'message' );
//		$_REQUEST['swm'] = $swm;
		$adminLink = new AdminLink();
		$adminLink->more = array('swm_file' => $swm_file, 'swm' => $swm );
		$adminLink->view = "swt";
		$adminLink->makeURL();
		$this->app->redirect($adminLink->url); 				
//		parent::display(); 		
	
	}
	
	function test() {		
		$swm = clm_core::$load->request_string('swm', '');
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$uturnier = clm_core::$load->request_string('turnier', '');
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		$result = clm_core::$api->db_swm_import($path.$swm,$sid,0,false,false,true);
		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		$_REQUEST['view'] = 'swmturnier';
		$_GET['turnier'] = $uturnier;
		$_REQUEST['swm'] = $swm;
		$_REQUEST['swm_filename'] = $swm;
		parent::display(); 		
	
	}

	function cancel() {		
		$swm = clm_core::$load->request_string('swm', '');
		$sid = clm_core::$load->request_int('filter_saison', 0);
		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		$_REQUEST['view'] = 'swt';
		JFactory::getApplication()->enqueueMessage( JText::_( 'SWM_ACTION_CANCEL' ),'message' );
//		$_REQUEST['swm'] = $swm;
		$adminLink = new AdminLink();
		$adminLink->more = array('swm_file' => $swm_file, 'swm' => $swm );
		$adminLink->view = "swt";
		$adminLink->makeURL();
		$this->app->redirect($adminLink->url); 				
//		parent::display(); 		
	
	}
}
?>