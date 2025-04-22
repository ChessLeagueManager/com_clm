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

class TableCLMMannschaften extends JTable
{
    public $id			= 0;
    public $sid		= 0;
    public $name		= '';
    public $liga		= 0;
    public $zps		= '';
    public $liste		= 0;
    public $edit_liste	= 0;
    public $man_nr		= 0;
    public $tln_nr		= 0;
    public $mf			= 0;
    public $sg_zps		= '';
    public $datum		= '1970-01-01 00:00:00';
    public $edit_datum	= '1970-01-01 00:00:00';
    public $lokal		= '';
    public $termine		= '';
    public $adresse		= '';
    public $homepage		= '';
    public $bemerkungen	= '';
    public $bem_int		= '';
    public $published		= 0;
    public $checked_out	= null;
    public $checked_out_time	= null;
    public $ordering		= 0;
    public $summanpunkte		= 0;
    public $sumbrettpunkte		= 0;
    public $sumwins		= 0;
    public $sumtiebr1		= 0;
    public $sumtiebr2		= 0;
    public $sumtiebr3		= 0;
    public $rankingpos		= 0;
    public $sname		= '';
    public $abzug		= 0;
    public $bpabzug	= 0;

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_mannschaften', 'id', $_db);
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
        if (trim($this->name == '')) {
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
