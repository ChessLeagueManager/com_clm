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

class TableCLMJos_users extends Table
{
	var $id			= null;
	var $name		= '';
	var $username	= '';
	var $email		= '';
	var $password	= '';
	var $usertype	= '';
	var $block		= '';
	var $sendEmail	= '';
	var $gid		= '';
	var $registerDate	= '1970-01-01 00:00:00';
	var $lastvisitDate	= '1970-01-01 00:00:00';
	var $activation		= '';
	var $params		= '';

	function __construct( &$_db ) {
		parent::__construct( '#__users', 'id', $_db );
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
