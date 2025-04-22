<?php

function clm_api_view_report_sl($lid, $rnd, $dg, $paar, $apaar)
{
    $out = clm_core::$api->db_report_sl($lid, $rnd, $dg, $paar, $apaar);
    if (!$out[0]) {
        if ($out[1] == "e_reportError") {
            $out = clm_core::$api->db_report_overview();
            $fix = clm_core::$load->load_view("report_overview", array($out[2]));
            return array(true, $out[1], '<div class="clm">'.$fix[1].'</div>');
        } elseif (count($out) == 3) {
            return array(false, $out[1], $out[2]);
        } else {
            return array(false, $out[1]);
        }
    }
    $fix = clm_core::$load->load_view("report_sl", array($out[2]));
    return array(true, $out[1], '<div class="clm">'.$fix[1].'</div>');
}
