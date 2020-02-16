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

class CLMControllerSWTTurnier extends JControllerLegacy
{
	function __construct() {		
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		$_REQUEST['view'] = 'swtturnier';
		parent::display(); 
	} 
	
	function update() {		
		$swt = clm_core::$load->request_string('swt', '');
		
		$_REQUEST['view'] = 'swtturnierinfo';
		$_REQUEST['swt'] = $swt;
		$_REQUEST['update'] = 1;
		
		parent::display(); 		
	
	}
	
	function add() {		
		$swt = clm_core::$load->request_string('swt', '');
		
		$_REQUEST['view'] = 'swtturnierinfo';
		$_REQUEST['swt'] = $swt;
		
		parent::display(); 		
	
	}
	
}
?>