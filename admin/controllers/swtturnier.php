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

class CLMControllerSWTTurnier extends JControllerLegacy
{
	function __construct() {		
		$this->app = JFactory::getApplication();
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		$_REQUEST['view'] = 'swtturnier';
		parent::display(); 
	} 
	
	function update() {		
		$swt_file = clm_core::$load->request_string('swt_file', '');
		$turnier = clm_core::$load->request_string('turnier', '0');
//		$_REQUEST['view'] = 'swtturnierinfo';
//		$_REQUEST['swt'] = $swt;
		$adminLink = new AdminLink();
		$adminLink->more = array('swt_file' => $swt_file, 'sid' => $sid, 'turnier' => $turnier, 'update' => 1);
		$adminLink->view = "swtturnierinfo";
		$adminLink->makeURL();
		$this->app->redirect($adminLink->url); 				
//		parent::display(); 		
	
	}
	
	function add() {		
		$swt_file = clm_core::$load->request_string('swt_file', '');
		
//		$_REQUEST['view'] = 'swtturnierinfo';
//		$_REQUEST['swt'] = $swt;
		$adminLink = new AdminLink();
		$adminLink->more = array('swt_file' => $swt_file, 'sid' => $sid);
		$adminLink->view = "swtturnierinfo";
		$adminLink->makeURL();
		$this->app->redirect($adminLink->url); 				
//		parent::display(); 		
	
	}
	
}
?>