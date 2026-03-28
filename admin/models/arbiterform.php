<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

class CLMModelArbiterForm extends JModelLegacy {

	// benötigt für Pagination
	function __construct()
	{
		parent::__construct();

		// user
		$this->user =Factory::getUser();
		
		$this->_getData();

		$this->_getForms();

	}


	function _getData() {
		
		// Instanz der Tabelle
		$this->arbiter = Table::getInstance( 'arbiters', 'TableCLM');
		if ($id = clm_core::$load->request_int('id')) {
			$this->arbiter->load($id);
		}
		
	}


	// alle vorhandenen Filter
	function _getForms() {
	
		//CLM parameter auslesen
		$clm_config = clm_core::$db->config();
		if ($clm_config->field_search == 1) $field_search = "js-example-basic-single";
		else $field_search = "inputbox";
	
		if (!isset($this->form) OR is_null($this->form)) $this->form = array();
		
		// published
		$this->form['published']	= CLMForm::radioPublished('published', $this->arbiter->published);
		
	}

}

?>
