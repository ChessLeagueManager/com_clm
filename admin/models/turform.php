<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelTurForm extends JModelLegacy {

	// benötigt für Pagination
	function __construct()
	{
		parent::__construct();

		// user
		$this->user =JFactory::getUser();
		
		$this->_getData();

		$this->_getForms();

	}


	// alle vorhandenen Filter
	function _getForms() {
	
		
		// category
		list($this->parentArray, $this->parentKeys) = CLMCategoryTree::getTree();
		if (count($this->parentArray) > 0)  { // nur, wenn Kategorien existieren
			$parentlist[]	= JHtml::_('select.option',  '0', CLMText::selectOpener(JText::_( 'NO_PARENT' )), 'id', 'name' );
			foreach ($this->parentArray as $key => $value) {
				$parentlist[]	= JHtml::_('select.option',  $key, $value, 'id', 'name' );
			}
			$this->form['catidAlltime'] = JHtml::_('select.genericlist', $parentlist, 'catidAlltime', 'class="inputbox" size="1" style="max-width: 250px;"', 'id', 'name', intval($this->turnier->catidAlltime));
			$this->form['catidEdition'] = JHtml::_('select.genericlist', $parentlist, 'catidEdition', 'class="inputbox" size="1" style="max-width: 250px;"', 'id', 'name', intval($this->turnier->catidEdition));
		}
		
		// Saison
		$this->form['sid']	= CLMForm::selectSeason('sid', $this->turnier->sid);
	
		// Modus
		$this->form['modus']	= CLMForm::selectModus('typ', $this->turnier->typ, false, ' onChange="showFormRoundscount()";');
		
		// Tiebreakers
		$this->form['tiebr1']	= CLMForm::selectTiebreakers('tiebr1', $this->turnier->tiebr1);
		$this->form['tiebr2']	= CLMForm::selectTiebreakers('tiebr2', $this->turnier->tiebr2);
		$this->form['tiebr3']	= CLMForm::selectTiebreakers('tiebr3', $this->turnier->tiebr3);
		
		
		// stages/dg
		$this->form['dg']	= CLMForm::selectStages('dg', $this->turnier->dg);
		
		// director/tl
		$this->form['tl']	= CLMForm::selectDirector('tl', $this->turnier->tl);
		
		// bezirksveranstaltung?
		$this->form['bezirkTur']= JHtml::_('select.booleanlist', 'bezirkTur', 'class="inputbox"', $this->turnier->bezirkTur);
		
		// vereinZPS
		if (strlen($this->turnier->vereinZPS) < 2) $this->turnier->vereinZPS = null;
		$this->form['vereinZPS']= CLMForm::selectVereinZPSuVerband('vereinZPS', $this->turnier->vereinZPS);
		
		
		// director/tl
		$this->form['tl']	= CLMForm::selectDirector('tl', $this->turnier->tl);
		
		
		// published
		$this->form['published']	= CLMForm::radioPublished('published', $this->turnier->published);
		
	}


	function _getData() {
		
		// Instanz der Tabelle
		$this->turnier = JTable::getInstance( 'turniere', 'TableCLM');
		if ($id = JRequest::getInt('id')) {
			$this->turnier->load($id);
		}
		
	}

}

?>