<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllertermineimport extends JControllerLegacy
{
	function __construct() {		
		$this->app = JFactory::getApplication();
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		$_REQUEST['view'] = 'termineimport';
		parent::display(); 
	} 
	
	function upload() {
		$model = $this->getModel('termineimport');
		$msg = $model->upload();
		$filename = clm_core::$load->request_string('filename', '');
		
		$adminLink = new AdminLink();
		$adminLink->more = array('filename' => $filename);
		$adminLink->view = "termineimport";
		$adminLink->makeURL();
			
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	}
	
	function delete(){
		$model = $this->getModel('termineimport');
		$msg = $model->delete();
		
		$adminLink = new AdminLink();
		$adminLink->view = "termineimport";
		$adminLink->makeURL();
			
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	}
	
	
	function import() {
		$lang = clm_core::$lang->terminliste;
		//Name der zu importierenden Datei wird geladen
		$filename = clm_core::$load->request_string('termine_file', '');
		//pgn-Verzeichnis
		$path = JPATH_COMPONENT . DS . "pgn" . DS;
		$result = clm_core::$api->db_term_import($path.$filename,false);		
		
		$adminLink = new AdminLink();
		$adminLink->view = "terminemain";
		$adminLink->makeURL();
		if($result[0] === true ) {
			$msg = $result[2].' '.$lang->{$result[1]};
		} else {			
			$msg = $lang->error.': '.$lang->$result[1]; 
		}	
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	}
	
}
?>