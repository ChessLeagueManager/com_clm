<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMMannschaften extends JTable
{

	var $id			= null;
	var $sid		= null;
	var $name		= '';
	var $liga		= '';
	var $zps		= '';
	var $liste		= '';
	var $edit_liste		= '';
	var $man_nr		= '';
	var $tln_nr		= '';
	var $mf			= '';
	var $sg_zps		= '';
	var $datum		= '';
	var $edit_datum		= '';
	var $lokal		= '';
	var $homepage		= '';
	var $adresse		= '';
	var $termine		= '';
	var $bemerkungen	= '';
	var $bem_int		= '';
	var $published		= 0;
	var $checked_out	= 0;
	var $checked_out_time	= 0;
	var $ordering		= null;
	var $summanpunkte		= 0;
	var $sumbrettpunkte		= 0;
	var $sumwins		= 0;
	var $sumtiebr1		= 0;
	var $sumtiebr2		= 0;
	var $sumtiebr3		= 0;
	var $rankingpos		= 0;
	var $sname		= '';
 
	function __construct( &$_db ) {
		parent::__construct( '#__clm_mannschaften', 'id', $_db );
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
		if (trim($this->name == '')) {
			$this->setError(JText::_( 'BNR_CLIENT_NAME' ));
			return false;
		}

		// check for valid client contact
/**		if (trim($this->sid == '')) {
			$this->setError(JText::_( 'Saison muss angegeben werden !' ));
			return false;
		}

**/		

		return true;
	}
}
