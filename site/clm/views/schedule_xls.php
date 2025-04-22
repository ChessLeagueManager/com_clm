<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_view_schedule_xls($out)
{
    $lang = clm_core::$lang->schedule;

    // Variablen initialisieren
    $paar 		= $out["paar"];
    $club 		= $out["club"];

    //CLM parameter auslesen
    $config = clm_core::$db->config();
    $countryversion = $config->countryversion;

    $schedule = array();

    $now = time();
    $first = true;

    // Terminschleife
    foreach ($paar as $paar1) {
        if ($first) {
            $first = false;
            $event = array();
            $event[1] = $lang->date;
            $event[2] = $lang->lname;
            $event[3] = $lang->dg;
            $event[4] = $lang->round;
            $event[5] = $lang->home;
            $event[6] = $lang->result;
            $event[7] = $lang->guest;
            $schedule[] = $event;
        }
        if ($paar1->rdate > "1970-01-01") {
            $prdate = clm_core::$cms->showDate($paar1->rdate, "d M Y");
            if ($paar1->rtime != "00:00:00" and $paar1->rtime != "24:00:00") {
                $prdate .= ' '.substr($paar1->rtime, 0, 5);
            }
        } else {
            $prdate = '';
        }
        $event = array();
        $event[1] = clm_core::$load->utf8decode($prdate);
        $event[2] = clm_core::$load->utf8decode($paar1->lname);
        $event[3] = $paar1->dg;
        $event[4] = $paar1->runde;
        $event[5] = clm_core::$load->utf8decode($paar1->hname);
        $event[6] = $paar1->brettpunkte." : ".$paar1->gbrettpunkte;
        $event[7] = clm_core::$load->utf8decode($paar1->gname);
        $schedule[] = $event;
    }

    //echo "<br><br>schedule:"; var_dump($schedule);
    //echo "<br><br>count:"; var_dump(count($schedule));

    // Ausgabe
    if (count($schedule) == 0) {
        return array(false, "e_ScheduleNoDataError");
    }

    $nl = "\n";
    $file_name = 'Schedule'.'_'.clm_core::$load->utf8decode($club[0]->name."_".$club[0]->season_name);
    $file_name .= '.csv';
    //	$file_name = strtr($file_name,' ','_');
    //	$file_name = strtr($file_name,"/","_");
    $file_name = clm_core::$load->make_valid($file_name, 20, 'outputfile');
    if (!file_exists('components'.DS.'com_clm'.DS.'pgn'.DS)) {
        mkdir('components'.DS.'com_clm'.DS.'pgn'.DS);
    }
    $pdatei = fopen('components'.DS.'com_clm'.DS.'pgn'.DS.$file_name, "wt");
    foreach ($schedule as $schedule1) {
        //echo "<br><br>schedule1:"; var_dump($schedule1); //die();
        $return = fputcsv($pdatei, $schedule1);
        //echo "<br><br>return:"; var_dump($return); //die();
        //break;
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

    return array(true, "m_ScheduleExportSuccess");
}
?>
 
