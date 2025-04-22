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

class TableCLMVereine extends JTable
{
    public $id			= 0;
    public $name		= '';
    public $sid		= 0;
    public $zps		= '';
    public $vl			= '';
    public $lokal		= '';
    public $homepage		= '';
    public $adresse		= '';
    public $vs			= '';
    public $vs_mail		= '';
    public $vs_tel		= '';
    public $tl			= '';
    public $tl_mail		= '';
    public $tl_tel		= '';
    public $jw			= '';
    public $jw_mail		= '';
    public $jw_tel		= '';
    public $pw			= '';
    public $pw_mail		= '';
    public $pw_tel		= '';
    public $kw			= '';
    public $kw_mail		= '';
    public $kw_tel		= '';
    public $sw			= '';
    public $sw_mail		= '';
    public $sw_tel		= '';
    public $termine		= '';
    public $published		= 0;
    public $bemerkungen	= '';
    public $bem_int		= '';
    public $checked_out	= null;
    public $checked_out_time	= null;
    public $ordering		= 0;

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_vereine', 'id', $_db);
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
