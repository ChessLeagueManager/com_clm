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
			$swt_file = clm_core::$load->request_string('swt_file', '');
			$update = clm_core::$load->request_string('update', 0);
			$tid = clm_core::$load->request_string('tid', 0);
			$swt_tid = clm_core::$load->request_string('swt_tid', 0);
			$sid = clm_core::$load->request_string('sid', 0);
			$useAsTWZ = clm_core::$load->request_string('useAsTWZ', '0');
//			$this->_message = JText::_( 'SWT_STORE_SUCCESS' );
			if ($plast == $pcount) {
//				$_REQUEST['view'] = 'swtturniererg';
				$adminLink = new AdminLink();
				$adminLink->more = array('swt_file' => $swt_file, 'tid' => $tid, 'swt_tid' => $swt_tid, 'sid' => $sid, 'update' => $update,
					'useAsTWZ' => $useAsTWZ, 'pfirst' => $pfirst, 'plast' => $plast, 'prange' => $prange, 'pcount' => $pcount);
				$adminLink->view = "swtturniererg";
				$adminLink->makeURL();
				$this->app->redirect($adminLink->url); 				
//				parent::display ();
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