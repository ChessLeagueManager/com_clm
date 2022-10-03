<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMRunden extends JTable
{
	var $id			= null;
	var $sid		= null;
	var $name		= '';
	var $liga		= 0;
	var $nr			= 0;
	var $datum		= '1970-01-01';
	var $startzeit  = '00:00:00';
	var $deadlineday   = '1970-01-01';
	var $deadlinetime  = '00:00:00';
	var $meldung	= 0;
	var $sl_ok		= 0;
	var $published	= 0;
	var $bemerkungen	= '';
	var $bem_int		= '';
	var $gemeldet		= 0;
	var $editor		= 0;
	var $zeit		= '1970-01-01 00:00:00';
	var $edit_zeit	= '1970-01-01 00:00:00';
	var $checked_out	= 0;
	var $checked_out_time	= '1970-01-01 00:00:00';
	var $ordering		= 0;
	var $enddatum		= '1970-01-01';

	function __construct( &$_db ) {
		parent::__construct( '#__clm_runden_termine', 'id', $_db );
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
/**		// check for valid client name
		if (trim($this->name == '')) {
			$this->setError(JText::_( 'BNR_CLIENT_NAME' ));
			return false;
		}

		// check for valid client contact
		if (trim($this->sid == '')) {
			$this->setError(JText::_( 'Saison muss angegeben werden !' ));
			return false;
		}

**/		

		return true;
	}
}
