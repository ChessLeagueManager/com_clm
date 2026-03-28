<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
/**
 * @copyright	Copyright (C) 2009-2011 ACYBA SARL - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewConfig extends JViewLegacy {
	
	function display($tpl = null) {
		
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( Text::_('CONFIG_TITLE'), 'clm_headmenu_einstellungen.png' );
		

		parent::display($tpl);		
	}
}
