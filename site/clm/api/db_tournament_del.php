<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_tournament_del($id, $group = true)
{
    $id = clm_core::$load->make_valid($id, 0, -1);

    if ($group) {
        //		clm_core::$api->db_tournament_delRounds($id,true);
        $query = " DELETE FROM #__clm_liga WHERE id = ".$id;
        clm_core::$db->query($query);
        $query = "DELETE FROM #__clm_mannschaften WHERE liga = ".$id;
        clm_core::$db->query($query);
        $query = "DELETE FROM #__clm_meldeliste_spieler WHERE lid = ".$id;
        clm_core::$db->query($query);
        $query = "DELETE FROM #__clm_rnd_man " . " WHERE lid = " . $id;
        clm_core::$db->query($query);
        $query = "DELETE FROM #__clm_rnd_spl " . " WHERE lid = " . $id;
        clm_core::$db->query($query);
        $query = "DELETE FROM #__clm_runden_termine " . " WHERE liga = " . $id;
        clm_core::$db->query($query);
    } else {
        //		clm_core::$api->db_tournament_delRounds($id,false);
        $query = " DELETE FROM #__clm_turniere " . " WHERE id = " . $id;
        clm_core::$db->query($query);
        $query = "DELETE FROM #__clm_turniere_tlnr " . " WHERE turnier = " . $id;
        clm_core::$db->query($query);
        $query = "DELETE FROM #__clm_turniere_sonderranglisten " . " WHERE turnier = " . $id;
        clm_core::$db->query($query);
        $query = "DELETE FROM #__clm_turniere_rnd_spl " . " WHERE turnier = " . $id;
        clm_core::$db->query($query);
        $query = "DELETE FROM #__clm_turniere_rnd_termine " . " WHERE turnier = " . $id;
        clm_core::$db->query($query);
    }
    return array(true, "");
}
