<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
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
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		$_REQUEST['view'] = 'trfturnier';
		parent::display(); 
	} 
	
	function update() {		
		$trf = clm_core::$load->request_string('trf', '');
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$tid = clm_core::$load->request_int('turnier', 0);
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		$result = clm_core::$api->db_trf_import($path.$trf,$sid,$tid,false,true,false);
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
		$clmLog->params = array('sid' => $sid, 'tid' => $tid, 'trf' => $trf);
		$clmLog->write();

		$_REQUEST['view'] = 'swt';
		if (isset($result[2]) AND $result[2] > 0) { $htext = " (ID = ".$new_ID.")"; } else $htext = ""; 
		JFactory::getApplication()->enqueueMessage( JText::_( 'SWT_STORE_SUCCESS' ).$htext,'message' );
		$_REQUEST['trf'] = $trf;
		
		parent::display(); 		
	
	}
	
	function add() {		
		$trf = clm_core::$load->request_string('trf', '');
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		$result = clm_core::$api->db_trf_import($path.$trf,$sid,0,false,false,false);
//echo "<br>result:"; var_dump($result); //die();
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
		$clmLog->params = array('sid' => $sid, 'tid' => $new_ID, 'trf' => $trf);
		$clmLog->write();

		$_REQUEST['view'] = 'swt';
		if (isset($result[2]) AND $result[2] > 0) { $htext = " (ID = ".$new_ID.")"; } else $htext = ""; 
		JFactory::getApplication()->enqueueMessage( JText::_( 'SWT_STORE_SUCCESS' ).$htext,'message' );
		$_REQUEST['trf'] = $trf;
		parent::display(); 		
	
	}
	
	function test() {		
		$trf = clm_core::$load->request_string('trf', '');
		$sid = clm_core::$load->request_int('filter_saison', 0);
		$uturnier = clm_core::$load->request_string('turnier', '');
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		$result = clm_core::$api->db_trf_import($path.$trf,$sid,0,false,false,true);
		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		$_REQUEST['view'] = 'trfturnier';
		$_GET['turnier'] = $uturnier;
		$_REQUEST['trf'] = $trf;
		$_REQUEST['trf_filename'] = $trf;
		parent::display(); 		
	
	}

	function cancel() {		
		$trf = clm_core::$load->request_string('trf', '');
		$sid = clm_core::$load->request_int('filter_saison', 0);
		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		$_REQUEST['view'] = 'swt';
		JFactory::getApplication()->enqueueMessage( JText::_( 'TRF_ACTION_CANCEL' ),'message' );
		$_REQUEST['trf'] = $trf;
		parent::display(); 		
	
	}
}
?>