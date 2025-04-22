<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Aufbau eines xls Links im CLM
function clm_function_create_link_ics($view, $title, $params = array())
{

    // open div
    //		$string = '<div class="ics">';
    $string = '  ';

    // imageTag zusammensetzen
    $imageTag = '<img src="'.clm_core::$load->gen_image_url("table/ics_button").'" width="16" height="19" title="'.$title.'" alt="ICS" class="CLMTooltip" />';

    // Format ergänzen
    $params['format'] = 'ics';

    $string .= clm_core::$load->create_link($imageTag, $view, $params);

    // close div
    //		$string .= '</div>';
    $string .= '  ';

    return $string;

}
