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

class CLMControllerSWTLigainfo extends JControllerLegacy
{
	function __construct() {
		parent::__construct();
	}
	
	function display($cachable = false, $urlparams = array()) {
		JRequest::setVar('view','swtliga');
		parent::display();
	} 
	
	function next() {
	
		$model = $this->getModel('swtligainfo');
		if ($model->store ()) {
			JRequest::setVar('view', 'swtligaman');
			$this->_message = JText::_( 'SWT_STORE_SUCCESS' );
			parent::display ();
		}
		else
		{
			JRequest::setVar('view', 'swtligainfo');
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
