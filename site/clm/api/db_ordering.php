<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_ordering($ids, $numbers, $table)
{

    if (count($ids) != count($numbers)) {
        return array(false,"e_paramsNotCorrect");
    }

    if (!in_array($table, array("turniere","liga"))) {
        return array(false,"e_tableIsNotAllowed");
    }

    if ($table == "turniere") {
        if (clm_core::$access->access('BE_tournament_general') === false) {
            return array(false,"e_noRights");
        }
    } elseif ($table == "liga") {
        if (clm_core::$access->access('BE_teamtournament_general') === false) {
            return array(false,"e_noRights");
        }
    }

    for ($i = 0;$i < count($numbers);$i++) {
        if (!clm_core::$db->$table->get($ids[$i])->isNew()) {
            //			clm_core::$db->$table->get($ids[$i])->ordering = $numbers[$i];
            $query = "UPDATE #__clm_".$table." SET ordering=".$numbers[$i]." WHERE id = ".$ids[$i];
            clm_core::$db->query($query);
        }
    }
    return array(true,"");
}
