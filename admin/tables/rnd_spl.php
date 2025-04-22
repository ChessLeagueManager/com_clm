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
defined('_JEXEC') or die('Restricted access');

class TableCLMRnd_spl extends JTable
{
    public $id			= null;
    public $sid		= null;
    public $lid		= null;
    public $runde		= null;
    public $paar		= null;
    public $dg			= null;
    public $tln_nr		= null;
    public $brett		= null;
    public $heim		= null;
    public $weiss		= null;
    public $spieler		= null;
    public $zps		= null;
    public $gegner		= null;
    public $gzps		= null;
    public $ergebnis		= null;
    public $kampflos		= null;
    public $punkte		= null;
    public $gemeldet		= null;
    public $dwz_edit		= null;
    public $dwz_editor		= null;

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_rnd_spl', 'id', $_db);
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
