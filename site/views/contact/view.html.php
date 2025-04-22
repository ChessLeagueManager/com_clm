<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Fred Baumgarten
*/

jimport('joomla.application.component.view');

class CLMViewContact extends JViewLegacy
{
    public function display($tpl = null)
    {
        $fixed = clm_core::$load->request_string('fixed', '_ZERO_');
        $mobile = clm_core::$load->request_string('mobile', '_ZERO_');
        $email = clm_core::$load->request_string('email', '_ZERO_');
        $jmail = clm_core::$load->request_string('jmail', '_ZERO_');
        $model = $this->getModel();

        $model->updateUser($fixed, $mobile, $email, $jmail);

        $clmuser = $model->getCLMClmuser();
        parent::display($tpl);
    }
}
