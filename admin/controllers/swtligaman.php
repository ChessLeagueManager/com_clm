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
		$model = $this->getModel('swtligaman');
		if ($model->store ()) {
			if ($model->fixSpielerID ()) {
				$_REQUEST['view'] = 'swtligaerg';
				//$app->enqueueMessage( JText::_('SWT_STORE_SUCCESS') );
				parent::display ();
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
