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

class TableCLMRegistrations extends JTable
{
    public $id			= 0;
    public $tid		= 0;
    public $name		= '';
    public $vorname 	= '';
    public $birthYear	= '0000';
    public $geschlecht = null;
    public $club	 	= '';
    public $email		= '';
    public $dwz		= 0;
    public $dwz_I0		= 0;
    public $elo		= 0;
    public $FIDEid		= null;
    public $FIDEcco	= null;
    public $titel		= null;
    public $mgl_nr		= 0;
    public $PKZ		= null;
    public $zps		= '0';
    public $tel_no		= '';
    public $account	= '';
    public $comment	= null;
    public $status		= 0;
    public $timestamp	= 0;
    public $ordering	= 0;

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_online_registration', 'id', $_db);
    }

    /**
     * Overloaded check function
     */

    /**
     * Overloaded check function
     *
     * @access public
     * @return boolean
     * @see JTable::check
     * @since 1.5
     */
    // wegen Abwärtskompatibilität kein Überschreiben und Verwenden von check()
    // kann bei Bedarf geändert werden, wenn alte Turnier-Implementierung gekappt wird.
    public function checkData()
    {

        // aktuelle Turnierdaten laden
        /*		$tournament = new CLMTournament($this->id, true);

                if (trim($this->name) == '') { // Name vorhanden
                    $this->setError( CLMText::errorText('NAME', 'MISSING') );
                    return false;
                }
        */
        return true;

    }


}
