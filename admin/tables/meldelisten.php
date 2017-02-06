<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2017 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMMeldelisten extends JTable
{
	var $id			= null;
	var $sid		= '';
	var $lid		= '';
	var $mnr		= '';
	var $snr		= '';
	var $mgl_nr		= '';
	var $zps		= '';
	var $status		= '';
	var $ordering		= '';
	var $DWZ		= '';
	var $I0			= '';
	var $Punkte		= '';
	var $Partien		= '';
	var $We			= '';
	var $Leistung		= '';
	var $EFaktor		= '';
	var $Niveau		= '';
	var $sum_saison		= '';
	var $gesperrt		= '';
	var $attr		= '';

	function __construct( &$_db ) {
		parent::__construct( '#__clm_meldeliste_spieler', 'sid', $_db );
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
/*		if (trim($this->name == '')) {
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
