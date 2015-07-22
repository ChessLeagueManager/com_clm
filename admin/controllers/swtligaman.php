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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerSWTLigaman extends JControllerLegacy
{
	function __construct() {
		parent::__construct();
	}
	
	function display($cachable = false, $urlparams = array()) {
		JRequest::setVar('view','swtligaman');
		parent::display();
	} 
	
	function next () {
	
		$model = $this->getModel('swtligaman');
		if ($model->store ()) {
			if ($model->fixSpielerID ()) {
				JRequest::setVar('view', 'swtligaerg');
				$this->_message = JText::_( 'SWT_STORE_SUCCESS' );
				parent::display ();
			}
			else {
				JRequest::setVar ('view', 'swtligaman');
				$this->_message = JText::_( 'SWT_STORE_ERROR' );
				parent::display ();
			}
		}
		else
		{
			JRequest::setVar('view', 'swtligaman');
			$this->_message = JText::_( 'SWT_STORE_ERROR' );
			parent::display ();
		}
	}
	
	function nextTeam () {
			
		$model = $this->getModel('swtligaman');
		if ($model->store ()) {
			JRequest::setVar('view', 'swtligaman');
			$man = JRequest::getVar('man', 0, 'default', 'int');
			JRequest::setVar('man', $man + 1);
			JRequest::setVar('filter_zps', '0');
			JRequest::setVar('filter_sg_zps', '0');
			
			$this->_message = JText::_( 'SWT_STORE_SUCCESS' );
			parent::display ();
		}
		else
		{
			JRequest::setVar('view', 'swtligaman');
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
