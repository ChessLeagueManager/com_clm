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

class CLMControllerSWTLigasave extends JControllerLegacy
{
	function __construct() {
				
		parent::__construct();
		
	}
	
	function display($cachable = false, $urlparams = array()) {
	
		JRequest::setVar('view','swtligasave');
		parent::display();
		
	}
	
	function save () {
		
		//echo "in save ()<br/>";

		$model = $this->getModel('swtligasave');
		if ($model->finalCopy () && $model->rundenTermine () && $model->userAnlegen ()) {
			JRequest::setVar('view', 'swt');
			//$this->_message = JText::_( 'SWT_STORE_SUCCESS' );
			JFactory::getApplication()->enqueueMessage( JText::_( 'SWT_STORE_SUCCESS' ),'message' );
			parent::display ();
		}
		else {
			JRequest::setVar('view', 'swtligaerg');
			//$this->_message = JText::_( 'SWT_STORE_ERROR' );
			JFactory::getApplication()->enqueueMessage( JText::_( 'SWT_STORE_ERROR' ),'message' );
			parent::display ();
		}
		
	}
		
}
?>
