<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerPGNdata extends JControllerLegacy
{
	function __construct() {
		parent::__construct();
	}
	
	function display($cachable = false, $urlparams = array()) {
		JRequest::setVar('view','pgndata');
		parent::display();
	} 
	
	function next() {
	
		$model = $this->getModel('pgndata');
		if ($model->store ()) {
			JRequest::setVar('view', 'swt');
			JFactory::getApplication()->enqueueMessage( JText::_( 'PGN_STORE_SUCCESS' ),'message' );
			parent::display ();
		}
		else
		{
			JRequest::setVar('view', 'swt');
			JFactory::getApplication()->enqueueMessage( JText::_( 'PGN_STORE_ERROR' ),'message' );
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
