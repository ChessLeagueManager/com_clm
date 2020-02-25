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

class CLMControllerSWTLigainfo extends JControllerLegacy
{
	function __construct() {
		parent::__construct();
	}
	
	function display($cachable = false, $urlparams = array()) {
		$_REQUEST['view'] = 'swtliga';
		parent::display();
	} 
	
	function next() {
	
		$app = JFactory::getApplication();
		$model = $this->getModel('swtligainfo');
		if ($model->store ()) {
			$_REQUEST['view'] = 'swtligaman';
			parent::display ();
		}
		else
		{
			$_REQUEST['view'] = 'swtligainfo';
			$app->enqueueMessage( JText::_('SWT_STORE_SUCCESS') );
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
