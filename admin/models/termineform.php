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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

class CLMModelTermineForm extends JModelLegacy {

	// benötigt für Pagination
	function __construct()
	{
		parent::__construct();

		// user
		$this->user =Factory::getUser();
		
		$this->_getData();

		$this->_getForms();
		
	}


	// alle vorhandenen Filter
	function _getForms() {
	
		if (!isset($this->form) OR is_null($this->form)) $this->form = array();	// seit J 4.2 nötig um notice zu vermeiden
		// published
		$this->form['published']	= CLMForm::radioPublished('published', $this->termine->published);
		
		// vereinZPS
		if (strlen($this->termine->host) < 2) $this->termine->host = null;
		$this->form['vereinZPS'] 	= CLMForm::selectVereinZPSuVerband('host', $this->termine->host);
		
	}


	function _getData() {
		
		// Instanz der Tabelle
		$this->termine = Table::getInstance( 'termine', 'TableCLM');
		if ($id = clm_core::$load->request_int('id', 0)) {
			$this->termine->load($id);
		}
		
	}

}

?>
