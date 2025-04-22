<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
/**
 * erstellt einen Link innerhalb des Frontends
*/

class CLMcLink extends stdClass
{
    public function __construct($option = 'com_clm')
    {

        // INIT
        $this->option 			= $option;

        $this->view 			= null; // Name des Views

        $this->more 			= array(); // weitere Parameter als assoziatives Array

        $this->title			= null;

        $this->url 				= 'index.php?option='.$this->option;

    }


    /**
     * URL zusammensetzen
     * nicht nur interner Gebrauch in makeHTML() denkbar
     */
    public function makeURL()
    {

        if ($this->view) {
            $this->url .= '&amp;view='.$this->view;
        }

        if (count($this->more) > 0) {
            foreach ($this->more as $key => $value) {
                $this->url .= "&amp;".$key."=".$value;
            }
        }

        $this->url = JRoute::_($this->url);

    }


    /**
     * make HTML
     */
    public function makeLink($string)
    {

        // URL fertigstellen
        // $this->makeURL();

        $html_string = '<a href="'.$this->url.'"';

        if ($this->title != null) {
            $html_string .= ' title="'.$this->title.'"';
        }
        $html_string .= '>'.$string.'</a>';

        return $html_string;

    }

}
