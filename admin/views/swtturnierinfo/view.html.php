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

class CLMViewSWTTurnierInfo extends JViewLegacy {
	function display($tpl = null) { 
		$update = clm_core::$load->request_int('update', 0);
		$filter_saison = clm_core::$load->request_string('filter_saison', '');
		
		
		//Daten vom Model
		$turnier = $this->get('turnier');	

		//Toolbar
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( Text::_('TITLE_SWT_TOURNAMENT_INFO') ,'clm_headmenu_manager.png' );
		
		//ToolBarHelper::custom('next','next.png','next_f2.png', Text::_('SWT_TOURNAMENT_NEXT'), false);
		ToolBarHelper::custom('next','forward.png','forward_f2.png', Text::_('SWT_TOURNAMENT_NEXT'), false);
		ToolBarHelper::custom('cancel','cancel.png','cancel_f2.png', Text::_('SWT_TOURNAMENT_CANCEL'), false);		
		
		// CLM Parameter
		$config = clm_core::$db->config();
		$params['tourn_showtlok'] = $config->tourn_showtlok;

		//Kategorie
		list($this->parentArray, $this->parentKeys) = CLMCategoryTree::getTree();
		$parentlist[]	= HTMLHelper::_('select.option',  '0', CLMText::selectOpener(Text::_( 'NO_PARENT' )), 'id', 'name' );
		foreach ($this->parentArray as $key => $value) {
			$parentlist[]	= HTMLHelper::_('select.option',  $key, $value, 'id', 'name' );
		}
		$lists['catidAlltime'] 	= HTMLHelper::_('select.genericlist', $parentlist, 'catidAlltime', 'class="inputbox" size="1" style="max-width: 250px;"', 'id', 'name', $turnier->catidAlltime);
		$lists['catidEdition'] 	= HTMLHelper::_('select.genericlist', $parentlist, 'catidEdition', 'class="inputbox" size="1" style="max-width: 250px;"', 'id', 'name', $turnier->catidEdition);
		
		//Saison
		if ($update == 1)
			$lists['sid'] 			= CLMForm::selectSeason('sid', $turnier->sid, FALSE, $filter_saison);
		else
			$lists['sid'] 			= CLMForm::selectSeason('sid', $turnier->sid, FALSE, FALSE);
		// Modus
		$lists['modus']			= CLMForm::selectModus('typ', $turnier->modus, FALSE, ' onChange="showFormRoundscount()";');
		
		// Tiebreakers
		$lists['tiebr1']		= CLMForm::selectTiebreakers('tiebr1', $turnier->tiebr1);
		$lists['tiebr2']		= CLMForm::selectTiebreakers('tiebr2', $turnier->tiebr2);
		$lists['tiebr3']		= CLMForm::selectTiebreakers('tiebr3', $turnier->tiebr3);
		
		// stages/dg
		$lists['dg']			= CLMForm::selectStages('dg', $turnier->dg);
		
		// director/tl
		$lists['tl']			= CLMForm::selectDirector('tl', $turnier->tl);
		
		// bezirksveranstaltung?
		$lists['bezirkTur']		= HTMLHelper::_('select.booleanlist', 'bezirkTur', 'class="inputbox"', $turnier->bezirkTur);
		
		// vereinZPS
		$lists['vereinZPS']		= CLMForm::selectVereinZPSuVerband('vereinZPS', $turnier->vereinZPS);
		
		// published
		$lists['published']		= CLMForm::radioPublished('published', $turnier->published);
		
		
		//Daten an Template
		$this->lists = $lists ;
		$this->turnier = $turnier ;
		$this->params = $params;
		
		parent::display($tpl);
	}
	
}

?>
