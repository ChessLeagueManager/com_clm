<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/

/**
 * Provides database access for unofficial FIDE rating calculation of team
 * tournaments.
 *
 * @author Oswald Jaskolla <clm@osjas.de>
 */
class clm_class_fide_elo_db_gateway_teams
    extends clm_class_fide_elo_db_gateway_tournament
{
    /**
     * {@inheritDoc}
     * @see clm_class_fide_elo_db_gateway_tournament::__construct
     */
    public function __construct($id) {
        parent::__construct($id);

        $config = clm_core::$db->config();
        $this->countryversion = $config->countryversion;

        $stmt = $this->executeWithId(
            "SELECT params FROM #__clm_liga WHERE id = ?"
        );

        $params = null;
        $stmt->bind_result($params);
        if (!$stmt->fetch()) {
            throw new RuntimeException("e_genFIDERatingDbError");
        }
        $stmt->free_result();

        $params = new clm_class_params($params);
        $date = $params->get('dwz_date', '1970-01-01');
        if ($date == '0000-00-00' || $date == '1970-01-01') {
            $this->old = true;
        } else {
            $this->old = false;
        }
    }

    /**
     * {@inheritDoc}
     * @see clm_class_fide_elo_db_gateway_tournament::getYear()
     */
    public function getYear() {
        $year = 0;

        $sql = "
            SELECT SUBSTRING(MAX(zeit), 1, 4) AS year
            FROM #__clm_rnd_man
            WHERE lid = ?
            GROUP BY lid
        ";

        $stmt = $this->executeWithId($sql);
        $stmt->bind_result($year);
        if (!$stmt->fetch()) {
            throw new RuntimeException("e_genFIDERatingDbError");
        }
        $stmt->free_result();

        return $year;
    }

    /**
     * {@inheritDoc}
     * @see clm_class_fide_elo_db_gateway_tournament::getPlayers()
     */
    public function getPlayers() {
        if ($this->countryversion == "de") {
            $join_condition =
                "p.mgl_nr = d.Mgl_Nr AND p.sid = d.sid AND p.zps = d.ZPS";
        } else {
            $join_condition =
                "p.PKZ = d.PKZ AND p.sid = d.sid AND p.zps = d.ZPS";
        }

        if ($this->old) {
            $elofield = "d.FIDE_Elo";
        } else {
            $elofield = "p.FIDEelo";
        }

        $sql = "
            SELECT
                p.id AS id, d.Geburtsjahr AS yob, $elofield AS elo
            FROM #__clm_meldeliste_spieler AS p
            LEFT JOIN #__clm_dwz_spieler AS d ON $join_condition
            WHERE lid = ?
        ";

        $stmt = $this->executeWithId($sql);
        $result = $stmt->get_result();
        if (!$result) {
            throw new RuntimeException("e_genFIDERatingDbError");
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     * @see clm_class_fide_elo_db_gateway_tournament::getGames()
     */
    public function getGames() {
        if ($this->countryversion == "de") {
            $join_condition_white = "g.spieler = w.mgl_nr";
            $join_condition_black = "g.gegner = b.mgl_nr";
        } else {
            $join_condition_white = "g.PKZ = w.PKZ";
            $join_condition_black = "g.gPKZ = b.PKZ";
        }

        $sql = "
            SELECT w.id AS id_white, b.id AS id_black, g.ergebnis AS result
            FROM #__clm_rnd_spl AS g
            INNER JOIN #__clm_meldeliste_spieler AS w
                ON $join_condition_white AND g.zps = w.zps AND g.lid = w.lid
            INNER JOIN #__clm_meldeliste_spieler AS b
                ON $join_condition_black AND g.gzps = b.zps AND g.lid = b.lid
            WHERE g.heim = 1 AND g.lid = ?";

        $stmt = $this->executeWithId($sql);
        $result = $stmt->get_result();
        if (!$result) {
            throw new RuntimeException("e_genFIDERatingDbError");
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     * @see clm_class_fide_elo_db_gateway_tournament::updatePlayer()
     */
    public function updatePlayer($id, $elo, $k) {
        assert(is_int($id));
        assert($elo === null || is_int($elo));
        assert($k === null || is_int($k));

        $sql = "
            UPDATE #__clm_meldeliste_spieler
            SET inofFIDEelo = ?, Fide_Kf = ?
            WHERE id = ?
        ";

        $stmt = clm_core::$db->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException("e_genFIDERatingDbError");
        }

        $success = $stmt->bind_param("iii", $elo, $k, $id);
        if (!$success) {
            throw new RuntimeException("e_genFIDERatingDbError");
        }

        $success = $stmt->execute();
        if (!$success) {
            throw new RuntimeException("e_genFIDERatingDbError");
        }
    }

    /**
     * {@inheritDoc}
     * @see clm_class_fide_elo_db_gateway_tournament::deleteRatings()
     */
    public function deleteRatings() {
        $sql = "
            UPDATE #__clm_meldeliste_spieler
            SET inofFIDEelo = null, Fide_Kf = null
            WHERE lid = ?
        ";

        $stmt = $this->executeWithId($sql);
        $stmt->free_result();

        return array(true, "m_delFIDERatingSuccess");
    }

    /**
     * Execute a query, binding the tournament ID to the query parameter.
     *
     * @param string $sql The query
     * @return mysqli_stmt The executed statement.
     * @throws RuntimeException
     */
    private function executeWithId($sql) {
        $stmt = clm_core::$db->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException("e_genFIDERatingDbError");
        }
		$success = @$stmt->bind_param("i", $this->ID);    // ausblenden von Notice
        if (!$success) {
            throw new RuntimeException("e_genFIDERatingDbError");
        }
        $success = $stmt->execute();
        if (!$success) {
            throw new RuntimeException("e_genFIDERatingDbError");
        }
        return $stmt;
    }

    /**
     * @var string
     */
    private $countryversion;

    /**
     * @var bool Whether the ratings in the registration list are outdated and
     *  must be fetched from the player table (true) or not (false)
     */
    private $old;
}