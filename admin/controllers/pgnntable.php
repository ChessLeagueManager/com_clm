<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerPGNntable extends JControllerLegacy
{
	function __construct() {
		parent::__construct();
	}
	
	function display($cachable = false, $urlparams = array()) {
		$_REQUEST['view'] = 'pgnntable';
		parent::display();
	} 
	
	function next() {
		$app =JFactory::getApplication();	
		$model = $this->getModel('pgnntable');
		$liga = clm_core::$load->request_string('liga', '');
		$pgn_file = clm_core::$load->request_string('pgn_file', '');
		if ($model->store ()) {
			$adminLink = new AdminLink ();
			$adminLink->view = 'pgnimport';
			$adminLink->more = array('liga' => $liga, 'pgn_file' => $pgn_file);
			$adminLink->makeURL ();
			$app->enqueueMessage( JText::_( 'PGN_STORE_SUCCESS' ),'message' );
			$app->redirect($adminLink->url);
		}
		else
		{
			$adminLink = new AdminLink ();
			$adminLink->view = 'swt';
			$adminLink->more = array('liga' => $liga, 'pgn_file' => $pgn_file);
			$adminLink->makeURL ();
			$app->enqueueMessage( JText::_( 'PGN_STORE_ERROR' ),'message' );
			$app->redirect($adminLink->url);
		}
	
	}
	
	function cancel() {		
		$app =JFactory::getApplication();	
		$liga = clm_core::$load->request_string('liga', '');
		$pgn_file = clm_core::$load->request_string('pgn_file', '');
		$adminLink = new AdminLink ();
		$adminLink->view = 'pgnimport';
		$adminLink->more = array('liga' => $liga, 'pgn_file' => $pgn_file);
		$adminLink->makeURL ();
		
		$msg = JText::_( 'SWT_CANCEL_MSG' );
		$app->enqueueMessage( $msg );
		$app->redirect($adminLink->url);
	
	}
	
}
?>
