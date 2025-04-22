<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2019 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

jimport('joomla.application.component.view');

class CLMViewTermine extends JViewLegacy
{
    public function display($tpl = 'pdf')
    // Man beachte den Unterschied zum Standard View "$tpl = null" !!
    {
        $model	  		= $this->getModel();
        $termine     	= $model->getTermine();
        $this->termine = $termine;

        $model	  		= $this->getModel();
        $termine_detail     	= $model->getTermine_Detail();
        $this->termine_detail = $termine_detail;

        $model	  		= $this->getModel();
        $plan  			= $model->getCLMSumPlan();
        $this->plan = $plan;

        $model	  		= $this->getModel();
        $schnellmenu  	= $model->getSchnellmenu();
        $this->schnellmenu = $schnellmenu;

        // Dokumenttyp setzen
        $document = JFactory::getDocument();
        $document->setMimeEncoding('application/pdf');
        parent::display($tpl);
    }
}
