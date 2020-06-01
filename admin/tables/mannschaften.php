<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMMannschaften extends JTable
{
	var $id			= 0;
	var $sid		= 0;
	var $name		= '';
	var $liga		= 0;
	var $zps		= '';
	var $liste		= 0;
	var $edit_liste	= 0;
	var $man_nr		= 0;
	var $tln_nr		= 0;
	var $mf			= 0;
	var $sg_zps		= '';
	var $datum		= '1970-01-01 00:00:00';
	var $edit_datum	= '1970-01-01 00:00:00';
	var $lokal		= '';
	var $termine		= '';
	var $adresse		= '';
	var $homepage		= '';
	var $bemerkungen	= '';
	var $bem_int		= '';
	var $published		= 0;
	var $checked_out	= 0;
	var $checked_out_time	= '1970-01-01 00:00:00';
	var $ordering		= 0;
	var $summanpunkte		= 0;
	var $sumbrettpunkte		= 0;
	var $sumwins		= 0;
	var $sumtiebr1		= 0;
	var $sumtiebr2		= 0;
	var $sumtiebr3		= 0;
	var $rankingpos		= 0;
	var $sname		= '';
	var $abzug		= 0;
	var $bpabzug	= 0;
 
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
