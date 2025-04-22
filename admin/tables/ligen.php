<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableCLMLigen extends JTable
{
    public $id			= 0;
    public $name		= '';
    public $sid		= 0;
    public $teil		= '';
    public $stamm		= '';
    public $ersatz		= '';
    public $rang		= '';
    public $sl			= '';
    public $runden		= '';
    public $durchgang		= '';
    public $heim		= '';
    public $sieg_bed		= '';
    public $runden_modus	= '';
    public $man_sieg		= '';
    public $man_remis		= '';
    public $man_nieder		= '';
    public $man_antritt	= '';
    public $sieg		= '';
    public $remis		= '';
    public $nieder		= '';
    public $antritt		= '';
    public $mail		= '';
    public $sl_mail		= '';
    public $order		= '';
    public $rnd		= '';
    public $ab			= 0;
    public $ab_evtl	= 0;
    public $auf		= 0;
    public $auf_evtl	= 0;
    public $published		= '';
    public $bemerkungen	= '';
    public $bem_int		= '';
    public $checked_out	= null;
    public $checked_out_time	= null;
    public $ordering		= 0;
    public $b_wertung		= '';
    public $liga_mt		= '';
    public $tiebr1		= 0;
    public $tiebr2		= 0;
    public $tiebr3		= 0;
    public $ersatz_regel	= '';
    public $anzeige_ma	= '';
    public $params 		= '';

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_liga', 'id', $_db);
    }

    /**
     * Overloaded check function
     */
}
