<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/

/**
 * Calculation of the FIDE rating based on the FIDE Rating Regulations
 * from March 1st 2024.
 *
 * @author Oswald Jaskolla <clm@osjas.de>
 * @see https://handbook.fide.com/chapter/B022024
 */
class clm_class_fide_elo_calculator {
    /**
     * Adds a player whose rating should be calculated.
     *
     * @param int $id The identifier of the player
     * @param int $age The age of the player at the end of the year that the
     *  game was played
     * @param int|null $elo The FIDE rating of the player, `null` if the player
     *  has no rating number
     * @param int|null $k The development coefficient of the player. If this
     *  is null, the development coefficient will be estimated based on age and
     *  rating.
     */
    public function addPlayer($id, $age, $elo, $k) {
        assert(is_int($id));
        assert(is_int($age));
        assert($age === null || is_int($age));
        assert($k === null || is_int($k));

        $this->ensure_player($id);
        $this->data[$id]->player = new clm_class_fide_elo_player($id, $age, $elo, $k);
    }

    /**
     * Adds a game that should be used to update ratings.
     *
     * @param int $id_white The identifier of the player playing white
     * @param int $id_black The identifier of the player playing black
     * @param clm_class_fide_elo_game_result $result The result of the game
     */
    public function addGame($id_white, $id_black, $result) {
        assert(is_int($id_white));
        assert(is_int($id_black));
//      assert($result instanceof result);
        assert($result instanceof clm_class_fide_elo_game_result);

        $this->ensure_player($id_white);
        $this->ensure_player($id_black);
        $this->data[$id_white]->games[] = new clm_class_fide_elo_game($id_white, $id_black, $result);
        $this->data[$id_black]->games[] = new clm_class_fide_elo_game($id_black, $id_white, $result->flip());
    }

    /**
     * Calculates the new rating for players that have been added.
     *
     * Games against players that have not been added using addPlayer() are
     * not included in the calculation because their rating is not known.
     *
     * @return array<int, clm_class_fide_elo_player> Players with updated ratings
     */
    public function calculate() {
        $return = [];
        foreach ($this->data as $record) {
            if ($record->player !== null) {
                if ($record->player->Elo === null) {
                    $player = $this->calculateInitialRating($record);
                } else {
                    $player = $this->updateEstablishedRating($record);
                }
                if ($player) {
                    $return[$record->player->Id] = $player;
                } else {
                    $return[$record->player->Id] = clone $record->player;
                }
            }
        }
        return $return;
    }

    /**
     * Initialize the entry in the `$this->data` array if this hasn't already
     * been done.
     *
     * @param int $id The identifier of the player
     */
    private function ensure_player($id) {
        if (!array_key_exists($id, $this->data)) {
            $this->data[$id] = (object)[
                'player' => null,
                'games' => []
            ];
        }
    }

    /**
     * Calculate rating for an unrated player.
     *
     * @param stdclass $record
     * @return clm_class_fide_elo_player|null The player with an initial rating
     *  or `null` if the player did not recieve a rating.
     */
    private function calculateInitialRating($record) {
        /** @var clm_class_fide_elo_player $player */
        $player = $record->player;
        $n = 0;
        $ra = 0;
        $score = 0;
        /** @var clm_class_fide_elo_game $game */
        foreach ($record->games as $game) {
            /** @var clm_class_fide_elo_player $op */
            $op = $this->data[$game->IdBlack]->player;
            if ($op === null || $op->Elo == null) {
                continue;
            }
            ++$n;
            $ra += $op->Elo;
            $score += $game->Result->White;
        }

        // 7.1.4 & 8.2.1
        if ($n < 5 || $score == 0) {
            return null;
        }

        // 8.2.2
        $ra += 2*1800;
        $n += 2;
        $score += 2;

        $ra /= $n;
        $score /= 2;
        $p = $score / $n;

        // 8.2.3
        $ru = min(2200, intval($ra + 0.5 + $this->d($p)));

        // 7.1.4
        if ($ru < 1400) {
            return new clm_class_fide_elo_player(
                $player->Id, $player->Age, null, 40
            );
        }

        // 8.3.3
        if ($n < 30) {
            $k = 40;
        } else if ($ru < 2400) {
            $k = 20;
        } else {
            $k = 10;
        }
        if ($player->Age <= 18 && $ru < 2300) {
            $k = 40;
        }
        if ($k * $n > 700) {
            $k = intval(700/$n);
        }

        return new clm_class_fide_elo_player(
            $player->Id, $player->Age, $ru, $k
        );
    }

    /**
     * Calculate rating for a player with an established rating.
     *
     * @param stdclass $record
     * @return clm_class_fide_elo_player|null The player with an updated rating
     *  or `null` if the player did not play against rated opponents
     * @todo New value for K cannot be determined without knowing the total
     *  number of rated games.
     */
    private function updateEstablishedRating($record) {
        /** @var clm_class_fide_elo_player $player */
        $player = $record->player;
        $n = 0;
        $sdr = 0;
        /** @var clm_class_fide_elo_game $game */
        foreach ($record->games as $game) {
            /** @var clm_class_fide_elo_player $op */
            $op = $this->data[$game->IdBlack]->player;
            if ($op === null || $op->Elo === null) {
                continue;
            }
            ++$n;
            $d = min($player->Elo - $op->Elo, 400);  // 8.3.1
            $sdr += $game->Result->White/2 - $this->p($d);  // 8.3.2 a), b), c)
        }

        if ($n === 0) {
            return null;
        }

        if ($player->Fide_Kf * $n > 700) { // 8.3.3
            $k = intval(700/$n);
        } else {
            $k = $player->Fide_Kf;
        }

        $ru = intval($player->Elo + 0.5 + $k * $sdr);  // 8.3.2 d)

        if ($ru < 1400) {  // 7.2.1
            $ru = null;
            $k_new = null;
        } else if ($ru >= 2400) {  // 8.3.3
            $k_new = 10;
        } else if ($player->Age <= 18) {
            $k_new = 40;
        } else {
            $k_new = 20;
        }

        return new clm_class_fide_elo_player(
            $player->Id, $player->Age, $ru, $k_new
        );
    }

    /**
     * @param int $dp The rating difference.
     * @return double The expected result.
     */
    private function p($dp) {
        if ($dp < 0) {
            $invert = true;
            $dp *= -1;
        } else {
            $invert = false;
        }

        for ($i = 1; $i < count(self::$PTab); ++$i) {
            if ($dp < self::$PTab[$i]) {
                break;
            }
        }

        $p = (50 + $i - 1)/100;

        return $invert ? 1-$p : $p;
    }

    /**
     * @param double $p The expected result
     * @return int The rating difference.
     */
    private function d($p) {
        if ($p < 0.5) {
            $p = 1-$p;
            $sign = -1;
        } else {
            $sign = 1;
        }
        $i = round($p * 100) - 50;

        return $sign * self::$DTab[$i];
    }

    private static $PTab = [
        0, 4, 11, 18, 26, 33, 40, 47, 54, 62,
        69, 77, 84, 92, 99, 107, 114, 122, 130, 138,
        146, 154, 163, 171, 180, 189, 198, 207, 216, 226,
        236, 246, 257, 268, 279, 291, 303, 316, 329, 345,
        358, 375, 392, 412, 433, 457, 485, 518, 560, 620,
        736
    ];

    private static $DTab = [
        0,   7,  14,  21,  29,  36,  43,  50,  57,  65,
        72,  80,  87,  95, 102, 110, 117, 125, 133, 141,
        149, 158, 166, 175, 184, 193, 202, 211, 220, 230,
        240, 251, 262, 273, 284, 296, 309, 322, 336, 351,
        366, 383, 401, 422, 444, 470, 501, 538, 589, 677,
        800
    ];

    private $data = [];
}