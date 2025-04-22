<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_view_paarungsliste_xls($out)
{
    $lang = clm_core::$lang->paarungsliste;

    // Variablen initialisieren
    $liga 			= $out["liga"];
    $paar 			= $out["paar"];

    //CLM parameter auslesen
    $config = clm_core::$db->config();
    $countryversion = $config->countryversion;

    $output = array();

    $now = time();
    $first = true;

    // Terminschleife
    foreach ($paar as $paar1) {
        if ($first) {
            $first = false;
            $line = array();
            $line[1] = $lang->round;
            $line[2] = $lang->fixture;
            $line[3] = $lang->date;
            $line[4] = $lang->home;
            $line[5] = $lang->guest;
            $output[] = $line;
        }
        if ($paar1->pdate > "1970-01-01") {
            $prdate = clm_core::$cms->showDate($paar1->pdate, $lang->date_format_clm_f);
            if ($paar1->ptime != "00:00:00" and $paar1->ptime != "24:00:00") {
                $prdate .= ' '.substr($paar1->ptime, 0, 5);
            }
        } else {
            $prdate = '';
        }
        $line = array();
        $line[1] = clm_core::$load->utf8decode($paar1->rname);
        $line[2] = clm_core::$load->utf8decode($paar1->paar);
        $line[3] = clm_core::$load->utf8decode($prdate);
        $line[4] = clm_core::$load->utf8decode($paar1->hname);
        $line[5] = clm_core::$load->utf8decode($paar1->gname);
        $output[] = $line;
    }

    // Ausgabe
    if (count($output) == 0) {
        return array(false, "e_PaarungslisteNoDataError");
    }

    $nl = "\n";
    $file_name = clm_core::$load->utf8decode($lang->title.'_'.$liga[0]->name."_".$liga[0]->sname);
    $file_name .= '.csv';
    $file_name = strtr($file_name, ' ', '_');
    $file_name = strtr($file_name, "/", "_");
    if (!file_exists('components'.DS.'com_clm'.DS.'pgn'.DS)) {
        mkdir('components'.DS.'com_clm'.DS.'pgn'.DS);
    }
    $pdatei = fopen('components'.DS.'com_clm'.DS.'pgn'.DS.$file_name, "wt");
    foreach ($output as $line1) {
        $return = fputcsv($pdatei, $line1);
    }
    fclose($pdatei);
    if (file_exists('components'.DS.'com_clm'.DS.'pgn'.DS.$file_name)) {
        header('Content-Disposition: attachment; filename="'.$file_name.'"');
        header('Content-type: application/csv');
        header('Cache-Control:');
        flush();
        readfile('components'.DS.'com_clm'.DS.'pgn'.DS.$file_name);
        flush();
        exit;
    }

    return array(true, "m_PaarungslisteExportSuccess");
}
?>
 
