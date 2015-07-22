<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
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
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		JRequest::setVar('view','swtturniertlnr');
		parent::display(); 
	} 
	
	function next() {
		$model = $this->getModel('swtturniertlnr');
		if ($model->store ()) {
			$pfirst = JRequest::getVar('pfirst');
			$plast  = JRequest::getVar('plast');
			$prange = JRequest::getVar('prange');
			$pcount = JRequest::getVar('pcount');
			$this->_message = JText::_( 'SWT_STORE_SUCCESS' );
			if ($plast == $pcount) {
				JRequest::setVar('view', 'swtturniererg');
				parent::display ();
			} else {
				JRequest::setVar('pfirst', ($plast + 1));
				JRequest::setVar('view', 'swtturniertlnr');
				parent::display ();
			}
		}
		else {
			JRequest::setVar('view', 'swtturniertlnr');
			$this->_message = JText::_( 'SWT_STORE_ERROR' );
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