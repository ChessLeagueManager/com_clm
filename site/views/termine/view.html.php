<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Fjodor Schäfer
 * @email ich@vonfio.de
*/

jimport('joomla.application.component.view');

class CLMViewTermine extends JViewLegacy
{
    public function display($tpl = null)
    {
        $model		= $this->getModel();
        $termine    = $model->getTermine();
        $this->termine = $termine;

        $model	  		= $this->getModel();
        $termine_detail     	= $model->getTermine_Detail();
        $this->termine_detail = $termine_detail;

        $model	  		= $this->getModel();
        $schnellmenu  	= $model->getSchnellmenu();
        $this->schnellmenu = $schnellmenu;

        parent::display($tpl);
    }
}
