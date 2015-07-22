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

class CLMViewSWTLigasave extends JViewLegacy {

	function display ($tpl = null) {
	
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( JText::_('TITLE_SWT_LEAGUE') ,'clm_headmenu_manager.png' );
		
		JToolBarHelper::custom('save','save.png','save_f2.png', JText::_('SWT_LEAGUE_SAVE'), false);
		
		parent::display ($tpl);
		
	}

}
?>
