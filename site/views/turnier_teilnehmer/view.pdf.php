<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2019 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
jimport('joomla.application.component.view');

class CLMViewTurnier_Teilnehmer extends JViewLegacy
{
    public function display($tpl = 'pdf')
    // Man beachte den Unterschied zum Standard View "$tpl = null" !!
    {
        $config = clm_core::$db->config();
        $model	  = $this->getModel();

        $this->turnier = $model->turnier;
        $this->players = $model->players;

        // Dokumenttyp setzen
        $document = JFactory::getDocument();
        $document->setMimeEncoding('application/pdf');
        parent::display($tpl);
    }
}
