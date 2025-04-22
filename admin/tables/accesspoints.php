<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableCLMAccessPoints extends JTable
{
    public $id			= null;
    public $area		= '';
    public $accesstopic = '';
    public $accesspoint = '';
    public $rule		= '';
    public $published	= 0;
    public $ordering	= 0;


    public function __construct(&$_db)
    {
        parent::__construct('#__clm_access_points', 'id', $_db);
    }

    /**
     * Overloaded check function
     *
     * @access public
     * @return boolean
     * @see JTable::check
     * @since 1.5
     */
    // wegen Abwärtskompatibilität kein Überschreiben und Verwenden von check()
    public function checkData()
    {

        // aktuelle Daten laden
        $accesspoint = new CLMAccesspoint($this->id, true);

        if (trim($this->accesspoint) == '') { // Name vorhanden
            $this->setError(CLMText::errorText('NAME', 'MISSING'));
            return false;
        }

        return true;

    }


}
