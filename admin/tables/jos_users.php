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

class TableCLMJos_users extends JTable
{
    public $id			= null;
    public $name		= '';
    public $username	= '';
    public $email		= '';
    public $password	= '';
    public $usertype	= '';
    public $block		= '';
    public $sendEmail	= '';
    public $gid		= '';
    public $registerDate	= '1970-01-01 00:00:00';
    public $lastvisitDate	= '1970-01-01 00:00:00';
    public $activation		= '';
    public $params		= '';

    public function __construct(&$_db)
    {
        parent::__construct('#__users', 'id', $_db);
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
