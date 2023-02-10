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

class CLMControllerSWTLigainfo extends JControllerLegacy
{
	function __construct() {
		$this->app =JFactory::getApplication();
		parent::__construct();
	}
	
	function display($cachable = false, $urlparams = array()) {
		$_REQUEST['view'] = 'swtliga';
		parent::display();
	} 
	
	function next() {
	
//		$app = JFactory::getApplication();
		$sid = clm_core::$load->request_int('sid', 0);
		$swt_file = clm_core::$load->request_string('swt_file', '');
		$lid = clm_core::$load->request_int('lid', 0);
		$name_land = clm_core::$load->request_string('name_land', '0');
		$model = $this->getModel('swtligainfo');
		if ($model->store ()) {
//			$_REQUEST['view'] = 'swtligaman';
			$update = clm_core::$load->request_int('update', 0);
			$swt_id = clm_core::$load->request_int('swt_id', 0);
			$mturnier = clm_core::$load->request_string('mturnier', '');
			$noOrgReference = clm_core::$load->request_string('noOrgReference', '0');		
			$noBoardResults = clm_core::$load->request_string('noBoardResults', '0');		
			$dwz_handling   = clm_core::$load->request_string( 'dwz_handling', '0');
			$params = clm_core::$load->request_string('strparams', '');
			$adminLink = new AdminLink();
			$adminLink->more = array('swt_file' => $swt_file, 'update' => $update, 'sid' => $sid, 'swt_id' => $swt_id, 'lid' => $lid,'mturnier' => $mturnier,
				'noOrgReference' => $noOrgReference, 'noBoardResults' => $noBoardResults, 'dwz_handling' => $dwz_handling, 'name_land' => $name_land, 'strparams' => $params);
			$adminLink->view = "swtligaman";
			$adminLink->makeURL();
			$this->app->redirect($adminLink->url); 		
//			parent::display ();
		} else {
			$_REQUEST['view'] = 'swtligainfo';
			$this->app->enqueueMessage( JText::_('SWT_STORE_ERROR') );
			parent::display ();
		}
	
	}
	
	function cancel() {		
	
		$adminLink = new AdminLink ();
		$adminLink->view = 'swt';
		$adminLink->makeURL ();
		
		$msg = JText::_( 'SWT_CANCEL_MSG' );
//		$this->setRedirect($adminLink->url, $msg);
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	
	}
	
}
?>
