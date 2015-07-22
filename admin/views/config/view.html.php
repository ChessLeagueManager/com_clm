<?php
/**
 * @copyright	Copyright (C) 2009-2011 ACYBA SARL - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class CLMViewConfig extends JView {
	
	function display($tpl = null) {
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');
		JToolBarHelper::title( JText::_('CONFIG_TITLE'), 'clm_headmenu_einstellungen.png' );
		
/*		JToolBarHelper::divider();
		JToolBarHelper::save( 'save' );
		JToolBarHelper::apply( 'apply' );
		JToolBarHelper::divider();
		JToolBarHelper::cancel( 'cancel' );
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_clm');
		
		// Tabs
		jimport('joomla.html.pane');
		
		
		// aktuelle Parameter-Einstellungen
		$paramsdata = &JComponentHelper::getParams( 'com_clm' );
		// XML-Vorgaben zu den Parametern
		$paramsdefs = JPATH_COMPONENT.DS.'config.xml';
		// beides zusammen übergeben
		$params = new JParameter( $paramsdata->_raw, $paramsdefs );
		$this->assignRef('params', $params);
		
		JHtml::_('behavior.tooltip');
		
		return parent::display($tpl);
*/
		parent::display($tpl);		
	}
}