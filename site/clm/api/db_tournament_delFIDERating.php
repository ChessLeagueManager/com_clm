<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/

/**
 * Delete FIDE ratings of a tournament.
 *
 * @param int $id Identifier of the tournament.
 * @param boolean $group Whether this is a team tournament (true) or not (false).
 * @return array
 * @author Oswald Jaskolla <clm@osjas.de>
 */
function clm_api_db_tournament_delFIDERating($id, $group=true) {
    assert(is_int($id));
    assert(is_bool($group));

    if ($group) {
        $gw = new clm_class_fide_elo_db_gateway_teams($id);
    } else {
        $gw = new clm_class_fide_elo_db_gateway_individual($id);
    }

    try {
        $gw->deleteRatings();
        return array(true, "m_delFIDERatingSuccess");
    } catch (RuntimeException $e) {
        return array(false, $e->getMessage());
    }
}