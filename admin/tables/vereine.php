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

class TableCLMVereine extends JTable
{

	var $id			= 0;
	var $name		= '';
	var $sid		= 0;
	var $zps		= '';
	var $vl			= '';
	var $lokal		= '';
	var $homepage		= '';
	var $adresse		= '';
	var $vs			= '';
	var $vs_mail		= '';
	var $vs_tel		= '';
	var $tl			= '';
	var $tl_mail		= '';
	var $tl_tel		= '';
	var $jw			= '';
	var $jw_mail		= '';
	var $jw_tel		= '';
	var $pw			= '';
	var $pw_mail		= '';
	var $pw_tel		= '';
	var $kw			= '';
	var $kw_mail		= '';
	var $kw_tel		= '';
	var $sw			= '';
	var $sw_mail		= '';
	var $sw_tel		= '';
	var $termine		= '';
	var $published		= 0;
	var $bemerkungen	= '';
	var $bem_int		= '';
	var $checked_out	= 0;
	var $checked_out_time	= '1970-01-01 00:00:00';
	var $ordering		= 0;

	function __construct( &$_db ) {
		parent::__construct( '#__clm_vereine', 'id', $_db );
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
