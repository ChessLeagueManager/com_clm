<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
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
		
		if($type == 0) {
			$_REQUEST['view'] = 'swtturnier';
			parent::display();
		} elseif($type == 255){
			$_REQUEST['view'] = 'swtliga';
			parent::display();
		} else {
			$adminLink = new AdminLink();
			$adminLink->view = "swt";
			$adminLink->makeURL();
			
			$msg = JText::_( 'SWT_FILE_ERROR' ); 
			
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url); 		
		}				
	}
	
	function pgn_upload() {
		$model = $this->getModel('swt');
		$msg = $model->pgn_upload();
		$pgn_filename = clm_core::$load->request_string('pgn_filename', '');
		
		$adminLink = new AdminLink();
		$adminLink->more = array('pgn_filename' => $pgn_filename);
		$adminLink->view = "swt";
		$adminLink->makeURL();
			
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	}
	
	function pgn_delete(){
		$model = $this->getModel('swt');
		$msg = $model->pgn_delete();
		
		$adminLink = new AdminLink();
		$adminLink->view = "swt";
		$adminLink->makeURL();
			
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	}
	
	function pgn_import() {
		$model = $this->getModel('swt');
		$type = $model->pgn_import();
		$type = 0;
		if($type == 0) {
			$_REQUEST['task'] = 'import';
			$_REQUEST['view'] = 'pgnimport';
			parent::display();
		} elseif($type == 255){
			$_REQUEST['view'] = 'swtliga';
			parent::display();
		} else {
			$adminLink = new AdminLink();
			$adminLink->view = "swt";
			$adminLink->makeURL();
			
			$msg = JText::_( 'PGN_FILE_ERROR!' ); 
			
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url); 		
		}				
	}
	
	function pgn_service() {
		$model = $this->getModel('swt');
		$type = $model->pgn_import();
		$type = 0;
		if($type == 0) {
			$_REQUEST['task'] = 'service';
			$_REQUEST['view'] = 'pgnimport';
			parent::display();
		} elseif($type == 255){
			$_REQUEST['view'] = 'swtliga';
			parent::display();
		} else {
			$adminLink = new AdminLink();
			$adminLink->view = "swt";
			$adminLink->makeURL();
			
			$msg = JText::_( 'PGN_FILE_ERROR!' ); 
			
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url); 		
		}				
	}
	
	function swm_upload() {
		$model = $this->getModel('swt');
		$msg = $model->swm_upload();
		$swm_filename = clm_core::$load->request_string('swm_filename', '');
		
		$adminLink = new AdminLink();
		$adminLink->more = array('swm_filename' => $swm_filename);
		$adminLink->view = "swt";
		$adminLink->makeURL();
			
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	}
	
	function swm_delete(){
		$model = $this->getModel('swt');
		$msg = $model->swm_delete();
		
		$adminLink = new AdminLink();
		$adminLink->view = "swt";
		$adminLink->makeURL();
			
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	}
	
	function swm_import() {
		$model = $this->getModel('swt');
		//$type = $model->swm_import();
		$type = 0;
		if($type == 0) {
			$_REQUEST['view'] = 'swmturnier';
			parent::display();
		} elseif($type == 255){
			$_REQUEST['view'] = 'swtliga';
			parent::display();
		} else {
			$adminLink = new AdminLink();
			$adminLink->view = "swt";
			$adminLink->makeURL();
			
			$msg = JText::_( 'SWM_FILE_ERROR!' ); 
			
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url); 		
		}				
	}
	
}
?>