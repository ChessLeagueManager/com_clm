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

class CLMControllerSWTLigasave extends JControllerLegacy
{
	function __construct() {
				
		parent::__construct();
		
	}
	
	function display($cachable = false, $urlparams = array()) {
	
		$_REQUEST['view'] = 'swtligasave';
		parent::display();
		
	}
	
	function save () {
		
		$app = JFactory::getApplication();
		$model = $this->getModel('swtligasave');
		if ($model->finalCopy () && $model->rundenTermine () && $model->userAnlegen ()) {
//			$_REQUEST['view'] = 'swt';
			$msg = JText::_( 'SWT_STORE_SUCCESS' );
//			$htext = " (ID = ".$lid.")";
		}
		else {
			$_REQUEST['view'] = 'swtligaerg';
			$msg = JText::_( 'SWT_STORE_ERROR' );			
		}
		$sid = clm_core::$load->request_int('sid');
		$lid = clm_core::$load->request_int('lid');
		$swt_file = clm_core::$load->request_string('swt_file');
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = 'SWT-Import - '.$msg;
		$clmLog->params = array('sid' => $sid, 'lid' => $lid, 'swt_file' => $swt_file);
		$clmLog->write();
		$htext = " (ID = ".$lid.")";
		$app->enqueueMessage( $msg.$htext,'message' );

		$adminLink = new AdminLink();
		$adminLink->more = array('swt_file' => $swt_file, 'sid' => $sid, 'lid' => $lid);
		$adminLink->view = "swt";
		$adminLink->makeURL();
		$app->redirect($adminLink->url); 		
//		parent::display ();
	}
		
}
?>
