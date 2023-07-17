<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMRanggruppe extends JTable
{

	var $id			= null;
	var $Gruppe		= '';
	var $Meldeschluss	= '1970-01-01';
	var $geschlecht		= '';
	var $alter_grenze	= '';
	var $alter		= '';
	var $status		= '';
	var $sid		= '';
	var $user		= '';
	var $bemerkungen	= '';
	var $bem_int		= '';
	var $published		= 0;
	var $checked_out	= 0;
	var $checked_out_time	= '1970-01-01 00:00:00';
	var $ordering		= null;

	function __construct( &$_db ) {
		parent::__construct( '#__clm_rangliste_name', 'id', $_db );
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
		// check for valid client name
	/*	if (trim($this->Gruppe == '')) {
			$this->setError(JText::_( 'BNR_CLIENT_NAME' ));
			return false;
		}
*/
		// check for valid client contact
/**		if (trim($this->sid == '')) {
			$this->setError(JText::_( 'Saison muss angegeben werden !' ));
			return false;
		}

**/		

		return true;
	}
}
