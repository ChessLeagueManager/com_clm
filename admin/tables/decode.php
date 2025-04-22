<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

class TableCLMDecode extends JTable
{
    public $id			= 0;
    public $sid		= 0;
    public $source		= '';
    public $oname		= '';
    public $nname		= '';
    public $verein		= '';

    public function __construct($_db)
    {
        parent::__construct('#__clm_player_decode', 'id', $_db);
    }

    public function check()
    {
        return true;
    }

}
