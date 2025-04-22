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

class TableCLMMeldelisten extends JTable
{
    public $id			= 0;
    public $sid		= 0;
    public $lid		= 0;
    public $mnr		= 0;
    public $snr		= 0;
    public $mgl_nr		= 0;
    public $PKZ		= '';
    public $zps		= '';
    public $status		= '';
    public $ordering		= 0;
    public $DWZ		= 0;
    public $I0			= 0;
    public $start_dwz	= 0;
    public $start_I0	= 0;
    public $FIDEelo	= 0;
    public $Punkte		= 0;
    public $Partien		= 0;
    public $We			= 0;
    public $Leistung		= 0;
    public $EFaktor		= 0;
    public $Niveau		= 0;
    public $sum_saison		= 0;
    public $gesperrt		= 0;
    //	var $attr		= '';

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_meldeliste_spieler', 'id', $_db);
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
        /*		if (trim($this->name == '')) {
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
