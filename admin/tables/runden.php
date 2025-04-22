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

class TableCLMRunden extends JTable
{
    public $id			= null;
    public $sid		= null;
    public $name		= '';
    public $liga		= 0;
    public $nr			= 0;
    public $datum		= '1970-01-01';
    public $startzeit  = '00:00:00';
    public $deadlineday   = '1970-01-01';
    public $deadlinetime  = '00:00:00';
    public $meldung	= 0;
    public $sl_ok		= 0;
    public $published	= 0;
    public $bemerkungen	= '';
    public $bem_int		= '';
    public $gemeldet		= 0;
    public $editor		= 0;
    public $zeit		= '1970-01-01 00:00:00';
    public $edit_zeit	= '1970-01-01 00:00:00';
    public $checked_out	= null;
    public $checked_out_time	= null;
    public $ordering		= 0;
    public $enddatum		= '1970-01-01';

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_runden_termine', 'id', $_db);
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
