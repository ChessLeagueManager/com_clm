<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
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
	var $id			= 0;
	var $sid		= 0;
	var $lid		= 0;
	var $mnr		= 0;
	var $snr		= 0;
	var $mgl_nr		= 0;
	var $PKZ		= '';
	var $zps		= '';
	var $status		= '';
	var $ordering		= 0;
	var $DWZ		= 0;
	var $I0			= 0;
	var $start_dwz	= 0;
	var $start_I0	= 0;
	var $FIDEelo	= 0;
	var $Punkte		= 0;
	var $Partien		= 0;
	var $We			= 0;
	var $Leistung		= 0;
	var $EFaktor		= 0;
	var $Niveau		= 0;
	var $sum_saison		= 0;
	var $gesperrt		= 0;
	var $attr		= '';

	function __construct( &$_db ) {
		parent::__construct( '#__clm_meldeliste_spieler', 'id', $_db );
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
