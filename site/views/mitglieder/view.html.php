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

jimport('joomla.application.component.view');

class CLMViewMitglieder extends JViewLegacy
{
    public function display($tpl = null)
    {
        $model	= $this->getModel();
        $spieler = $model->getCLMSpieler();
        $this->spieler = $spieler;

        $model	= $this->getModel();
        $clmuser = $model->getCLMCLMuser();
        $this->clmuser = $clmuser;

        /* Call the state object */
        $state = $this->get('state');

        /* Get the values from the state object that were inserted in the model's construct function */
        $lists['order']     = $state->get('filter_order_mgl');
        $lists['order_Dir'] = $state->get('filter_order_Dir_mgl');

        $this->lists = $lists;

        parent::display($tpl);
    }
}
