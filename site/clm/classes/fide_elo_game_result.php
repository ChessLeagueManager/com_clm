<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/

/**
 * Enumeration type for results of chess games.
 *
 * @author Oswald Jaskolla <clm@osjas.de>
 *
 * @method static clm_class_fide_elo_game_result WinLoss() A result where white wins and black loses.
 * @method static clm_class_fide_elo_game_result DrawLoss() A result where white draws and black loses.
 * @method static clm_class_fide_elo_game_result DrawDraw() A result where white and black draws.
 * @method static clm_class_fide_elo_game_result LossDraw() A result where white loses and black draws.
 * @method static clm_class_fide_elo_game_result LossWin() A result where white loses and black wins.
 * @property-read int $White The self::RESULT_ constant for the white player
 * @property-read int $Black The self::RESULT_ constant for the black player
 */
class clm_class_fide_elo_game_result {
    const RESULT_WIN = 2;
    const RESULT_DRAW = 1;
    const RESULT_LOSS = 0;

    /**
     * @ignore Documented at the top of the class
     */
    public static function __callStatic($method, $arguments) {
        assert(count($arguments) == 0);

        if (empty(self::$results)) {
            self::$results = [
                2 => new self(1,0),
                3 => new self(0,1),
                4 => new self(2,0),
                6 => new self(1,1),
                9 => new self(0,2)
            ];
        }

       switch ($method) {
            case 'WinLoss':
                return self::$results[4];
            case 'DrawLoss':
                return self::$results[2];
            case 'DrawDraw':
                return self::$results[6];
            case 'LossDraw':
                return self::$results[3];
            case 'LossWin':
			    return self::$results[9];
            default:
                throw new LogicException("No such function ".__CLASS__."::$method");
        }
    }

    /**
     * @return clm_class_fide_elo_game_result The opposite result
     */
    public function flip() {
        return self::$results[2**$this->black * 3**$this->white];
    }

    /**
     * @ignore Documented at the top of the class
     */
    public function __get($property) {
        switch ($property) {
            case 'White':
                return $this->white;
            case 'Black':
                return $this->black;
            default:
                throw new LogicException("No such property ".__CLASS__."::$property");
        }
    }

    private function __construct($white, $black) {
        $this->white = $white;
        $this->black = $black;
	}

    private $white;
    private $black;
    private static $results = [];
}