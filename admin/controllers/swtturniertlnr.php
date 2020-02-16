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
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerSWTTurnierTlnr extends JControllerLegacy
{
	function __construct() {		
		$this->app = JFactory::getApplication();
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		$_REQUEST['view'] = 'swtturniertlnr';
		parent::display(); 
	} 
	
	function next() {
		$model = $this->getModel('swtturniertlnr');
		if ($model->store ()) {
			$pfirst = clm_core::$load->request_string('pfirst');
			$plast  = clm_core::$load->request_string('plast');
			$prange = clm_core::$load->request_string('prange');
			$pcount = clm_core::$load->request_string('pcount');
			$this->_message = JText::_( 'SWT_STORE_SUCCESS' );
			if ($plast == $pcount) {
				$_REQUEST['view'] = 'swtturniererg';
				parent::display ();
			} else {
				$_GET['pfirst'] = ($plast + 1);
				$_REQUEST['view'] = 'swtturniertlnr';
				parent::display ();
			}
		}
		else {
			$_REQUEST['view'] = 'swtturniererg';
			$this->_message = JText::_( 'SWT_STORE_ERROR' );
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