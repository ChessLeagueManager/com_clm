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

/**
 * CLM Component Controller
 * weitere extends in controllers/*
 */
class CLMController extends JControllerLegacy {
	
	function __construct() {
		
		parent::__construct();

	}


	/**
	 * Display the view
	 */
	function display($cachable = false, $urlparams = array()) {

		parent::display();

	}


	// Default-Methode 
	function cancel() {
		$this->setRedirect( 'index.php?option=com_clm', JText::_('IRREGULAR_ABORT') );
		// wird später in allen anderen Controllern (so vorhanden) überschrieben!!!
	}


}
?>
