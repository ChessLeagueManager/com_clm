<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelPGNntable extends JModelLegacy
{
    public function __construct()
    {

        parent::__construct();

        // Konfigurationsparameter auslesen
        $config = clm_core::$db->config();
    }


    // Turnierdaten
    public function getTurnier()
    {
        $liga = clm_core::$load->request_string('liga', '');
        $liga_arr = explode('.', $liga, 2);
        $tkz = $liga_arr[0];
        $tid = $liga_arr[1];
        $_POST['tkz'] = $tkz;
        $_POST['tid'] = $tid;
        $tkz = clm_core::$load->request_string('tkz', '0');
        $tid = clm_core::$load->request_string('tid', '0');
        if ($tkz == 't') { 		// Teamwettbewerb
            $query = "SELECT * FROM #__clm_liga "
                    .' WHERE id = '.$tid;
        } elseif ($tkz == 's') { 		// Teamwettbewerb
            $query = "SELECT * FROM #__clm_turniere "
                    .' WHERE id = '.$tid;
        }
        $turnier	= clm_core::$db->loadObjectList($query);
        $this->turnier = $turnier;

        return $turnier;
    }

    public function getMainPGN()
    {

        $task = clm_core::$load->request_string('task', '');
        $stask = clm_core::$load->request_string('stask', '');

        $liga = clm_core::$load->request_string('liga', '');
        $liga_arr = explode('.', $liga, 2);
        $tkz = $liga_arr[0];
        $tid = $liga_arr[1];

        // offene Notationen auslesen
        $query = "SELECT * FROM #__clm_pgn "
            .' WHERE tkz = "'.$tkz.'"'
            .' AND tid = '.$tid
            .' AND runde = 0 ';
        $gameslist	= clm_core::$db->loadObjectList($query);
        $zz = 0;
        $pgn_arr = array();
        foreach ($gameslist as $gl) {
            $return_arr = array();
            $return_arr['pgnnr'] = $gl->id;
            $return_arr['tkz'] = $gl->tkz;
            $return_arr['tid'] = $gl->tid;
            $return_arr['dg'] = $gl->dg;
            $return_arr['runde'] = $gl->runde;
            if ($tkz == 't') {
                $return_arr['paar'] = $gl->paar;
            } else {
                $return_arr['paar'] = 0;
            }
            $return_arr['brett'] = $gl->brett;
            $return_arr['text'] = $gl->text;
            $return_arr['error'] = $gl->error;
            $pgn_arr[] = $return_arr;
        }

        return $pgn_arr;
    }

    // Tabelleneinträge für pgn-Import
    public function getTEntries()
    {
        $query = "SELECT * FROM #__clm_player_decode "
                .' WHERE sid = '.$this->turnier[0]->sid
                .' AND source = "pgn_import" ';
        $tentries	= clm_core::$db->loadObjectList($query);
        $aentries = array();
        foreach ($tentries as $te1) {
            $aentries[$te1->oname] = $te1->nname;
        }
        return $aentries;
    }


    public function store()
    {

        $verein = '';
        $tid 	= clm_core::$load->request_string('tid', '0');
        $tkz 	= clm_core::$load->request_string('tkz', '0');
        $pgn_count 	= clm_core::$load->request_int('pgn_count', -1);

        // DB-Zugriff
        for ($p = 0; $p < $pgn_count ; $p++) {

            $sid	= clm_core::$load->request_int('sid'.$p);
            $oname	= clm_core::$load->request_string('woname'.$p);
            $nname	= clm_core::$load->request_string('wnname'.$p);
            if ($oname != '' and $nname != '' and $oname != $nname) {
                // Update der Recode-Tabelle
                $query	= "INSERT INTO #__clm_player_decode"
                    . " ( `sid`, `source`, `oname`, `nname`, `verein`) "
                    . " VALUES (".$sid.",'pgn_import', '".$oname."', '".$nname."', '".$verein."' )"
                    . " ON DUPLICATE KEY UPDATE nname = '".$nname."', verein = '".$verein."'";
                clm_core::$db->query($query);
            }
            $oname	= clm_core::$load->request_string('boname'.$p);
            $nname	= clm_core::$load->request_string('bnname'.$p);
            if ($oname != '' and $nname != '' and $oname != $nname) {
                // Update der Recode-Tabelle
                $query	= "INSERT INTO #__clm_player_decode"
                    . " ( `sid`, `source`, `oname`, `nname`, `verein`) "
                    . " VALUES (".$sid.",'pgn_import', '".$oname."', '".$nname."', '".$verein."' )"
                    . " ON DUPLICATE KEY UPDATE nname = '".$nname."', verein = '".$verein."'";
                clm_core::$db->query($query);
            }
        }
        return true;
    }
}
