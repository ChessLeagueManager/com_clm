<?php
/**
 * @copyright	Copyright (C) 2009-2011 ACYBA SARL - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

class CLMViewConfig extends JViewLegacy {
	
	function display($tpl = null) {
		
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( JText::_('CONFIG_TITLE'), 'clm_headmenu_einstellungen.png' );
		

		parent::display($tpl);		
	}
}
