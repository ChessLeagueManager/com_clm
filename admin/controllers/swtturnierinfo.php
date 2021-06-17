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
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerSWTTurnierInfo extends JControllerLegacy
{
	function __construct() {		
		$this->app = JFactory::getApplication();
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		$_REQUEST['view'] = 'swtturnierinfo';
		parent::display(); 
	} 
	
	function next() {
		$model = $this->getModel('swtturnierinfo');
		$swt_file = clm_core::$load->request_string('swt_file', '');
		$update = clm_core::$load->request_string('update', '0');
		$tid = clm_core::$load->request_string('tid', '0');
		if ($model->store ()) {
//			$_REQUEST['view'] = 'swtturniertlnr';
			$swt_tid = clm_core::$load->request_int('swt_tid', '0');
			$sid = clm_core::$load->request_string('sid', '0');
			$params = $_POST['params'];
			$useAsTWZ = $params['useAsTWZ'];
			$adminLink = new AdminLink();
			$adminLink->more = array('swt_file' => $swt_file, 'tid' => $tid, 'swt_tid' => $swt_tid, 'sid' => $sid, 'update' => $update, 'useAsTWZ' => $useAsTWZ);
			$adminLink->view = "swtturniertlnr";
			$adminLink->makeURL();
//			$this->_message = JText::_( 'SWT_STORE_SUCCESS' );
			$this->app->redirect($adminLink->url); 				
//			parent::display ();
		}
		else {
			$_REQUEST['view'] = 'swtturnierinfo';
			$msg = JText::_( 'SWT_STORE_ERROR' );
			$this->app->enqueueMessage( $msg );
//			$this->_message = JText::_( 'SWT_STORE_ERROR' );
			parent::display ();
		}
	
	}
	
	function cancel() {		
		$adminLink = new AdminLink ();
		$adminLink->view = 'swt';
		$adminLink->makeURL ();
		
		$msg = JText::_( 'SWT_CANCEL_MSG' );
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	
	}
	
}
?>