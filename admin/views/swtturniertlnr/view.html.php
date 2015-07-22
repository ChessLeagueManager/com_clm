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

class CLMViewSWTTurnierTlnr extends JViewLegacy {
	function display($tpl = null) { 
		
		//Daten vom Model
		
		$teilnehmer = $this->get('teilnehmer');
		
		//Toolbar
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( JText::_('TITLE_SWT_TOURNAMENT_TLNR') ,'clm_headmenu_manager.png' );
		
		//JToolBarHelper::custom('next','next.png','next_f2.png', JText::_('SWT_TOURNAMENT_NEXT'), false);
		JToolBarHelper::custom('next','forward.png','forward_f2.png', JText::_('SWT_TOURNAMENT_NEXT'), false);
		JToolBarHelper::custom('cancel','cancel.png','cancel_f2.png', JText::_('SWT_TOURNAMENT_CANCEL'), false);	
		
		//Listen
		$geschlechter[] = JHtml::_('select.option','',JText::_(''));
		$geschlechter[] = JHtml::_('select.option','M',JText::_('OPTION_SEX_M'));
		$geschlechter[] = JHtml::_('select.option','W',JText::_('OPTION_SEX_W'));
		
		//Daten ans Template
		$this->assignRef('teilnehmer',$teilnehmer);
		$this->assignRef('geschlechter',$geschlechter);
		
		parent::display($tpl);
	}
	
}

?>
