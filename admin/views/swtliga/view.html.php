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

class CLMViewSWTliga extends JViewLegacy {

	function display($tpl = null) { 

		//Daten vom Model
		$state		= $this->get( 'state' );
		$saisons	= $this->get( 'saisons' );
		$ligen		= $this->get( 'ligen' );
		
			
		//Toolbar
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( JText::_('TITLE_SWT_LEAGUE') ,'clm_headmenu_manager.png' );
		
		JToolBarHelper::custom('update','refresh.png','refresh_f2.png', JText::_('SWT_LEAGUE_UPDATE'), false);
		JToolBarHelper::custom('add','new.png','new_f2.png', JText::_('SWT_TOURNAMENT_NEW'), false);
		
		//Saison- und Ligen-Auswahl erstellen
		$options_saisons[] = JHtml::_('select.option', '', JText::_( 'SWT_SAISONS' ));
		foreach($saisons as $saison) {
			$options_saisons[] = JHtml::_('select.option', $saison->id, $saison->name);
		}
		
		$options_ligen[] = JHtml::_('select.option', '', JText::_( 'SWT_LEAGUES' ));
		foreach($ligen as $liga) {
			$options_ligen[] = JHtml::_('select.option', $liga->id, $liga->name);
		}
		
		$lists['saisons']	= JHtml::_('select.genericlist', $options_saisons, 'filter_saison', 'class="inputbox" onchange="this.form.submit();"', 'value', 'text', $state->get('filter_saison') );
		$lists['ligen']	= JHtml::_('select.genericlist', $options_ligen, 'liga', 'class="inputbox"', 'value', 'text', 0 );
		
		//Daten an Template
		$this->assignRef( 'lists', $lists );
		
		parent::display($tpl);
		
	}
	
}

?>
