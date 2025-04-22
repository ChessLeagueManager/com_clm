<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2025 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
/* TWZ aus Wertungsmodus, DWZ und ELO ermitteln
 * $mode = 0 --> höhere Wertung
 * $mode = 1 --> dwz vor elo
 * $mode = 2 --> elo vor dwz
 * $mode = 3 --> nur elo
 * $mode = 4 --> nur dwz
*/
function clm_function_gen_twz($mode = 0, $dwz = 0, $elo = 0)
{
    $twz = 0;
    if ($mode == 0) {
        $twz = max(array($dwz, $elo));
    } elseif ($mode == 1) {
        $twz = $dwz;
        if ($twz == 0) {
            $twz = $elo;
        }
    } elseif ($mode == 2) {
        $twz = $elo;
        if ($twz == 0) {
            $twz = $dwz;
        }
    } elseif ($mode == 3) {
        $twz = $elo;
    } elseif ($mode == 4) {
        $twz = $dwz;
    }
    return $twz;
}
