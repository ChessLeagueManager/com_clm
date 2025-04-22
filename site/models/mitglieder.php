<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class CLMModelMitglieder extends JModelLegacy
{
    public function _getCLMSpieler(&$options)
    {

        $mainframe	= JFactory::getApplication();
        $option 	= clm_core::$load->request_string('option');

        $sid	= clm_core::$load->request_int('saison', '1');
        $zps	= clm_escape(clm_core::$load->request_string('zps'));

        $db	= JFactory::getDBO();
        $id	= @$options['id'];

        $query = "SELECT Spielername as name, Mgl_Nr as id, Status, Geburtsjahr, DWZ as dwz, DWZ_Index, FIDE_Elo as elo"
                ." FROM #__clm_dwz_spieler "
                ." WHERE ZPS = '$zps'  "
                ." AND sid = '$sid' "
        ;

        $filter_order     = $mainframe->getUserStateFromRequest($option.'filter_order_mgl', 'filter_order', 'name', 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($option.'filter_order_Dir_mgl', 'filter_order_Dir', 'ASC', 'word');

        $query .= " ORDER BY ".$filter_order." ".$filter_order_Dir .", name ";

        return $query;
    }
    public function getCLMSpieler($options = array())
    {
        $query	= $this->_getCLMSpieler($options);
        $result = $this->_getList($query);
        return @$result;
    }

    // Prüfen ob User berechtigt ist zu melden
    public function _getCLMClmuser(&$options)
    {
        $user	= JFactory::getUser();
        $jid	= $user->get('id');
        $sid	= clm_core::$load->request_int('saison', '1');

        $db	= JFactory::getDBO();
        $id	= @$options['id'];

        $query	= "SELECT zps,published "
            ." FROM #__clm_user "
            ." WHERE jid = $jid "
            ." AND sid = $sid "
        ;
        return $query;
    }

    public function getCLMClmuser($options = array())
    {
        $query	= $this->_getCLMClmuser($options);
        $result = $this->_getList($query);
        return @$result;
    }

}
