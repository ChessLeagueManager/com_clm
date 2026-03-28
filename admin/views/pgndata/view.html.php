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
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewPGNdata extends JViewLegacy {

	function display ($tpl = null) {

		$task = clm_core::$load->request_string('task', '');
		$stask = clm_core::$load->request_string('stask', '');
//echo "<br>html-pgndata: task $task  stask $stask "; //die();

		// Turnierdaten
		$turnier		= $this->get( 'Turnier' );
//echo "<br>vhd-turnier: "; var_dump($turnier); //die(); 


		$pgn_arr = array();
		$pgn_error = 0;
		$pgn_del = 0;
		$pgn_arr		= $this->get( 'MainPGN' );
		for ($p = 0; $p < count($pgn_arr); $p++) { 
			if ($pgn_arr[$p]['error'] != '') $pgn_error++;
		}
//echo "<br>html-pgn_arr:".count($pgn_arr).' '.$pgn_error.'end '; var_dump($pgn_arr); //die();
		
		//Toolbar
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( Text::_('TITLE_PGN_MAINTAIN') ,'clm_headmenu_manager.png' );
		
		if ($pgn_error == 0)
			ToolBarHelper::custom('next','forward.png','forward_f2.png', Text::_('SWT_LEAGUE_END'), false);
		else
			ToolBarHelper::custom('next','forward.png','forward_f2.png', Text::_('SWT_LEAGUE_KORR'), false);
		ToolBarHelper::custom('cancel','cancel.png','cancel_f2.png', Text::_('SWT_LEAGUE_CANCEL'), false);
		
	
		// PGN-Daten an Template
		$this->pgn_del = $pgn_del;
		$this->pgn_arr = $pgn_arr;
		$this->turnier = $turnier;

		parent::display($tpl);
		
	}

}

?>
