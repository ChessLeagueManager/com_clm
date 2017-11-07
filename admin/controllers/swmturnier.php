<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2017 CLM Team.  All rights reserved
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
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		JRequest::setVar('view','swmturnier');
		parent::display(); 
	} 
	
	function update() {		
		$swm = JRequest::getVar('swm', '');
		$sid = JRequest::getVar('filter_saison', '0');
		$tid = JRequest::getVar('turnier', '0');
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		$result = clm_core::$api->db_swm_import($path.$swm,$sid,$tid,false,true,false);
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

		JRequest::setVar('view', 'swt');
		JFactory::getApplication()->enqueueMessage( JText::_( 'SWT_STORE_SUCCESS' ),'message' );
		JRequest::setVar('swm' , $swm);
		
		parent::display(); 		
	
	}
	
	function add() {		
		$swm = JRequest::getVar('swm', '');
		$sid = JRequest::getVar('filter_saison', '0');
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		$result = clm_core::$api->db_swm_import($path.$swm,$sid,0,false,false,false);
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

		JRequest::setVar('view', 'swt');
		JFactory::getApplication()->enqueueMessage( JText::_( 'SWT_STORE_SUCCESS' ),'message' );
		JRequest::setVar('swm' , $swm);
		parent::display(); 		
	
	}
	
	function test() {		
		$swm = JRequest::getVar('swm', '');
		$sid = JRequest::getVar('filter_saison', '0');
		$uturnier = JRequest::getVar('turnier', '');
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		$result = clm_core::$api->db_swm_import($path.$swm,$sid,0,false,false,true);
		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		JRequest::setVar('view', 'swmturnier');
		JRequest::setVar('turnier' , $uturnier);
		JRequest::setVar('swm' , $swm);
		JRequest::setVar('swm_filename' , $swm);
		parent::display(); 		
	
	}

	function cancel() {		
		$swm = JRequest::getVar('swm', '');
		$sid = JRequest::getVar('filter_saison', '0');
		
		$language = JFactory::getLanguage();
		$language->load('com_clm');
		$language->load('com_clm.swtimport');	

		JRequest::setVar('view', 'swt');
		JFactory::getApplication()->enqueueMessage( JText::_( 'SWM_ACTION_CANCEL' ),'message' );
		JRequest::setVar('swm' , $swm);
		parent::display(); 		
	
	}
}
?>