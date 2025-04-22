<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Input: Saison, Verein, Mitgliedsnummer, Sperrkennzeichen
// Output: Setzen des Sperrkennzeichen in der Mitgliederverwaltung sowie allen Melde- und rabglisten

function clm_api_db_syn_player_block($sid, $zps, $mglnr, $block)
{
    $sid = clm_core::$load->make_valid($sid, 0, -1);
    $zps = clm_core::$load->make_valid($zps, 8, '');
    $mglnr = clm_core::$load->make_valid($mglnr, 0, -1);
    $block = clm_core::$load->make_valid($block, 9, 0, array(0, 1));

    $query = "SELECT * "
            ." FROM  #__clm_dwz_spieler "
            ." WHERE sid = ".$sid
            ." AND ZPS = '".$zps."'"
            ." AND Mgl_Nr = ".$mglnr
    ;
    $player = clm_core::$db->loadObjectList($query);
    if (is_null($player) or count($player) != 1) {
        return array(false,'e_noPlayer');
    }

    if ($player[0]->gesperrt == $block) {
        return array(true,'m_noUpdate');
    }

    $anz = array();

    // Update Mitgliederverwaltung
    $query	= "UPDATE #__clm_dwz_spieler "
        ." SET gesperrt = ".$block
        ." WHERE sid = ".$sid
        ." AND ZPS = '".$zps."' "
        ." AND Mgl_Nr = ".$mglnr;
    if (!clm_core::$db->query($query)) {
        echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
    }
    $anz[0] = clm_core::$db->affected_rows();

    // Update Meldelisten
    $query	= "UPDATE #__clm_meldeliste_spieler "
        ." SET gesperrt = ".$block
        ." WHERE sid = ".$sid
        ." AND zps = '".$zps."' "
        ." AND mgl_nr = ".$mglnr;
    if (!clm_core::$db->query($query)) {
        echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
    }
    $anz[1] = clm_core::$db->affected_rows();

    // Update Meldelisten
    $query	= "UPDATE #__clm_rangliste_spieler "
        ." SET gesperrt = ".$block
        ." WHERE sid = ".$sid
        ." AND ZPSmgl = '".$zps."' "
        ." AND Mgl_Nr = ".$mglnr;
    if (!clm_core::$db->query($query)) {
        echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
    }
    $anz[2] = clm_core::$db->affected_rows();
    if ($block == 1) {
        $msg = $player[0]->Spielername.' wurde gesperrt';
    } else {
        $msg = $player[0]->Spielername.' wurde entsperrt';
    }

    return array(true, $msg , $anz);
}
