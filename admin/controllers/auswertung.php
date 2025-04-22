<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class CLMControllerAuswertung extends JControllerLegacy
{
    public function __construct()
    {
        parent::__construct();
    }

    public function display($cachable = false, $urlparams = array())
    {
        $_REQUEST['view'] = 'auswertung';
        parent::display();
    }

}
