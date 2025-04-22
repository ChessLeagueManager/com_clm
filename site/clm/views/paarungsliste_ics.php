<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_view_paarungsliste_ics($out)
{

    $lang = clm_core::$lang->paarungsliste;

    // Variablen initialisieren
    $liga 			= $out["liga"];
    $paar 			= $out["paar"];

    //CLM parameter auslesen
    $config = clm_core::$db->config();
    $countryversion = $config->countryversion;

    $termine = array();

    $now = time();
    $runde = '0';

    // Terminschleife
    foreach ($paar as $paar1) {
        if ($paar1->rdate > "1970-01-01") {
            $prdate = clm_core::$cms->showDate($paar1->rdate, "d M Y");
            if ($paar1->rtime != "00:00:00" and $paar1->rtime != "24:00:00") {
                $prdate .= ' '.substr($paar1->rtime, 0, 5);
            }
        } else {
            continue;
        }
        $prdate = substr($paar1->rdate, 0, 4).substr($paar1->rdate, 5, 2).substr($paar1->rdate, 8, 2);
        if (!is_numeric($prdate)) {
            continue;
        }
        $prtime = substr($paar1->rtime, 0, 2).substr($paar1->rtime, 3, 2);
        if (!is_numeric($prtime)) {
            continue;
        }

        if ($runde != $paar1->runde) {
            if ($runde != 0) {
                $termine[] = $event;
            }
            $runde = $paar1->runde;
            $event = array();
            $event['DSTART'] = $prdate;
            $event['TSTART'] = $prtime.'00';
            $event['DEND'] = $prdate;
            $event['TEND'] = sprintf("%06d", $event['TSTART'] + 50000);
            $event['SUMMARY'] = 'Punktspiel '.$liga[0]->name.' '.$paar1->rname;
            $event['DESCRIPTION'] = 'Punktspiel '.$liga[0]->name.' '.$paar1->rname;
            $event['DESCRIPTION'] .= '\n'.' . '.$paar1->hname.' - '.$paar1->gname;
            $event['UID'] = 'CLM-L'.$paar1->lid.$paar1->dg.$paar1->runde;  // L - League Schedule
        } else {
            $event['DESCRIPTION'] .= '\n'.' . '.$paar1->hname.' - '.$paar1->gname;
        }
    }
    if (isset($event['DSTART'])) {
        $termine[] = $event;
    }

    $filename = 'Liga'.'_'.clm_core::$load->utf8decode($liga[0]->name."_".$liga[0]->sname);
    $filename = clm_core::$load->file_name($filename);

    $result = clm_core::$api->db_ics_export($filename, $termine);

    $location = $_SERVER['HTTP_HOST'].str_replace('&format=ics', '', $_SERVER['REQUEST_URI']);
    header('Location: http://'.$location."&fnr=".$result[1]);
    exit;

    return array(true, "m_PaarungslisteICSExportSuccess");
}
