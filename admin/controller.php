<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Language\Text;

//JLoader::registerAlias('JControllerLegacy', '\\Joomla\\CMS\\MVC\\Controller\\BaseController', '6.0');
//use Joomla\CMS\MVC\Controller\BaseController;
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
		$this->setRedirect( 'index.php?option=com_clm', Text::_('IRREGULAR_ABORT') );
		// wird später in allen anderen Controllern (so vorhanden) überschrieben!!!
	}


}
?>
