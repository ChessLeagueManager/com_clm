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

jimport( 'joomla.application.component.view');

class CLMViewAccessgroupsMain extends JView {
	function display($tpl = null) { 
		
		$mainframe	= JFactory::getApplication();
		$option 	= JRequest::getCmd( 'option' );
		
		//Daten vom Model
		$state =& $this->get( 'State' );
		$accessgroups = & $this->get( 'Accessgroups' );

		$pagination = & $this->get( 'Pagination' );
		$user 	=& $this->get( 'User' );
		
		//Benutzergruppen vorhanden
		if(count($accessgroups) == 0){
			$accessgroupExists = false;
		} else {
			$accessgroupExists = true;
		}
		
		//Toolbar
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');
		JToolBarHelper::title( JText::_('TITLE_ACCESSGROUPS') ,'clm_headmenu_sonderranglisten.png' );
		
		if($accessgroupExists) {
			JToolBarHelper::publishList('publish');
			JToolBarHelper::unpublishList();
			JToolBarHelper::deleteList();
			JToolBarHelper::editListX(); 
			JToolBarHelper::customX( 'copy', 'copy.png', 'copy_f2.png', JText::_( 'LEAGUE_BUTTON_4' ) );
			JToolBarHelper::addNewX(); 
		}
		
		JHtml::_('behavior.tooltip');
		
		//Suche 
		$search 			= $state->get( 'search' );
		$lists['search'] = $search;
		
		//Sortierung
		$lists['order_Dir'] = $state->get( 'filter_order_Dir' );
		$lists['order']     = $state->get( 'filter_order' );
		
		//Reihenfolge
		if($lists['search'] != '' or ($lists['order'] != 'ordering' and $lists['order'] != 'accessgroup')) {
			$ordering = false;
		}
		else {
			$ordering = true;
		}
		
		
		//Daten an Template
		$this->assignRef( 'accessgroups', $accessgroups );
		$this->assignRef( 'lists', $lists );
		$this->assignRef( 'user', $user );
		$this->assignRef( 'pagination', $pagination );
		$this->assignRef( 'ordering', $ordering );
		$this->assignRef( 'accessgroupExists', $accessgroupExists );
		
		parent::display($tpl); 
	} 
} 
?>