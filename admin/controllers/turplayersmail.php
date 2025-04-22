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

class CLMControllerTurPlayersMail extends JControllerLegacy
{
    // Konstruktor
    public function __construct($config = array())
    {

        parent::__construct($config);

        $this->app 	= JFactory::getApplication();

    }


    public function mail_send()
    {

        $msg = '';
        // Konfigurationsparameter auslesen - get configuration parameter
        $config = clm_core::$db->config();
        $from = $config->email_from;
        $fromname = $config->email_fromname;
        //$htmlMail = $config->email_type;
        $htmlMail = 0;
        if ($from == '') {
            $msg .= '<br>'.JText::_('REGISTRATION_E_INSTALL_MAIL');
        }
        if ($fromname == '') {
            $msg .= '<br>'.JText::_('REGISTRATION_E_INSTALL_NAME');
        }
        $mail_to 	= clm_core::$load->request_string('mail_to', '');
        $mail_bcc 	= clm_core::$load->request_string('mail_bcc', '');
        $mail_subj 	= clm_core::$load->request_string('mail_subj', '');
        $mail_body 	= clm_core::$load->request_string('mail_body', '');

        if ($mail_to == '') {
            $msg .= '<br>'.JText::_('MAIL_TO_EMPTY');
        }
        if ($mail_bcc == '') {
            $msg .= '<br>'.JText::_('MAIL_BCC_EMPTY');
        }
        if ($mail_subj == '') {
            $msg .= '<br>'.JText::_('MAIL_SUBJ_EMPTY');
        }
        if ($mail_body == '') {
            $msg .= '<br>'.JText::_('MAIL_BODY_EMPTY');
        }
        $count_mail = 0;
        // mail to TL
        //		$result = clm_core::$cms->sendMail($from, $fromname, $mail_to, $mail_subj, $mail_body, $htmlMail);
        $result = clm_core::$api->mail_send($mail_to, $mail_subj.' (Kopie für Turnierleiter)', $mail_body, $htmlMail);
        if ($result[0] !== true) {
            $msg .= '<br>'.JText::_('MAIL_ERROR').' '.$mail_to;
        } else {
            $count_mail++;
        }
        // mail to participants
        $a_bcc = explode(';', $mail_bcc);
        foreach ($a_bcc as $bcc) {
            if ($bcc == '') {
                continue;
            }
            //			$result = clm_core::$cms->sendMail($from, $fromname, $bcc, $mail_subj, $mail_body, $htmlMail);
            $result = clm_core::$api->mail_send($bcc, $mail_subj, $mail_body, $htmlMail);
            if ($result[0] !== true) {
                $msg .= '<br>'.JText::_('MAIL_ERROR').' '.$bcc;
            } else {
                $count_mail++;
            }
        }
        if ($msg != '') {
            $this->app->enqueueMessage(substr($msg, 4), 'warning');
        }
        if ($count_mail > 0) {
            $this->app->enqueueMessage($count_mail.' '.JText::_('MAIL_SENT'), 'message');
        }

        // turnierid
        $turnierid = clm_core::$load->request_int('turnierid');

        $adminLink = new AdminLink();
        $adminLink->view = "turplayers";
        $adminLink->more = array('id' => $turnierid);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }



    public function cancel()
    {

        // turnierid
        $turnierid = clm_core::$load->request_int('turnierid');

        $adminLink = new AdminLink();
        $adminLink->view = "turplayers";
        $adminLink->more = array('id' => $turnierid);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }

}
