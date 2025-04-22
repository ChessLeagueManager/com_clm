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
defined('_JEXEC') or die('Restricted access');

class TableCLMSonderranglistenform extends JTable
{
    public $id						= null;
    public $turnier				= null;
    public $name					= '';
    public $use_rating_filter 		= false;
    public $rating_type			= 1;
    public $rating_higher_than 	= 0;
    public $rating_lower_than 		= 0;
    public $use_birthYear_filter	= false;
    public $birthYear_younger_than	= 0;
    public $birthYear_older_than	= 0;
    public $use_sex_filter			= false;
    public $sex					= '';
    public $use_sex_year_filter	= false;
    public $maleYear_younger_than	= 0;
    public $maleYear_older_than	= 0;
    public $femaleYear_younger_than	= 0;
    public $femaleYear_older_than	= 0;
    public $use_zps_filter 		= false;
    public $zps_higher_than 		= '';
    public $zps_lower_than 		= 'ZZZZZ';
    public $published				= 0;
    public $checked_out			= null;
    public $checked_out_time		= null;
    public $ordering				= null;

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_turniere_sonderranglisten', 'id', $_db);
    }

    public function check()
    {

        return true;

    }

    public function checkData()
    {

        if (trim($this->name) == '') { // Name vorhanden
            $this->setError(CLMText::errorText('NAME', 'MISSING'));
            return false;
        }
        return true;

    }

    public function reorderAll()
    {
        $query = 'SELECT DISTINCT turnier FROM '.$this->_db->nameQuote($this->_tbl);
        $this->_db->setQuery($query);
        $turniere = $this->_db->loadResultArray();


        foreach ($turniere as $turnier) {
            $this->reorder('turnier = '.$turnier);
        }
        return true;
    }

}
