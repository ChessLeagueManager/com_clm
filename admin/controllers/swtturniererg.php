<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerSWTTurnierErg extends JControllerLegacy
{
	function __construct() {		
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		JRequest::setVar('view','swtturniererg');
		parent::display(); 
	} 
	
	function next() {
		$model = $this->getModel('swtturniererg');
		if ($model->store ()) {
			$rfirst = JRequest::getVar('rfirst');
			$rlast  = JRequest::getVar('rlast');
			$rrange = JRequest::getVar('rrange');
			$rcount = JRequest::getVar('rcount');
			$this->_message = JText::_( 'SWT_STORE_SUCCESS' );
			if ($rlast == $rcount) {
				JRequest::setVar('view', 'swt');
				JFactory::getApplication()->enqueueMessage( JText::_( 'SWT_STORE_SUCCESS' ),'message' );
				parent::display ();
			} else {
				JRequest::setVar('rfirst', ($rlast + 1));
				JRequest::setVar('view', 'swtturniererg');
				parent::display ();
			}

/*		JRequest::setVar('view', 'swt');
			JFactory::getApplication()->enqueueMessage( JText::_( 'SWT_STORE_SUCCESS' ),'message' );
			parent::display (); */
		}
		else {
			JRequest::setVar('view', 'swtturniererg');
			JFactory::getApplication()->enqueueMessage( JFactory::getDBO()->getErrorMsg() /*JText::_('SWT_STORE_ERROR_COPY_TOURNAMENT')*/,'error' );
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