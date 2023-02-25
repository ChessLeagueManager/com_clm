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

class CLMControllerSWT extends JControllerLegacy
{
	function __construct() {		
		$this->app = JFactory::getApplication();
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		$_REQUEST['view'] = 'swt';
		parent::display(); 
	} 
	
	function upload() {
		$model = $this->getModel('swt');
		$msg = $model->upload();
		$filename = clm_core::$load->request_string('filename', '');
		
		$adminLink = new AdminLink();
		$adminLink->more = array('filename' => $filename);
		$adminLink->view = "swt";
		$adminLink->makeURL();
			
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	}
	
	function delete(){
		$model = $this->getModel('swt');
		$msg = $model->delete();
		
		$adminLink = new AdminLink();
		$adminLink->view = "swt";
		$adminLink->makeURL();
			
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	}
	
	function import() {
		$model = $this->getModel('swt');
		$type = $model->import();
		$swt_file = clm_core::$load->request_string('swt_file', '');
		
		if($type == 0) {
//			$_REQUEST['view'] = 'swtturnier';
			$adminLink = new AdminLink();
			$adminLink->more = array('swt_file' => $swt_file);
			$adminLink->view = "swtturnier";
			$adminLink->makeURL();
			$this->app->redirect($adminLink->url); 		
//			parent::display();
		} elseif($type == 255){
//			$_REQUEST['view'] = 'swtliga';
			$adminLink = new AdminLink();
			$adminLink->more = array('swt_file' => $swt_file);
			$adminLink->view = "swtliga";
			$adminLink->makeURL();
			$this->app->redirect($adminLink->url); 		
//			parent::display();
		} else {
			$adminLink = new AdminLink();
			$adminLink->view = "swt";
			$adminLink->makeURL();
			
			$msg = JText::_( 'SWT_FILE_ERROR' ); 
			
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url); 		
		}				
	}
	
	
	function trf_import() {
		$trf_file = clm_core::$load->request_string('trf_file', '');
		$adminLink = new AdminLink();
		$adminLink->more = array('trf_file' => $trf_file);
		$adminLink->view = "trfturnier";
		$adminLink->makeURL();
		$this->app->redirect($adminLink->url); 		
						
	}
	
	function swm_import() {
		$swm_file = clm_core::$load->request_string('swm_file', '');
		$adminLink = new AdminLink();
		$adminLink->more = array('swm_file' => $swm_file);
		$adminLink->view = "swmturnier";
		$adminLink->makeURL();
		$this->app->redirect($adminLink->url); 		
						
	}
	
	function pgn_import() {
		$pgn_file = clm_core::$load->request_string('pgn_file', '');
		$adminLink = new AdminLink();
		$adminLink->more = array('pgn_file' => $pgn_file);
		$adminLink->view = "pgnimport";
		$adminLink->makeURL();
		$this->app->redirect($adminLink->url); 		
						
	}
	
	function arena_import() {
		$arena_code = clm_core::$load->request_string('arena_code', '');
		$adminLink = new AdminLink();
		$adminLink->more = array('arena_code' => $arena_code);
		$adminLink->view = "arenaturnier";
		$adminLink->makeURL();
		$this->app->redirect($adminLink->url); 		
						
	}
	
}
?>