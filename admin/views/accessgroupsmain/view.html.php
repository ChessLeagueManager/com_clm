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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewAccessgroupsMain extends JViewLegacy {
	function display($tpl = null) { 
		
		$mainframe	= Factory::getApplication();
		$option 	= clm_core::$load->request_string('option', '');
		
		//Daten vom Model
		$state = $this->get( 'State' );
		$accessgroups =  $this->get( 'Accessgroups' );

		$pagination =  $this->get( 'Pagination' );
		$user 	= $this->get( 'User' );
		
		//Benutzergruppen vorhanden
		if(count($accessgroups) == 0){
			$accessgroupExists = false;
		} else {
			$accessgroupExists = true;
		}
		
		//Toolbar
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( Text::_('TITLE_ACCESSGROUPS') ,'clm_headmenu_sonderranglisten.png' );
		
		if($accessgroupExists) {
			ToolBarHelper::publishList('publish');
			ToolBarHelper::unpublishList();
			ToolBarHelper::deleteList();
			ToolBarHelper::editList(); 
			ToolBarHelper::custom( 'copy', 'copy.png', 'copy_f2.png', Text::_( 'LEAGUE_BUTTON_4' ) );
			ToolBarHelper::addNew(); 
		}
		
//		HTMLHelper::_('behavior.tooltip');
		require_once (JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');
		
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
		$this->accessgroups = $accessgroups;
		$this->lists = $lists;
		$this->user = $user;
		$this->pagination = $pagination;
		$this->ordering = $ordering;
		$this->accessgroupExists = $accessgroupExists;
		
		parent::display($tpl); 
	} 
} 
?>
