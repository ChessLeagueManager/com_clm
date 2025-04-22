<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

/**
 * erstellt einen Link innerhalb des Administrationsbereichs
*/

class AdminLink extends stdClass
{
    public function __construct($option = 'com_clm')
    {

        // INIT
        $this->option 			= $option;

        $this->view 			= null; // Name des Views

        $this->more 			= array(); // weitere Parameter als assoziatives Array

        $this->url 				= 'index.php?option='.$this->option;

    }


    /**
     * URL zusammensetzen
     */
    public function makeURL()
    {

        if ($this->view) {
            $this->url .= '&view='.$this->view;
        }

        if (count($this->more) > 0) {
            foreach ($this->more as $key => $value) {
                $this->url .= "&".$key."=".$value;
            }
        }

        // Achtung: eine Verwendung von JRoute ist nicht nötig,
        // da eine SEO-Optimierung im backend überflüssig scheint

    }


}
