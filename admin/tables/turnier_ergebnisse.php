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
defined('_JEXEC') or die('Restricted access');

class TableCLMTurnier_Ergebnisse extends JTable
{
    public $id				= null;
    public $sid				= null;
    public $turnier		= '';
    public $runde			= '';
    public $paar			= '';
    public $brett			= '';
    public $dg				= '';
    public $tln_nr			= '';
    public $heim			= '';
    public $spieler		= '';
    public $gegner			= '';
    public $ergebnis		= '';
    public $tiebrS		= 0;
    public $tiebrG		= 0;
    public $kampflos		= '';
    public $gemeldet		= 0;
    public $pgn			= '';
    public $ordering		= null;

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_turniere_rnd_spl', 'id', $_db);
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
}
