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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

class TableCLMAccessgroupsForm extends Table
{
	var $id			= null;
	var $name		= '';
	var $usertype	= '';
	var $kind		= 'USER';
	var $published	= 0;
	var $ordering	= 0;
	var $params	= '';

	function __construct( &$_db ) {
		parent::__construct( '#__clm_usertype', 'id', $_db );
	}

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see Table::check
	 * @since 1.5
	 */
	function checkData() {

		// check for valid name
		if(trim($this->name) == '') {
			$this->setError(Text::_( 'Name angeben !' ));
			return false;
		}

		if(empty($this->usertype)) {
			$this->setError(Text::_( 'Type angeben' ));
			return false;
		}
		return true;
	}

	function reorderAll() {
		$query = 'SELECT DISTINCT name FROM '.$this->_db->nameQuote($this->_tbl);
		$this->_db->setQuery($query);
		$names = $this->_db->loadResultArray();
		

		foreach($names as $name) {
			$this->reorder('name = '.$name);
		}
		return true;
	}

}
