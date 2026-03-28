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

class TableCLMacl_aro extends Table
{
	var $section_value	= '';
	var $value		= '';
	var $name		= '';

	function __construct( &$_db ) {
		parent::__construct( '#__core_acl_aro', 'id', $_db );
	}

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see Table::check
	 * @since 1.5
	 */
	function check()
	{

		// check for valid name
		if(trim($this->name) == '') {
			$this->setError(Text::_( 'Name angeben !' ));
			return false;
		}

		if(empty($this->email)) {
			$this->setError(Text::_( 'Email angeben' ));
			return false;
		}
		return true;
	}

}
