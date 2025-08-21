<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/

/**
 * Base class for classes that provide database access for unofficial
 * FIDE rating calculation.
 *
 * @author Oswald Jaskolla <clm@osjas.de>
 *
 * @property-read int $ID Identifier of the tournament
 */
abstract class clm_class_fide_elo_db_gateway_tournament {
    /**
     * @param int $id Identifier of the tournament
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * @return int The year of the last game of the tournament.
     * @throws RuntimeException
     */
    abstract public function getYear();

    /**
     * @return Traversable Something that can be iterated over using `foreach`
     *  yielding arrays with keys 'id', 'elo', 'k' and 'yob'.
     * @throws RuntimeException
     */
    abstract public function getPlayers();

    /**
     * @return Traversable Something that can be iterated over using `foreach`
     *  yielding arrays with keys 'id_white', 'id_black' and 'result'.
     * @throws RuntimeException
     */
    abstract public function getGames();

    /**
     * Updates the unofficial FIDE rating of a player.
     *
     * @param int $id Identifier of the player
     * @param int $elo New FIDE rating of the player
     * @param int $k Development coefficient of the player
     * @throws RuntimeException
     */
    abstract public function updatePlayer($id, $elo, $k);

    /**
     * Deletes unofficial FIDE ratings of the tournament.
     *
     * @throws RuntimeException
     */
    abstract public function deleteRatings();

    /**
     * @ignore Documented at the top of the class
     */
    public function __get($property) {
        $property = strtolower($property);

        if ($property === "id") {
            return $this->id;
        }
    }

    /**
     * @var int Identifier of the tournament
     */
    private $id;
}