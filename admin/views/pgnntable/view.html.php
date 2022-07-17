<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewPGNntable extends JViewLegacy {

	function display ($tpl = null) {

		$task = clm_core::$load->request_string('task', '');
		$stask = clm_core::$load->request_string('stask', '');

		// Turnierdaten
		$turnier		= $this->get( 'Turnier' );
		$aentries		= $this->get( 'TEntries' );

		$pgn_arr = array();
		$pgn_error = 0;
		$pgn_del = 0;
		$pgn_arr		= $this->get( 'MainPGN' );
		if (!is_null($pgn_arr)) {
			for ($p = 0; $p < count($pgn_arr); $p++) { 
				if ($pgn_arr[$p]['error'] != '') $pgn_error++;
			}
		}
		
		//Toolbar
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( JText::_('TITLE_PGN_MAINTAIN_NTABLE') ,'clm_headmenu_manager.png' );
		
		if ($pgn_error == 0)
			JToolBarHelper::custom('next','forward.png','forward_f2.png', JText::_('SWT_LEAGUE_END'), false);
		else
			JToolBarHelper::custom('next','forward.png','forward_f2.png', JText::_('SWT_LEAGUE_KORR'), false);
		JToolBarHelper::custom('cancel','cancel.png','cancel_f2.png', JText::_('SWT_LEAGUE_CANCEL'), false);
		
	
		// PGN-Daten an Template
		$this->pgn_del = $pgn_del;
		$this->pgn_arr = $pgn_arr;
		$this->turnier = $turnier;
		$this->aentries = $aentries;

		parent::display($tpl);
		
	}

}

?>
