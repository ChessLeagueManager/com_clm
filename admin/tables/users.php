<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMUsers extends JTable
{
	var $id			= 0;
	var $sid		= 0;
	var $jid		= 0;
	var $name		= '';
	var $username	= '';
	var $aktive		= 0;
	var $email		= '';
	var $tel_fest	= '';
	var $tel_mobil	= '';
	var $usertype	= '';
	var $zps		= '';
	var $mglnr		= '';
	var $PKZ		= '';
	var $org_exc		= '0';
	var $mannschaft		= 0;
	var $published		= 0;
	var $bemerkungen	= '';
	var $bem_int		= '';
	var $checked_out	= null;
	var $checked_out_time	= null;
	var $ordering		= 0;
	var $block		= 0;
	var $activation		= '';

	function __construct( &$_db ) {
		parent::__construct( '#__clm_user', 'id', $_db );
	}

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	function check()
	{

		// check for valid name
		if(trim($this->name) == '') {
			$this->setError(JText::_( 'Name angeben !' ));
			return false;
		}

		if(empty($this->email)) {
			$this->setError(JText::_( 'Email angeben' ));
			return false;
		}
		return true;
	}

}
