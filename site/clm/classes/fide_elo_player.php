<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/

/**
 * A player whose FIDE Rating should be calculated.
 *
 * @author Oswald Jaskolla <clm@osjas.de>
 *
 * @property-read int $ID The identifier of the player.
 * @property-read int $Age The age of the player at the end of the year of the
 *  rating period that the game was played
 * @property-read int|null $Elo The FIDE rating number of the player, null if
 *  the player has no rating number.
 * @property-read int $K The development coefficient of the player.
 */
class clm_class_fide_elo_player {
    /**
     * @param int $id The identifier of the player
     * @param int $age The age of the player at the end of the year of the
     *  rating period that the game was played
     * @param int|null $elo The FIDE rating of the playere, null is the player
     *  has no rating number
     * @param int|null $k The development coefficient of the player. If this
     *  is null, K will be estimated based on Age and Elo
     */
    public function __construct($id, $age, $elo = null, $k=null) {
        assert(is_int($id));
        assert(is_int($age));
        assert($elo === null || is_int($elo));
        assert($k === null || is_int($k));

        $this->id = $id;
        $this->age = $age;
        $this->elo = $elo;
        $this->k = $k;

        if ($this->k === null) {
            if ($this->age <= 18 && $this->elo < 2300) {
                $this->k = 40;
            } else if ($this->elo === null) {
                $this->k = 40;
            } else if ($this->elo < 2400) {
                $this->k = 20;
            } else {
                $this->k = 10;
            }
        }
    }

    /**
     * @ignore Documented at the top of the class
     */
    public function __get($property) {
		if ($property == 'Fide_Kf') $property = 'K';
        $property = strtolower($property);
        assert(property_exists($this, $property));

        return $this->$property;
    }

    private $id;
    private $age;
    private $elo;
    private $k;
}