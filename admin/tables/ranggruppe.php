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

class TableCLMRanggruppe extends JTable
{
    public $id			= null;
    public $Gruppe		= '';
    public $Meldeschluss	= '1970-01-01';
    public $geschlecht		= '';
    public $alter_grenze	= '';
    public $alter		= '';
    public $status		= '';
    public $sid		= '';
    public $user		= '';
    public $bemerkungen	= '';
    public $bem_int		= '';
    public $published		= 0;
    public $checked_out	= null;
    public $checked_out_time	= null;
    public $ordering		= null;

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_rangliste_name', 'id', $_db);
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
        // check for valid client name
        /*	if (trim($this->Gruppe == '')) {
                $this->setError(JText::_( 'BNR_CLIENT_NAME' ));
                return false;
            }
*/
        // check for valid client contact
        /**		if (trim($this->sid == '')) {
                    $this->setError(JText::_( 'Saison muss angegeben werden !' ));
                    return false;
                }

        **/

        return true;
    }
}
