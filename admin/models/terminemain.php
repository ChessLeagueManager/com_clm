<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelTermineMain extends JModelLegacy
{
    public $_pagination = null;
    public $_total = null;


    // benötigt für Pagination
    public function __construct()
    {
        parent::__construct();

        global $mainframe, $option;
        //Joomla 1.6 compatibility
        if (empty($mainframe)) {
            $mainframe = JFactory::getApplication();
            $option = $mainframe->scope;
        }

        $this->limit		= $mainframe->getUserStateFromRequest($option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $this->limitstart = $mainframe->getUserStateFromRequest($option.'limitstart', 'limitstart', 0, 'int');

        $this->setState('limit', $this->limit);
        $this->setState('limitstart', $this->limitstart);

        // user
        $this->user = JFactory::getUser();

        // get parameters
        $this->_getParameters();

        // get tournament data
        $this->_getTermine();

        $this->_getPagination();

    }


    // alle vorhandenen Parameter auslesen
    public function _getParameters()
    {

        $mainframe = JFactory::getApplication();
        global $option;

        if (!isset($this->param) or is_null($this->param)) {
            $this->param = array();
        }	// seit J 4.2 nötig um notice zu vermeiden
        // search
        $this->param['search'] = $mainframe->getUserStateFromRequest("$option.search", 'search', '', 'string');
        $this->param['search'] = strtolower($this->param['search']);

        // status
        $this->param['state'] = $mainframe->getUserStateFromRequest("$option.filter_state", 'filter_state', '', 'word');

        // Order
        $this->param['order'] = $mainframe->getUserStateFromRequest("$option.filter_order", 'filter_order', 'a.startdate', 'cmd');
        $this->param['order_Dir'] = $mainframe->getUserStateFromRequest("$option.filter_order_Dir", 'filter_order_Dir', 'desc', 'word');

    }

    public function _getTermine()
    {

        $query = 'SELECT a.* , u.name AS editor'
            . ' FROM #__clm_termine AS a'
            . ' LEFT JOIN #__users AS u ON u.id = a.checked_out'
            .$this->_sqlWhere();
        $this->termineTotal = $this->_getListCount($query);

        $query .= $this->_sqlOrder();
        if ($this->limit > 0) {
            $query .= ' LIMIT '.$this->limitstart.', '.$this->limit;
        }

        $this->_db->setQuery($query);
        $this->termine = $this->_db->loadObjectList();

        // Zudem weitere Daten ermitteln
        foreach ($this->termine as $key => $value) {
            // Verein
            $this->termine[$key]->hostname = clm_core::$load->zps_to_district($value->host);
        }
    }

    public function _sqlWhere()
    {

        // init
        $where = array();

        if ($this->param['search']) {
            $where[] = 'LOWER(a.name) LIKE "'.$this->_db->escape('%'.$this->param['search'].'%').'"';
        }

        if ($this->param['state']) {
            if ($this->param['state'] == 'P') {
                $where[] = 'a.published = 1';
            } elseif ($this->param['state'] == 'U') {
                $where[] = 'a.published = 0';
            }
        }

        $where 		= (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

        return $where;

    }

    public function _sqlOrder()
    {

        // array erlaubter order-Felder:
        $arrayOrderAllowed = array('a.name', 'a.beschreibung', 'a.address', 'a.category', 'a.host', 'a.startdate', 'a.published', 'a.id');

        // passt?
        if (!in_array($this->param['order'], $arrayOrderAllowed)) {
            $this->param['order'] = 'a.startdate';
            $this->param['order_Dir'] = 'desc';
        }

        $orderby = ' ORDER BY '. $this->param['order'] .' '. $this->param['order_Dir'] .', a.id';

        return $orderby;

    }

    public function _getPagination()
    {
        // Load the content if it doesn't already exist
        if (empty($this->pagination)) {
            jimport('joomla.html.pagination');
            $this->pagination = new JPagination($this->termineTotal, $this->limitstart, $this->limit);
        }
    }

}
