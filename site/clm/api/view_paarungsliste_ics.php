<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_view_paarungsliste_ics($liga)
{

    $out = clm_core::$api->db_paarungsliste($liga);

    if (!$out[0]) {
        if ($out[1] == "e_scheduleError") {
            $out = clm_core::$api->db_report_overview();
            $fix = clm_core::$load->load_view("report_overview", array($out[2]));
            return array(true, $out[1], '<div class="clm">'.$fix[1].'</div>');
        } elseif (count($out) == 3) {
            return array(false, $out[1], $out[2]);
        } else {
            return array(false, $out[1]);
        }
    }
    $fix = clm_core::$load->load_view("paarungsliste_ics", array($out[2]));
    return;

}
