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

class CLMViewTurnier_Rangliste extends JViewLegacy
{
    public function display($tpl = 'pdf')
    // Man beachte den Unterschied zum Standard View "$tpl = null" !!
    {
        $model	  = $this->getModel();

        $this->turnier = $model->turnier;

        $this->players = $model->players;
        $this->posToPlayers = $model->posToPlayers;

        $this->matches = $model->matches;
        $this->matrix = $model->matrix;

        // Dokumenttyp setzen
        $document = JFactory::getDocument();
        $document->setMimeEncoding('application/pdf');
        parent::display($tpl);
    }
}
