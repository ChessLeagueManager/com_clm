<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CLMViewMailapprove extends JViewLegacy
{
    public $_namespace	= 'com_user.reset.';

    public function display($tpl = null)
    {
        jimport('joomla.html.html');

        require("index.php");
        $parameter = $_GET["parameter"];
        if (!isset($parameter)) {
            return;
        }
        if ($parameter == '') {
            return;
        }

        $result = clm_core::$api->db_tournament_registration_approve($parameter);
        $lang = clm_core::$lang->registration;
        $app	= JFactory::getApplication();
        $mtext	= $result[1];
        $msg	= $lang->$mtext;
        if ($result[0] === true) {
            $tid  = $result[2];
            $type = 'message';
            $link = str_replace('components/com_clm/clm/', '', JURI::base()).'index.php?option=com_clm&view=turnier_info&turnier='.$tid.'&Itemid=';
        } elseif ($result[1] == 'e_alreadyApproved') {
            $tid  = $result[2];
            $type = 'warning';
            $link = str_replace('components/com_clm/clm/', '', JURI::base()).'index.php?option=com_clm&view=turnier_info&turnier='.$tid.'&Itemid=';
        } elseif ($result[1] == 'e_toolateApproved') {
            $tid  = $result[2];
            $type = 'warning';
            $link = str_replace('components/com_clm/clm/', '', JURI::base()).'index.php?option=com_clm&view=turnier_info&turnier='.$tid.'&Itemid=';
        } else {
            $tid  = 0;
            $type = 'warning';
            $mtext	= 'e_approve_error';
            $msg	= $lang->$mtext;
            $link = str_replace('components/com_clm/clm/', '', JURI::base()).'index.php';
        }
        $app->enqueueMessage($msg, $type);
        $app->redirect($link);
    }
}
