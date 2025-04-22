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

class TableCLMGruppen extends JTable
{
    public $id			= 0;
    public $Gruppe		= '';
    public $Meldeschluss	= '1970-01-01';
    public $geschlecht		= '0';
    public $alter_grenze	= '0';
    public $alter		= 0;
    public $status		= '';
    public $sid		= 0;
    public $user		= 0;
    public $bemerkungen	= '';
    public $bem_int		= '';
    public $published		= 0;
    public $checked_out	= null;
    public $checked_out_time	= null;
    public $ordering		= 0;
    public $anz_sgp		= 0;

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
        if (trim($this->Gruppe == '')) {
            $this->setError(JText::_('BNR_CLIENT_NAME'));
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
