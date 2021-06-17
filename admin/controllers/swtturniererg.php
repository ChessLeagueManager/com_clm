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

class CLMControllerSWTTurnierErg extends JControllerLegacy
{
	function __construct() {		
		$this->app = JFactory::getApplication();
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		$_REQUEST['view'] = 'swtturniererg';
		parent::display(); 
	} 
	
	function next() {
		$model = $this->getModel('swtturniererg');
		if ($model->store ()) {
			$rfirst = clm_core::$load->request_string('rfirst');
			$rlast  = clm_core::$load->request_string('rlast');
			$rrange = clm_core::$load->request_string('rrange');
			$rcount = clm_core::$load->request_string('rcount');
//			$this->_message = JText::_( 'SWT_STORE_SUCCESS' );
			if ($rlast == $rcount) {
//				$_REQUEST['view'] = 'swt';
				$adminLink = new AdminLink();
				$adminLink->more = array('swt_file' => $swt_file);
				$adminLink->view = "swt";
				$adminLink->makeURL();
				$this->app->enqueueMessage( JText::_( 'SWT_STORE_SUCCESS' ),'message' );
				$this->app->redirect($adminLink->url); 				
//				parent::display ();
			} else {
				$_GET['rfirst'] = ($rlast + 1);
				$_REQUEST['view'] = 'swtturniererg';
				parent::display ();
			}
		}
		else {
			$_REQUEST['view'] = 'swtturniererg';
			$this->app->enqueueMessage( JFactory::getDBO()->getErrorMsg() /*JText::_('SWT_STORE_ERROR_COPY_TOURNAMENT')*/,'error' );
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