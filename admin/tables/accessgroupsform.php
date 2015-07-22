<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMAccessgroupsForm extends JTable
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
	 * @see JTable::check
	 * @since 1.5
	 */
	function checkData() {

		// check for valid name
		if(trim($this->name) == '') {
			$this->setError(JText::_( 'Name angeben !' ));
			return false;
		}

		if(empty($this->usertype)) {
			$this->setError(JText::_( 'Type angeben' ));
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
