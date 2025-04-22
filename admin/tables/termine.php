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

class TableCLMTermine extends JTable
{
    public $id					= 0;
    public $name				= '';
    public $beschreibung		= '';
    public $address			= '';
    public $catidAlltime		= 0;
    public $catidEdition		= 0;
    public $category			= '';
    public $host				= '';
    public $startdate			= '1970-01-01';
    public $starttime			= '00:00:00';
    public $allday				= 0;
    public $enddate			= '1970-01-01';
    public $endtime			= '00:00:00';
    public $noendtime			= 0;
    public $attached_file		= '';
    public $attached_file_description	= '';
    public $published			= 0;
    public $checked_out		= null;
    public $checked_out_time	= null;
    public $ordering			= 0;
    public $event_link			= '';

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_termine', 'id', $_db);
    }

    /**
     * Overloaded check function
     *
     * @access public
     * @return boolean
     * @see JTable::check
     * @since 1.5
     */
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


}
