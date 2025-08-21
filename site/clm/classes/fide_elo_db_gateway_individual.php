<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/

/**
 * Provides database access for unofficial FIDE rating calculation of
 * individual tournaments.
 *
 * @author Oswald Jaskolla <clm@osjas.de>
 */
class clm_class_fide_elo_db_gateway_individual
    extends clm_class_fide_elo_db_gateway_tournament
{
    /**
     * {@inheritDoc}
     * @see clm_class_fide_elo_db_gateway_tournament::getYear()
     */
    public function getYear() {
        $year = 0;

        $sql = "
            SELECT MAX(YEAR(datum)) AS year
            FROM #__clm_turniere_rnd_termine
            WHERE turnier = ?
            GROUP BY turnier
        ";
        $stmt = $this->executeWithId($sql);
        $stmt->bind_result($year);
        if (!$stmt->fetch()) {
            throw new RuntimeException("e_genFIDERatingDbErrorgetYear");
        }
        $stmt->free_result();

        return $year;
    }

    /**
     * {@inheritDoc}
     * @see clm_class_fide_elo_db_gateway_tournament::getPlayers()
     */
    public function getPlayers() {
        $sql = "
            SELECT p.id AS id, p.birthYear AS yob, p.FIDEelo AS elo
            FROM #__clm_turniere_tlnr AS p
            WHERE p.turnier = ?;
        ";

        $stmt = $this->executeWithId($sql);
        $result = $stmt->get_result();
        if (!$result) {
            throw new RuntimeException("e_genFIDERatingDbErrorgetPlayers");
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     * @see clm_class_fide_elo_db_gateway_tournament::getGames()
     */
    public function getGames() {
        $sql = "
            SELECT w.id AS id_white, b.id AS id_black, g.ergebnis AS result
            FROM #__clm_turniere_rnd_spl AS g
            INNER JOIN #__clm_turniere_tlnr AS w
                ON g.spieler = w.snr AND g.turnier = w.turnier
            INNER JOIN #__clm_turniere_tlnr AS b
                ON g.gegner = b.snr AND g.turnier = b.turnier
            WHERE g.heim = 1 AND g.turnier = ?
        ";

        $stmt = $this->executeWithId($sql);
        $result = $stmt->get_result();
        if (!$result) {
            throw new RuntimeException("e_genFIDERatingDbErrorgetGames");
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
            UPDATE #__clm_turniere_tlnr
            SET inofFIDEelo = ?, Fide_Kf = ?
            WHERE id = ?
        ";

        $stmt = clm_core::$db->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException("e_genFIDERatingDbErrorupdatePlayer 1");
        }

        $success = $stmt->bind_param("iii", $elo, $k, $id);
        if (!$success) {
            throw new RuntimeException("e_genFIDERatingDbErrorupdatePlayer 2");
        }

        $success = $stmt->execute();
        if (!$success) {
            throw new RuntimeException("e_genFIDERatingDbErrorupdatePlayer 3");
        }
    }

    /**
     * {@inheritDoc}
     * @see clm_class_fide_elo_db_gateway_tournament::deleteRatings()
     */
    public function deleteRatings() {
        $sql = "
            UPDATE #__clm_turniere_tlnr
            SET inofFIDEelo = null, Fide_Kf = null
            WHERE turnier = ?
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
            throw new RuntimeException("e_genFIDERatingDbErrorexecuteWithId 1");
        }
		$success = @$stmt->bind_param("i", $this->ID);    // ausblenden von Notice
        if (!$success) {
            throw new RuntimeException("e_genFIDERatingDbErrorexecuteWithId 2");
        }
        $success = $stmt->execute();
        if (!$success) {
            throw new RuntimeException("e_genFIDERatingDbErrorexecuteWithId 3");
        }
        return $stmt;
    }
}