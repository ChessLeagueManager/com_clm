<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_view_logging()
{
    $lang = clm_core::$lang->logging;
    $table = new clm_class_table("logging", "db_logging", 8, 7, false);

    //Andere Position?
    clm_core::$load->load_js("modal");
    clm_core::$load->load_css("modal");
    //Andere Position?

    $clmAccess = clm_core::$access;
    if ($clmAccess->access('BE_logfile_delete')) {
        $table->add_button($lang->new, $lang->new_title, clm_core::$url.clm_core::$load->gen_url(array("view" => "view_logging_new")), "clm_table_form clm_button clm_button_new clm_button_big clm_button_big_new");
        $table->add_button($lang->del, $lang->del_title, json_encode(array("db_logging_del",array(""))), "clm_table_event_special clm_table_confirm clm_button clm_button_cancel clm_button_big clm_button_big_cancel");
        $table->add_button($lang->del2, $lang->del2_title, json_encode(array("db_logging_del2",array(""))), "clm_table_event_special clm_table_confirm clm_button clm_button_cancel clm_button_big clm_button_big_cancel2");
    }
    $table->add_filter_field("type", array("" => $lang->type,0 => $lang->type0,1 => $lang->type1,2 => $lang->type2,3 => $lang->type3,4 => $lang->type4));
    $fix = clm_core::$load->load_view("table", $table->result());
    return array(true,"m_tableSuccess","<div class='clm'>".$fix[1]."</div>");
}
