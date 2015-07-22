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

class CLMViewSWTTurnierInfo extends JView {
	function display($tpl = null) { 
		
		
		//Daten vom Model
		$turnier =& $this->get('turnier');	

		//Toolbar
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'admin_menue_images.php');
		JToolBarHelper::title( JText::_('TITLE_SWT_TOURNAMENT_INFO') ,'clm_headmenu_manager.png' );
		
		//JToolBarHelper::custom('next','next.png','next_f2.png', JText::_('SWT_TOURNAMENT_NEXT'), false);
		JToolBarHelper::custom('next','forward.png','forward_f2.png', JText::_('SWT_TOURNAMENT_NEXT'), false);
		JToolBarHelper::custom('cancel','cancel.png','cancel_f2.png', JText::_('SWT_TOURNAMENT_CANCEL'), false);		
		
		// CLM Parameter
		$config	= &JComponentHelper::getParams( 'com_clm' );
		$params['tourn_showtlok'] = $config->get('tourn_showtlok',0);

		//Kategorie
		list($this->parentArray, $this->parentKeys) = CLMCategoryTree::getTree();
		$parentlist[]	= JHtml::_('select.option',  '0', CLMText::selectOpener(JText::_( 'NO_PARENT' )), 'id', 'name' );
		foreach ($this->parentArray as $key => $value) {
			$parentlist[]	= JHtml::_('select.option',  $key, $value, 'id', 'name' );
		}
		$lists['catidAlltime'] 	= JHtml::_('select.genericlist', $parentlist, 'catidAlltime', 'class="inputbox" size="1" style="max-width: 250px;"', 'id', 'name', $turnier->catidAlltime);
		$lists['catidEdition'] 	= JHtml::_('select.genericlist', $parentlist, 'catidEdition', 'class="inputbox" size="1" style="max-width: 250px;"', 'id', 'name', $turnier->catidEdition);
		
		//Saison
		$lists['sid'] 			= CLMForm::selectSeason('sid', $turnier->sid);
		
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
		$lists['bezirkTur']		= JHtml::_('select.booleanlist', 'bezirkTur', 'class="inputbox"', $turnier->bezirkTur);
		
		// vereinZPS
		$lists['vereinZPS']		= CLMForm::selectVereinZPS('vereinZPS', $turnier->vereinZPS);
		
		// published
		$lists['published']		= CLMForm::radioPublished('published', $turnier->published);
		
		
		//Daten an Template
		$this->assignRef( 'lists', $lists );
		$this->assignRef( 'turnier', $turnier );
		$this->assignRef( 'params', $params);
		
		parent::display($tpl);
	}
	
}

?>