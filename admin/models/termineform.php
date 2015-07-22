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

class CLMModelTermineForm extends JModelLegacy {

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
	
		// published
		$this->form['published']	= CLMForm::radioPublished('published', $this->termine->published);
		
		// vereinZPS
		if (strlen($this->termine->host) < 2) $this->termine->host = null;
		$this->form['vereinZPS'] 	= CLMForm::selectVereinZPSuVerband('host', $this->termine->host);
		
	}


	function _getData() {
		
		// Instanz der Tabelle
		$this->termine = JTable::getInstance( 'termine', 'TableCLM');
		if ($id = JRequest::getInt('id')) {
			$this->termine->load($id);
		}
		
	}

}

?>
