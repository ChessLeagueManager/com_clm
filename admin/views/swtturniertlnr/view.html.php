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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewSWTTurnierTlnr extends JViewLegacy {
	function display($tpl = null) { 
		
		//Daten vom Model
		
		$teilnehmer = $this->get('teilnehmer');
		
		//Toolbar
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( Text::_('TITLE_SWT_TOURNAMENT_TLNR') ,'clm_headmenu_manager.png' );
		
		//ToolBarHelper::custom('next','next.png','next_f2.png', Text::_('SWT_TOURNAMENT_NEXT'), false);
		ToolBarHelper::custom('next','forward.png','forward_f2.png', Text::_('SWT_TOURNAMENT_NEXT'), false);
		ToolBarHelper::custom('cancel','cancel.png','cancel_f2.png', Text::_('SWT_TOURNAMENT_CANCEL'), false);	
		
		//Listen
		$geschlechter[] = HTMLHelper::_('select.option','',Text::_(''));
		$geschlechter[] = HTMLHelper::_('select.option','M',Text::_('OPTION_SEX_M'));
		$geschlechter[] = HTMLHelper::_('select.option','W',Text::_('OPTION_SEX_W'));
		
		//Daten ans Template
		$this->teilnehmer = $teilnehmer;
		$this->geschlechter = $geschlechter;
		
		parent::display($tpl);
	}
	
}

?>
