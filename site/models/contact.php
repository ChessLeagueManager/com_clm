<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Fred Baumgarten
*/
defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class CLMModelContact extends JModelLegacy
{
    public function _getCLMClmuser(&$options)
    {
        $user	= JFactory::getUser();
        $jid	= $user->get('id');
        $query	= "SELECT c.*, u.email as jmail FROM #__clm_user as c "
            ." LEFT JOIN #__users as u ON c.jid = u.id "
            ." WHERE c.jid = $jid AND c.sid in (select id from #__clm_saison as s where s.published = 1 and s.archiv = 0)";
        return $query;
    }

    public function getCLMClmuser($options = array())
    {
        $query	= $this->_getCLMClmuser($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function updateUser($fest, $mobil, $email, $jmail)
    {
        $user	= JFactory::getUser();
        $jid	= $user->get('id');
        $parray = array();
        $query	= "UPDATE #__clm_user SET ";
        $nc = 0;
        if ($fest !== "_ZERO_") {
            $query = $query . "tel_fest='" . $fest . "'";
            $nc++;
            $parray['tel_fest'] = $fest;
        }
        if ($mobil !== "_ZERO_") {
            if ($nc != 0) {
                $query .= ", ";
            }
            $query = $query . "tel_mobil='" . $mobil . "'";
            $nc++;
            $parray['tel_mobil'] = $mobil;
        }
        if ($email !== "_ZERO_") {
            if ($nc != 0) {
                $query .= ", ";
            }
            $query = $query . "email='" . $email . "'";
            $nc++;
            $parray['email'] = $email;
        }
        $query .= " WHERE jid = $jid AND sid in (select id from #__clm_saison where published = 1)";
        if ($nc != 0) {
            clm_core::$db->query($query);
            // Log - log
            $aktion = "Kontaktdatenpflege FE";
            clm_core::addDeprecated($aktion, json_encode($parray));
        }
        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->email_independent == 0 and $email !== "_ZERO_") {
            $query = "UPDATE #__users SET ";
            $query = $query . "email='" . $email . "'";
            $query = $query . " WHERE id='" . $jid . "'";
            clm_core::$db->query($query);
        }
        if ($clm_config->email_independent == 1 and $jmail !== "_ZERO_") {
            $query = "UPDATE #__users SET ";
            $query = $query . "email='" . $jmail . "'";
            $query = $query . " WHERE id='" . $jid . "'";
            clm_core::$db->query($query);
        }
    }
}
