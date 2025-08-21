<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/

/**
 * Games that are used to update the FIDE rating of a player.
 *
 * @author Oswald Jaskolla <clm@osjas.de>
 *
 * @property-read int $IdWhite The identifier of the player playing white
 * @property-read int $IdBlack The identifier of the player playing black
 * @property-read clm_class_fide_elo_game_result $Result The result of the game
 */
class clm_class_fide_elo_game {
    /**
     * @param int $id_white The identifier of the player playing white
     * @param int $id_black The identifier of the player playing black
     * @param clm_class_fide_elo_game_result $result The result of the game
     */
    public function __construct($id_white, $id_black, $result) {
        assert(is_int($id_white));
        assert(is_int($id_black));
//      assert(is_instance($result instanceof clm_class_fide_elo_game_result));
        assert($result instanceof clm_class_fide_elo_game_result);

        $this->idwhite = $id_white;
        $this->idblack = $id_black;
        $this->result = $result;
    }

    /**
     * @ignore Documented at the top of the class
     */
    public function __get($property) {
        $property = strtolower($property);
        assert(property_exists($this, $property));

        return $this->$property;
    }

    private $idwhite;
    private $idblack;
    private $result;
}