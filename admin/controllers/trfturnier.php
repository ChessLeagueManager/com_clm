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

class CLMControllerTRFTurnier extends JControllerLegacy
{
	function __construct() {		
		$this->app = JFactory::getApplication();
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		$_REQUEST['view'] = 'trfturnier';
		parent::display(); 
	} 
	
	function update() {		
		$trf_file = clm_core::$load->request_string('trf_file', '');
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$tid = clm_core::$load->request_int('turnier', 0);
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		if ($trf_file == '') {
			$adminLink = new AdminLink();
			$adminLink->view = "trfturnier";
			$adminLink->makeURL();			
			$msg = JText::_( 'TRF_FILE_ERROR' ); 			
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url); 		
		}
		$_REQUEST['trf_file'] = $trf_file;
		if ($tid == 0) {
			$adminLink = new AdminLink();
			$adminLink->view = "trfturnier";
			$adminLink->makeURL();			
			$msg = JText::_( 'SWT_TOURNAMENT_ERROR' ); 			
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url); 		
		}
		$_REQUEST['tid'] = $tid;
		$result = clm_core::$api->db_trf_import($path.$trf_file,$sid,$tid,false,true,false);
		if (isset($result[2]) AND $result[2] > 0) {
			$new_ID = $result[2];
			$turnier = new CLMTournament($new_ID,true);
		
			//Punkte und Feinwertungen neu berechnen
			$turnier->calculateRanking();
		
			//Rangliste neu berechnen
			$turnier->setRankingPositions();

			//inoffizielle DWZ-Ermittlung
			clm_core::$api->direct("db_tournament_genDWZ",array($new_ID,false));
		}
		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		// Log schreiben
		$msg = JText::_( 'SWT_STORE_SUCCESS' );
		$clmLog = new CLMLog();
		$clmLog->aktion = 'TRF-Import - '.$msg;
		$clmLog->params = array('sid' => $sid, 'tid' => $tid, 'trf_file' => $trf_file);
		$clmLog->write();

		$_REQUEST['view'] = 'trfturnier';
		if (isset($result[2]) AND $result[2] > 0) { $htext = " (ID = ".$new_ID.")"; } else $htext = ""; 
		$this->app->enqueueMessage( JText::_( 'SWT_STORE_SUCCESS' ).$htext,'message' );
		$_REQUEST['trf_file'] = $trf_file;
		
		parent::display();
	}
	
	function add() {		
		$trf_file = clm_core::$load->request_string('trf_file', '');
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		if ($trf_file == '') {
			$adminLink = new AdminLink();
			$adminLink->view = "trfturnier";
			$adminLink->makeURL();			
			$msg = JText::_( 'TRF_FILE_ERROR' ); 			
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url); 		
		}
		$result = clm_core::$api->db_trf_import($path.$trf_file,$sid,0,false,false,false);

		if (isset($result[2]) AND $result[2] > 0) {
			$new_ID = $result[2];
			$turnier = new CLMTournament($new_ID,true);
		
			//Punkte und Feinwertungen neu berechnen
			$turnier->calculateRanking();
		
			//Rangliste neu berechnen
			$turnier->setRankingPositions();

			//inoffizielle DWZ-Ermittlung
			clm_core::$api->direct("db_tournament_genDWZ",array($new_ID,false));
		}
		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		// Log schreiben
		$msg = JText::_( 'SWT_STORE_SUCCESS' );
		$clmLog = new CLMLog();
		$clmLog->aktion = 'TRF-Import - '.$msg;
		$clmLog->params = array('sid' => $sid, 'tid' => $new_ID, 'trf_file' => $trf_file);
		$clmLog->write();

		$_REQUEST['view'] = 'trfturnier';
		if (isset($result[2]) AND $result[2] > 0) { $htext = " (ID = ".$new_ID.")"; } else $htext = ""; 
		JFactory::getApplication()->enqueueMessage( JText::_( 'SWT_STORE_SUCCESS' ).$htext,'message' );
		$_REQUEST['trf_file'] = $trf_file;

		parent::display();
	}
	
	function test() {		
		$trf_file = clm_core::$load->request_string('trf_file', '');
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$uturnier = clm_core::$load->request_string('turnier', '');
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		$result = clm_core::$api->db_trf_import($path.$trf_file,$sid,0,false,false,true);
		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		$_POST['view'] = 'trfturnier';
		$_GET['turnier'] = $uturnier;
		$_POST['trf_file'] = $trf_file;

		parent::display(); 		
	
	}

	function cancel() {		
		$trf_file = clm_core::$load->request_string('trf_file', '');
		$sid = clm_core::$load->request_int('filter_saison', 0);
		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		$adminLink = new AdminLink();
		$adminLink->view = "swt";
		$adminLink->more = array('swm_file' => $swm_file );
		$adminLink->makeURL();
		$this->app->enqueueMessage( JText::_( 'TRF_ACTION_CANCEL' ),'message' );
		$this->app->redirect($adminLink->url); 		
	
	}

	function trf_upload() {
		$model = $this->getModel('trfturnier');
		$msg = $model->trf_upload();
		$trf_file = clm_core::$load->request_string('trf_file', '');
		
		$adminLink = new AdminLink();
		$adminLink->more = array('trf_file' => $trf_file);
		$adminLink->view = "trfturnier";
		$adminLink->makeURL();
			
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	}
	
	function trf_delete(){
		$model = $this->getModel('trfturnier');
		$msg = $model->trf_delete();
		
		$adminLink = new AdminLink();
		$adminLink->view = "trfturnier";
		$adminLink->makeURL();
			
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	}


}
?>