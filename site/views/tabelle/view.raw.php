<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2022 CLM Team. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

jimport('joomla.application.component.view');

class CLMViewTabelle extends JViewLegacy
{
    public function display($tpl = 'raw')
    {
        $model	  = $this->getModel();
        $liga     = $model->getCLMLiga();
        $this->liga = $liga;

        if ($liga[0]->runden_modus == 4 or $liga[0]->runden_modus == 5) {
            $app = JFactory::getApplication();
            $app->redirect('index.php?option=com_clm&amp;view=paarungsliste&amp;format=raw&amp;layout=default&amp;saison='.$liga[0]->sid.'&amp;liga='.$liga[0]->id.'&amp;Itemid=99');
        }

        $model	  = $this->getModel();
        $spielfrei     = $model->getCLMSpielfrei();
        $this->spielfrei = $spielfrei;

        $model	  = $this->getModel();
        $punkte     = $model->getCLMPunkte();
        $this->punkte = $punkte;

        $html	= clm_core::$load->request_string('html', '1');
        if ($html != "1") {
            $document = JFactory::getDocument();
            $document->setMimeEncoding('text/css');
        }

        parent::display($tpl);
    }
}
