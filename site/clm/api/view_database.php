<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2020 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_view_database()
{
    $config = clm_core::$db->config();
    $sid = clm_core::$access->getSeason();
    $rating_type = clm_core::$db->saison->get($sid)->rating_type;

    if ($config->countryversion == "de") { // direct update only for Germany
        $lang1 = clm_core::$lang->dewis_import;
        $fix1 = clm_core::$load->load_view("dewis_import", array());
        $online_import = clm_core::$load->load_view("spoiler", array($lang1->title,$fix1[1])); // array dereferencing fix php 5.3
        $online_import = $online_import[1]; // array dereferencing fix php 5.3
    } elseif ($config->countryversion == "en" and $rating_type == 1) { // direct update for England since 01.07.2020
        $lang1 = clm_core::$lang->ecfv2_import;
        $fix1 = clm_core::$load->load_view("ecfv2_import", array());
        $online_import = clm_core::$load->load_view("spoiler", array($lang1->title,$fix1[1])); // array dereferencing fix php 5.3
        $online_import = $online_import[1]; // array dereferencing fix php 5.3
    } else {
        $online_import = "";
    }
    if ($config->countryversion == "de") { // for Germany
        $lang2 = clm_core::$lang->dsb_import;
        $fix2 = clm_core::$load->load_view("dsb_import", array());
        $country_import = clm_core::$load->load_view("spoiler", array($lang2->title,$fix2[1])); // array dereferencing fix php 5.3
        $country_import = $country_import[1]; // array dereferencing fix php 5.3
    } elseif ($config->countryversion == "en" and $rating_type == 0) { // for Great Britain
        $lang2 = clm_core::$lang->ecf_import;
        $fix2 = clm_core::$load->load_view("ecf_import", array());
        $country_import = clm_core::$load->load_view("spoiler", array($lang2->title,$fix2[1])); // array dereferencing fix php 5.3
        $country_import = $country_import[1]; // array dereferencing fix php 5.3
    } else {
        $country_import = "";
    }

    $output = '<div class="clm"><div class="clm_api_view_database">'.$online_import.$country_import.'</div></div>';


    return array(true, "",$output);
}
