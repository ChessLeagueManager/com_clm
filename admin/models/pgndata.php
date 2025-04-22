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
defined('_JEXEC') or die('Restricted access');

class CLMModelPGNdata extends JModelLegacy
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
        //echo "<br>1liga: $liga  tid: $tid  tkz: $tkz"; //die();
        $_POST['tkz'] = $tkz;
        $_POST['tid'] = $tid;
        $tkz = clm_core::$load->request_string('tkz', '0');
        $tid = clm_core::$load->request_string('tid', '0');
        //echo "<br>2liga: $liga  tid: $tid  tkz: $tkz"; //die();
        //echo "<br>ttid: $tid  tkz: $tkz"; //die();
        if ($tkz == 't') { 		// Teamwettbewerb
            $query = "SELECT * FROM #__clm_liga "
                    .' WHERE id = '.$tid;
            //echo "<br>query: "; var_dump($query); //die();
        } elseif ($tkz == 's') { 		// Teamwettbewerb
            $query = "SELECT * FROM #__clm_turniere "
                    .' WHERE id = '.$tid;
            //echo "<br>query: "; var_dump($query); //die();
        }
        $turnier	= clm_core::$db->loadObjectList($query);
        //echo "<br>turnier: "; var_dump($turnier); //die();
        return $turnier;
    }

    public function getMainPGN()
    {

        $task = clm_core::$load->request_string('task', '');
        $stask = clm_core::$load->request_string('stask', '');
        //echo "<br>md-main-pgndata: task $task  stask $stask "; //die();

        $liga = clm_core::$load->request_string('liga', '');
        $liga_arr = explode('.', $liga, 2);
        $tkz = $liga_arr[0];
        $tid = $liga_arr[1];
        //echo "<br>main-liga: $liga  tid: $tid  tkz: $tkz"; //die();

        // offene Notationen auslesen
        $query = "SELECT * FROM #__clm_pgn "
            .' WHERE tkz = "'.$tkz.'"'
            .' AND tid = '.$tid
            .' AND runde = 0 ';
        //echo "<br>query: "; var_dump($query); //die();
        $gameslist	= clm_core::$db->loadObjectList($query);
        //echo "<br>md-count-gameslist:".count($gameslist); var_dump($gameslist); //die();
        $zz = 0;
        $pgn_arr = array();
        foreach ($gameslist as $gl) {
            //echo "<br>pgnnr_arr: "; var_dump($pgnnr_arr); //die();
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
            //echo "<br>return:"; var_dump($return_arr);
        }
        //echo "<br><br>return:"; var_dump($pgn_arr);

        return $pgn_arr;
    }

    public function store()
    {

        $tid 	= clm_core::$load->request_string('tid', '0');
        $tkz 	= clm_core::$load->request_string('tkz', '0');
        $pgn_count 	= clm_core::$load->request_int('pgn_count', -1);
        //echo "<br>store  tid $tid  tkz $tkz  pgn_count $pgn_count";
        // DB-Zugriff
        for ($p = 0; $p < $pgn_count ; $p++) {

            if (clm_core::$load->request_int('runde'.$p, -1) == (-1)) {
                continue;
            }
            if (clm_core::$load->request_int('dg'.$p, 0) == 0) {
                continue;
            }
            if (clm_core::$load->request_int('runde'.$p, 0) == 0) {
                continue;
            }
            if ($tkz == 't') {
                if (clm_core::$load->request_int('paar'.$p, 0) == 0) {
                    continue;
                }
            }
            if (clm_core::$load->request_int('brett'.$p, 0) == 0) {
                continue;
            }
            $query = "DELETE FROM #__clm_pgn "
                .' WHERE tkz = "'.$tkz.'"'
                .' AND tid = '.$tid
                .' AND dg = '.clm_escape(clm_core::$load->request_int('dg'.$p))
                .' AND runde = '.clm_escape(clm_core::$load->request_int('runde'.$p));
            if ($tkz == 't') {
                $query .= ' AND paar = '.clm_escape(clm_core::$load->request_int('paar'.$p));
            }
            $query .= ' AND brett = '.clm_escape(clm_core::$load->request_int('brett'.$p));
            //echo "<br>store-delete_query: "; var_dump($query); //die();
            clm_core::$db->query($query);
            $query = 'UPDATE #__clm_pgn '
                .' SET dg = '.clm_escape(clm_core::$load->request_int('dg'.$p))
                .' , runde = '.clm_escape(clm_core::$load->request_int('runde'.$p));
            if ($tkz == 't') {
                $query .= ' , paar = '.clm_escape(clm_core::$load->request_int('paar'.$p));
            }
            $query .= ' , brett = '.clm_escape(clm_core::$load->request_int('brett'.$p))
//				." , text = '".clm_escape(clm_core::$load->request_string('text'.$p))."'"
                ." , text = '".clm_escape($_POST['text'.$p])."'"
                ." , error = ''"
                .' WHERE id = '.clm_escape(clm_core::$load->request_int('pgnnr'.$p));
            //echo "<br>in_query: "; var_dump($query); //die();
            clm_core::$db->query($query);
            //echo "<br>e: ".mysqli_errno.": ".mysqli_error;
            if ($tkz == 't') {
                $query = 'UPDATE #__clm_rnd_spl '
                  .' SET pgnnr = '.clm_escape(clm_core::$load->request_int('pgnnr'.$p))
                  .' WHERE lid = '.$tid
                  .' AND dg = '.clm_escape(clm_core::$load->request_int('dg'.$p))
                  .' AND runde = '.clm_escape(clm_core::$load->request_int('runde'.$p))
                  .' AND paar = '.clm_escape(clm_core::$load->request_int('paar'.$p))
                  .' AND brett = '.clm_escape(clm_core::$load->request_int('brett'.$p));
            } elseif ($tkz == 's') {
                $query = 'UPDATE #__clm_turniere_rnd_spl '
                  .' SET pgn = '.clm_escape(clm_core::$load->request_int('pgnnr'.$p))
                  .' WHERE turnier = '.$tid
                  .' AND dg = '.clm_escape(clm_core::$load->request_int('dg'.$p))
                  .' AND runde = '.clm_escape(clm_core::$load->request_int('runde'.$p))
                  .' AND brett = '.clm_escape(clm_core::$load->request_int('brett'.$p));
            }
            //echo "<br>query: "; var_dump($query); //die();
            clm_core::$db->query($query);
        }
        return true;
    }
}
