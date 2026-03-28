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

class CLMViewSWTLigasave extends JViewLegacy {

	function display ($tpl = null) {
	
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( Text::_('TITLE_SWT_LEAGUE') ,'clm_headmenu_manager.png' );
		
		ToolBarHelper::custom('save','save.png','save_f2.png', Text::_('SWT_LEAGUE_SAVE'), false);
		
		parent::display ($tpl);
		
	}

}
?>
