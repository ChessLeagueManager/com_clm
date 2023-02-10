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

class CLMControllerSWTLigaman extends JControllerLegacy
{
	function __construct() {
		parent::__construct();
	}
	
	function display($cachable = false, $urlparams = array()) {
		$_REQUEST['view'] = 'swtligaman';
		parent::display();
	} 
	
	function next () {
	
		$app = JFactory::getApplication();
		$sid = clm_core::$load->request_int('sid', 0);
		$swt_file = clm_core::$load->request_string('swt_file', '');
		$lid = clm_core::$load->request_int('lid', 0);
		$name_land = clm_core::$load->request_string('name_land', '0');
		$model = $this->getModel('swtligaman');
		if ($model->store ()) {
			if ($model->fixSpielerID ()) {
//				$_REQUEST['view'] = 'swtligaerg';
				//$app->enqueueMessage( JText::_('SWT_STORE_SUCCESS') );
				$swt_id = clm_core::$load->request_int('swt_id', 0);
				$mturnier = clm_core::$load->request_string('mturnier', '0');
				$update = clm_core::$load->request_string('update', '0');
				$ungerade = clm_core::$load->request_string('ungerade', '0');
				$noOrgReference = clm_core::$load->request_string('noOrgReference', '0');		
				$noBoardResults = clm_core::$load->request_string('noBoardResults', '0');		
				$dwz_handling   = clm_core::$load->request_string( 'dwz_handling', '0');
				$params = clm_core::$load->request_string('strparams', '');
				$adminLink = new AdminLink();
				$adminLink->more = array('swt_file' => $swt_file, 'update' => $update, 'sid' => $sid, 'swt_id' => $swt_id, 'lid' => $lid, 'mturnier' => $mturnier, 'ungerade' => $ungerade,
					'noOrgReference' => $noOrgReference, 'noBoardResults' => $noBoardResults, 'dwz_handling' => $dwz_handling, 'name_land' => $name_land, 'strparams' => $params);
				$adminLink->view = "swtligaerg";
				$adminLink->makeURL();
				$app->redirect($adminLink->url); 		
//				parent::display ();
			}
			else {
				$_REQUEST['view'] = 'swtligaman';
				$app->enqueueMessage( JText::_('SWT_STORE_ERROR') );
				parent::display ();
			}
		}
		else
		{
			$_REQUEST['view'] = 'swtligaman';
			$app->enqueueMessage( JText::_('SWT_STORE_ERROR') );
			parent::display ();
		}
	}
	
	function nextTeam () {
			
		$app = JFactory::getApplication();
		$model = $this->getModel('swtligaman');
		if ($model->store ()) {
			$_REQUEST['view'] = 'swtligaman';
			$man = clm_core::$load->request_int('man', -1);
			$_GET['man'] = $man + 1;
			$_GET['filter_zps'] = '0';
			$_GET['filter_sg_zps'] = '0';
			
			//$app->enqueueMessage( JText::_('SWT_STORE_SUCCESS') );
			parent::display ();
		}
		else
		{
			$_REQUEST['view'] = 'swtligaman';
			$app->enqueueMessage( JText::_('SWT_STORE_ERROR') );
			parent::display ();
		}
	}
	
	function cancel() {		
	
		$adminLink = new AdminLink ();
		$adminLink->view = 'swt';
		$adminLink->makeURL ();
		
		$msg = JText::_( 'SWT_CANCEL_MSG' );
		$this->setRedirect($adminLink->url, $msg);
	
	}
	
}
?>
