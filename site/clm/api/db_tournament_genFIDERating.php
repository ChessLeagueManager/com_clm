<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/

use clm_class_fide_elo_game_result as result;

/**
 * Calculate FIDE ratings of a tournament.
 *
 * @param int $id Identifier of the tournament.
 * @param boolean $group Whether this is a team tournament (true) or not (false).
 * @return array
 * @author Oswald Jaskolla <clm@osjas.de>
 */
function clm_api_db_tournament_genFIDERating($id, $group=true) {
    assert(is_int($id), __FUNCTION__ . ' expects integer as parameter $id.');
    assert(is_bool($group), __FUNCTION__ . ' expects boolean as parameter $group.');

    if ($group) {
        $gw = new clm_class_fide_elo_db_gateway_teams($id);
    } else {
        $gw = new clm_class_fide_elo_db_gateway_individual($id);
    }

    $calculator = new clm_class_fide_elo_calculator();

    try {
        $year = $gw->getYear();

        foreach ($gw->getPlayers() as $p) {
            $calculator->addPlayer($p['id'], $year - $p['yob'], $p['elo'] > 0 ? $p['elo'] : null, null);
        }

        foreach ($gw->getGames() as $g) {
            $result = clm_core::$load->gen_result($g['result'],0);
            if (is_array($result) && array_sum($result) > 0) {
                if ($result[0] == 0) {
                    if ($result[1] == 1) {
                        $r = result::LossWin();
                    } else {
                        $r = result::LossDraw();
                    }
                } else if ($result[1] == 0) {
                    if ($result[0] == 1) {
                        $r = result::WinLoss();
                    } else {
                        $r = result::DrawLoss();
                    }
					                } else {
                    $r = result::DrawDraw();
                }
                $calculator->addGame($g['id_white'], $g['id_black'], $r);
            }
        }

        $calculated = $calculator->calculate();

        foreach ($calculated as $player) {
            $gw->updatePlayer($player->ID, $player->Elo, $player->Fide_Kf);
       }

        return array(true, "m_calculateFIDERatingSuccess");
    } catch (RuntimeException $e) {
        return array(false, $e->getMessage());
    }
}