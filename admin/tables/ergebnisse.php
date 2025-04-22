<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

class TableCLMErgebnisse extends JTable
{
    public $id			= null;
    public $sid		= 0;
    public $lid		= '';
    public $runde		= '';
    public $paar		= '';
    public $dg			= '';
    public $heim		= '';
    public $tln_nr		= '';
    public $gegner		= '';
    public $ergebnis	= null;
    public $kampflos	= null;
    public $brettpunkte	= '';
    public $manpunkte		= '';
    public $bp_sum		= '';
    public $mp_sum		= '';
    public $gemeldet		= '';
    public $editor		= 0;
    public $dwz_editor		= 0;
    public $zeit		= '1970-01-01 00:00:00';
    public $edit_zeit		= '1970-01-01 00:00:00';
    public $dwz_zeit		= '1970-01-01 00:00:00';
    public $published		= 0;
    public $checked_out	= null;
    public $checked_out_time	= null;
    public $ordering		= null;
    public $ko_decision	= 0;
    public $comment		= '';
    public $icomment		= '';
    public $pdate		= '1970-01-01';
    public $ptime		= '00:00:00';

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_rnd_man', 'id', $_db);
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
