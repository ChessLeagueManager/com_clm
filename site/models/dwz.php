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
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class CLMModelDWZ extends JModelLegacy
{
    public function _getCLMzps()
    {
        $mainframe	= JFactory::getApplication();
        $option 	= clm_core::$load->request_string('option');

        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $sid	= clm_core::$load->request_int('saison', 1);
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query = " SELECT a.* FROM #__clm_dwz_spieler as a"
            ." WHERE a.ZPS = '$zps'"
            ." AND a.sid = ".$sid
        ;

        $filter_order     = $mainframe->getUserStateFromRequest($option.'filter_order_dwz', 'filter_order', 'DWZ', 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($option.'filter_order_Dir_dwz', 'filter_order_Dir', 'DESC', 'word');

        $this->param = array();
        $this->param['order'] = $mainframe->getUserStateFromRequest("$option.filter_order", 'filter_order', 'DWZ', 'cmd');
        $this->param['order_Dir'] = $mainframe->getUserStateFromRequest("$option.filter_order_Dir", 'filter_order_Dir', 'desc', 'word');

        if (!empty($filter_order) && !empty($filter_order_Dir)) {
            $query .= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
            if ($filter_order == 'DWZ') {
                $query .= ', DWZ_Index '.$filter_order_Dir;
            }
        }

        return $query;
    }

    public function getCLMzps($options = array())
    {
        $query	= $this->_getCLMzps($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMLiga(&$options)
    {
        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $sid	= clm_core::$load->request_int('saison', 1);
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query = "SELECT Vereinname FROM #__clm_dwz_vereine as a "
            ." LEFT JOIN #__clm_saison as s ON s.id = ".$sid
            ." WHERE a.ZPS = '$zps'"
            ." AND a.sid = ".$sid
            ." AND s.published = 1"
        ;
        return $query;
    }
    public function getCLMLiga($options = array())
    {
        $query	= $this->_getCLMLiga($options);
        $result = $this->_getList($query);
        return @$result;
    }


    public function _getCLMVereinsliste(&$options)
    {
        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $sid	= clm_core::$load->request_int('saison', 1);
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query  = 'SELECT DISTINCT a.zps, a.name FROM #__clm_vereine as a'
            ." WHERE a.published = 1"
            ." ORDER BY a.name ASC "
        ;

        return $query;
    }

    public function getCLMVereinsliste($options = array())
    {
        $query	= $this->_getCLMVereinsliste($options);
        $result = $this->_getList($query);
        return @$result;
    }
    public function _getCLMSaisons(&$options)
    {
        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $sid	= clm_core::$load->request_int('saison', 1);
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query  = ' SELECT a.name, a.id, a.archiv FROM #__clm_saison AS a'
            ." ORDER BY a.name DESC "
        ;

        return $query;
    }

    public function getCLMSaisons($options = array())
    {
        $query	= $this->_getCLMSaisons($options);
        $result = $this->_getList($query);
        return @$result;
    }

}
