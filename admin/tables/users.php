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

class TableCLMUsers extends JTable
{
    public $id			= 0;
    public $sid		= 0;
    public $jid		= 0;
    public $name		= '';
    public $username	= '';
    public $aktive		= 0;
    public $email		= '';
    public $tel_fest	= '';
    public $tel_mobil	= '';
    public $usertype	= '';
    public $zps		= '';
    public $mglnr		= '';
    public $PKZ		= '';
    public $org_exc		= '0';
    public $mannschaft		= 0;
    public $published		= 0;
    public $bemerkungen	= '';
    public $bem_int		= '';
    public $checked_out	= null;
    public $checked_out_time	= null;
    public $ordering		= 0;
    public $block		= 0;
    public $activation		= '';

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_user', 'id', $_db);
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

        // check for valid name
        if (trim($this->name) == '') {
            $this->setError(JText::_('Name angeben !'));
            return false;
        }

        if (empty($this->email)) {
            $this->setError(JText::_('Email angeben'));
            return false;
        }
        return true;
    }

}
