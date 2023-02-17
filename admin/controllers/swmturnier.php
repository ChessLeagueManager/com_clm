<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
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
		$swm_file = clm_core::$load->request_string('swm_file', '');
		if (strtolower(JFile::getExt($swm_file) ) == 'tumx' OR strtolower(JFile::getExt($swm_file) ) == 'tutx') {
			$group = true; 
		} else { $group = false; }
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$tid = clm_core::$load->request_int('turnier', 0);
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		if ($swm_file == '') {
			$adminLink = new AdminLink();
			$adminLink->view = "swmturnier";
			$adminLink->makeURL();			
			$msg = JText::_( 'SWM_FILE_ERROR' ); 			
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url); 		
		}
		$_REQUEST['swm_file'] = $swm_file;
		if ($tid == 0) {
			$adminLink = new AdminLink();
			$adminLink->view = "swmturnier";
			$adminLink->makeURL();			
			$msg = JText::_( 'SWT_TOURNAMENT_ERROR' ); 			
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url); 		
		}
		$_REQUEST['tid'] = $tid;
		$result = clm_core::$api->db_swm_import($path.$swm_file,$sid,$tid,$group,true,false);
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
		$clmLog->params = array('sid' => $sid, 'tid' => $tid, 'swm_file' => $swm_file);
		$clmLog->write();

		$_REQUEST['view'] = 'swtturnier';
		if (isset($result[2]) AND $result[2] > 0) { $htext = " (ID = ".$new_ID.")"; } else $htext = ""; 
		$this->app->enqueueMessage( JText::_( 'SWT_STORE_SUCCESS' ).$htext,'message' );
		$_REQUEST['swm_file'] = $swm_file;

		parent::display();
	}
	
	function add() {		
		$swm_file = clm_core::$load->request_string('swm_file', '');
		if (strtolower(JFile::getExt($swm_file) ) == 'tumx' OR strtolower(JFile::getExt($swm_file) ) == 'tutx') {
			$group = true; 
		} else { $group = false; }
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		if ($swm_file == '') {
			$adminLink = new AdminLink();
			$adminLink->view = "swmturnier";
			$adminLink->makeURL();			
			$msg = JText::_( 'SWM_FILE_ERROR' ); 			
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url); 		
		}
		$result = clm_core::$api->db_swm_import($path.$swm_file,$sid,0,$group,false,false);

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
		$clmLog->params = array('sid' => $sid, 'tid' => $new_ID, 'swm_file' => $swm_file);
		$clmLog->write();

		$_REQUEST['view'] = 'swmturnier';
		if (isset($result[2]) AND $result[2] > 0) { $htext = " (ID = ".$new_ID.")"; } else $htext = ""; 
		$this->app->enqueueMessage( JText::_( 'SWT_STORE_SUCCESS' ).$htext,'message' );
		$_REQUEST['swm_file'] = $swm_file;

		parent::display();
	}
	
	function test() {		
		$swm_file = clm_core::$load->request_string('swm_file', '');
		if (strtolower(JFile::getExt($swm_file) ) == 'tumx' OR strtolower(JFile::getExt($swm_file) ) == 'tutx') {
			$group = true; 
		} else { $group = false; }
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$uturnier = clm_core::$load->request_string('turnier', '');
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		$result = clm_core::$api->db_swm_import($path.$swm_file,$sid,0,$group,false,true);
		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		$_POST['view'] = 'swmturnier';
		$_GET['turnier'] = $uturnier;
		$_POST['swm_file'] = $swm_file;

		parent::display(); 		
	
	}

	function cancel() {		
		$swm_file = clm_core::$load->request_string('swm_file', '');
		$sid = clm_core::$load->request_int('filter_saison', 0);
		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		$adminLink = new AdminLink();
		$adminLink->view = "swt";
		$adminLink->more = array('swm_file' => $swm_file );
		$adminLink->makeURL();
		$this->app->enqueueMessage( JText::_( 'SWM_ACTION_CANCEL' ),'message' );
		$this->app->redirect($adminLink->url); 				
	
	}

	function swm_upload() {
		$model = $this->getModel('swmturnier');
		$msg = $model->swm_upload();
		$swm_file = clm_core::$load->request_string('swm_file', '');
		
		$adminLink = new AdminLink();
		$adminLink->more = array('swm_file' => $swm_file);
		$adminLink->view = "swmturnier";
		$adminLink->makeURL();
			
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	}
	
	function swm_delete(){
		$model = $this->getModel('swmturnier');
		$msg = $model->swm_delete();
		
		$adminLink = new AdminLink();
		$adminLink->view = "swmturnier";
		$adminLink->makeURL();
			
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	}
}
?>