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

class CLMViewSonderranglistenMain extends JViewLegacy {
	function display($tpl = null) { 
		
		$mainframe	= JFactory::getApplication();
		$option 	= JRequest::getCmd( 'option' );
		
		//Daten vom Model
		$state = $this->get( 'State' );
		$sonderranglisten =  $this->get( 'Sonderranglisten' );
		$turniere =  $this->get( 'Turniere' );
		$pagination =  $this->get( 'Pagination' );
		$user 	= $this->get( 'User' );
		
		$filter_saisons	 =  $this->get( 'FilterSaisons' );
		$filter_turniere =  $this->get( 'FilterTurniere' );
		
		//Turnier vorhanden
		if(count($turniere) == 0){
			$turnierExists = false;
		} else {
			$turnierExists = true;
		}
		
		//Toolbar
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( JText::_('TITLE_SPECIALRANKINGS') ,'clm_headmenu_sonderranglisten.png' );
		
		if($turnierExists) {
			JToolBarHelper::publishList('publish');
			JToolBarHelper::unpublishList();
			JToolBarHelper::deleteList();
			JToolBarHelper::editList(); 
			JToolBarHelper::addNew(); 
		}
		
		JHtml::_('behavior.tooltip');
		
		//Suche und Filter
		$filter_saison		= $state->get( 'filter_saison' );
		$filter_turnier		= $state->get( 'filter_turnier' );
		$search 			= $state->get( 'search' );
		
		//Suche
		$lists['search'] = $search;
		
		//Sortierung
		$lists['order_Dir'] = $state->get( 'filter_order_Dir' );
		$lists['order']     = $state->get( 'filter_order' );
		
		
		//Filter 
		$options_filter_tur[]		= JHtml::_('select.option', '', JText::_( 'SPECIALRANKINGS_TOURNEMENTS' ));
		foreach($filter_turniere as $tur)	{
			$options_filter_tur[]		= JHtml::_('select.option', $tur->id, $tur->name);
		}
		$lists['filter_turnier']	= JHtml::_('select.genericlist', $options_filter_tur, 'filter_turnier', 'class="inputbox" onchange="this.form.submit();"', 'value', 'text', $filter_turnier );
		
		$options_filter_sai[]		= JHtml::_('select.option', '', JText::_( 'SPECIALRANKINGS_SEASONS' ));
		foreach($filter_saisons as $sai)	{
			$options_filter_sai[]		= JHtml::_('select.option', $sai->id, $sai->name);
		}
		$lists['filter_saison']	= JHtml::_('select.genericlist', $options_filter_sai, 'filter_saison', 'class="inputbox" onchange="this.form.submit();"', 'value', 'text', $filter_saison );
		
		
		//Reihenfolge
		if($lists['search'] != '' or ($lists['order'] != 'ordering' and $lists['order'] != 'turnier')) {
			$ordering = false;
		}
		else {
			$ordering = true;
		}
		
		
		//Daten an Template
		$this->assignRef( 'sonderranglisten', $sonderranglisten );
		$this->assignRef( 'lists', $lists );
		$this->assignRef( 'user', $user );
		$this->assignRef( 'pagination', $pagination );
		$this->assignRef( 'ordering', $ordering );
		$this->assignRef( 'turnierExists', $turnierExists );
		
		parent::display($tpl); 
	} 
} 
?>
