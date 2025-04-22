<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableCLMAccessgroupsForm extends JTable
{
    public $id			= null;
    public $name		= '';
    public $usertype	= '';
    public $kind		= 'USER';
    public $published	= 0;
    public $ordering	= 0;
    public $params	= '';

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_usertype', 'id', $_db);
    }

    /**
     * Overloaded check function
     *
     * @access public
     * @return boolean
     * @see JTable::check
     * @since 1.5
     */
    public function checkData()
    {

        // check for valid name
        if (trim($this->name) == '') {
            $this->setError(JText::_('Name angeben !'));
            return false;
        }

        if (empty($this->usertype)) {
            $this->setError(JText::_('Type angeben'));
            return false;
        }
        return true;
    }

    public function reorderAll()
    {
        $query = 'SELECT DISTINCT name FROM '.$this->_db->nameQuote($this->_tbl);
        $this->_db->setQuery($query);
        $names = $this->_db->loadResultArray();


        foreach ($names as $name) {
            $this->reorder('name = '.$name);
        }
        return true;
    }

}
