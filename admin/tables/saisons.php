<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

class TableCLMSaisons extends JTable
{
    public $id				= 0;
    public $name			= '';
    public $published		= 0;
    public $archiv			= 0;
    public $bemerkungen	= '';
    public $bem_int		= '';
    public $checked_out	= null;
    public $checked_out_time	= null;
    public $ordering		= 0;
    public $datum			= '1970-01-01';
    public $rating_type	= 0;

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_saison', 'id', $_db);
    }
}
