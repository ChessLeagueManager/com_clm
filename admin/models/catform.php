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
defined('_JEXEC') or die('Restricted access');

class CLMModelCatForm extends JModelLegacy {

	// benötigt für Pagination
	function __construct()
	{
		parent::__construct();

		// user
		$this->user =JFactory::getUser();
		
		$this->_getData();

		$this->_getForms();

	}


	function _getData() {
		
		// Instanz der Tabelle
		$this->category = JTable::getInstance( 'categories', 'TableCLM');
		if ($id = JRequest::getInt('id')) {
			$this->category->load($id);
		}
		
	}


	// alle vorhandenen Filter
	function _getForms() {
	
		// get Tree
		list($this->parentArray, $this->parentKeys) = CLMCategoryTree::getTree();
		
		// parent
		$parentlist[]	= JHTML::_('select.option',  '0', CLMText::selectOpener(JText::_( 'NO_PARENT' )), 'id', 'name' );
		foreach ($this->parentArray as $key => $value) {
			// Einträge ausscheiden, die die Kategorie selbst sind, ODER der Kategorie untergeordent sind!
			if ( $key != $this->category->id) { 
				if ( ( !isset($this->parentKeys[$key]) OR (isset($this->parentKeys[$key]) AND !in_array($this->category->id, $this->parentKeys[$key])) ) ) {
					$parentlist[]	= JHTML::_('select.option',  $key, $value, 'id', 'name' );
				}
			}
		}
		$this->form['parent'] = JHTML::_('select.genericlist', $parentlist, 'parentid', 'class="inputbox" size="1"', 'id', 'name', intval($this->category->parentid));
		
	
	
		// director/tl
		// $this->form['tl']	= CLMForm::selectDirector('tl', $this->category->tl);
		
		// published
		$this->form['published']	= CLMForm::radioPublished('published', $this->category->published);
		
	}

}

?>