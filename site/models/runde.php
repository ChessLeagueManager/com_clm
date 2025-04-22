<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class CLMModelRunde extends JModelLegacy
{
    public function _getCLMLiga(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', 0);
        $liga	= clm_core::$load->request_int('liga', 0);
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query = " SELECT a.*, r.datum, r.startzeit, r.enddatum, r.name as rname, r.bemerkungen as comment, r.published as pub,"
            ." s.name as saison_name,"
            ." u.name as mf_name,u.email as email FROM #__clm_liga as a"
            ." LEFT JOIN #__clm_runden_termine as r ON r.liga = a.id AND r.sid = a.sid "
            ." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
            ." LEFT JOIN #__clm_user as u ON u.jid = a.sl and u.sid = a.sid"
                ." WHERE a.id = ".$liga
                ." AND a.sid = ".$sid
                ." AND s.published = 1"
                ." ORDER BY nr "
        ;
        return $query;
    }
    public function getCLMLiga($options = array())
    {
        $query	= $this->_getCLMLiga($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMMannschaft(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', 0);
        $liga	= clm_core::$load->request_int('liga', 0);
        // TODO: Cache on the fingerprint of the arguments
        $db			= JFactory::getDBO();
        //		$id			= @$options['id'];

        $query = " SELECT * FROM #__clm_mannschaften "
            ." WHERE liga = ".$liga
            ." AND sid = ".$sid
            ." ORDER BY tln_nr "
        ;

        return $query;
    }
    public function getCLMMannschaft($options = array())
    {
        $query	= $this->_getCLMMannschaft($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMPaar(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', 0);
        $liga	= clm_core::$load->request_int('liga', 0);
        $dg 	= clm_core::$load->request_int('dg');
        $runde = clm_core::$load->request_int('runde');

        $db			= JFactory::getDBO();
        //		$id			= @$options['id'];

        $query = " SELECT a.*,g.id as gid, g.name as gname, g.tln_nr as gtln,g.published as gpublished, g.rankingpos as grank, "
            ." h.id as hid, h.name as hname, h.tln_nr as htln, h.published as hpublished, h.rankingpos as hrank, b.wertpunkte as gwertpunkte "
            ." FROM #__clm_rnd_man as a"
            ." LEFT JOIN #__clm_mannschaften AS g ON g.tln_nr = a.gegner"
            ." LEFT JOIN #__clm_mannschaften AS h ON h.tln_nr = a.tln_nr"
            ." LEFT JOIN #__clm_rnd_man AS b ON b.sid = ".$sid." AND b.lid = ".$liga." AND b.runde = ".$runde." AND b.dg = ".$dg." AND b.paar = a.paar AND b.heim = 0 "
                ." WHERE g.liga = ".$liga
                ." AND g.sid = ".$sid
                ." AND h.liga = ".$liga
                ." AND h.sid = ".$sid
                ." AND a.sid = ".$sid
                ." AND a.lid = ".$liga
                ." AND a.runde = ".$runde
                ." AND a.dg = ".$dg
                ." AND a.heim = 1 "
                ." AND (g.man_nr > 0 OR h.man_nr > 0) "
                ." ORDER BY a.paar ASC"
        ;

        return $query;
    }

    public function getCLMPaar($options = array())
    {
        $query	= $this->_getCLMPaar($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMEinzel(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', 0);
        $liga	= clm_core::$load->request_int('liga', 0);
        $dg 	= clm_core::$load->request_int('dg');
        $runde = clm_core::$load->request_int('runde');
        //CLM parameter auslesen
        $config = clm_core::$db->config();
        $countryversion = $config->countryversion;

        $db	= JFactory::getDBO();

        $query = " SELECT rang "
            ." FROM #__clm_liga as a "
            ." WHERE a.id = ".$liga
            ." AND a.sid = ".$sid
        ;
        $db->setQuery($query);
        $man	= $db->loadObjectList();
        if (isset($man[0])) {
            $rang = $man[0]->rang;
        } else {
            $rang = 0;
        }

        if ($rang == 0) {
            $query = "  SELECT a.zps, a.gzps, a.paar,a.brett,a.spieler,a.PKZ,a.gegner,a.gPKZ,a.ergebnis,a.kampflos, a.dwz_edit, a.dwz_editor, a.weiss,"
                ." a.pgnnr, pg.text,"
                ." m.name, n.name as mgname, m.sname, n.sname as smgname, d.Spielername as hname, d.DWZ as hdwz, d.FIDE_Elo as helo, d.Status as hstatus,"
                ." p.erg_text as erg_text, e.Spielername as gname, e.DWZ as gdwz, e.FIDE_Elo as gelo, e.Status as gstatus, q.erg_text as dwz_text,"
                ." k.snr as hsnr, l.snr as gsnr, k.start_dwz as hstart_dwz, l.start_dwz as gstart_dwz, k.attr as hattr, l.attr as gattr"
                ." FROM #__clm_rnd_spl as a "
                ." LEFT JOIN #__clm_rnd_man as r ON ( r.lid = a.lid AND r.runde = a.runde AND r.tln_nr = a.tln_nr AND  r.dg = a.dg) "
                ." LEFT JOIN #__clm_mannschaften AS m ON ( m.tln_nr = r.tln_nr AND m.liga = a.lid) "
                ." LEFT JOIN #__clm_mannschaften AS n ON ( n.tln_nr = r.gegner AND n.liga = a.lid) "
                ." LEFT JOIN #__clm_pgn AS pg ON ( pg.id = a.pgnnr) ";
            if ($countryversion == "de") {
                $query .= " LEFT JOIN #__clm_dwz_spieler AS d ON ( d.Mgl_Nr = a.spieler AND d.ZPS = a.zps AND d.sid = a.sid) "
                        ." LEFT JOIN #__clm_dwz_spieler AS e ON ( e.Mgl_Nr = a.gegner AND e.ZPS = a.gzps AND e.sid = a.sid) "
                        ." LEFT JOIN #__clm_meldeliste_spieler as k ON k.sid = a.sid AND k.lid = a.lid AND k.mgl_nr = a.spieler AND k.zps = a.zps AND k.mnr=m.man_nr "
                        ." LEFT JOIN #__clm_meldeliste_spieler as l ON l.sid = a.sid AND l.lid = a.lid AND l.mgl_nr = a.gegner AND l.zps = a.gzps AND l.mnr=n.man_nr ";
            } else {
                $query .= " LEFT JOIN #__clm_dwz_spieler AS d ON ( d.PKZ = a.PKZ AND d.ZPS = a.zps AND d.sid = a.sid) "
                        ." LEFT JOIN #__clm_dwz_spieler AS e ON ( e.PKZ = a.gPKZ AND e.ZPS = a.gzps AND e.sid = a.sid) "
                        ." LEFT JOIN #__clm_meldeliste_spieler as k ON k.sid = a.sid AND k.lid = a.lid AND k.PKZ = a.PKZ AND k.zps = a.zps AND k.mnr=m.man_nr "
                        ." LEFT JOIN #__clm_meldeliste_spieler as l ON l.sid = a.sid AND l.lid = a.lid AND l.PKZ = a.gPKZ AND l.zps = a.gzps AND l.mnr=n.man_nr ";
            }
            $query .= " LEFT JOIN #__clm_ergebnis AS p ON p.eid = a.ergebnis "
                ." LEFT JOIN #__clm_ergebnis AS q ON q.eid = a.dwz_edit "
                ." WHERE a.sid =  ".$sid
                ." AND a.lid = ".$liga
                ." AND a.runde = ".$runde
                ." AND a.dg = ".$dg
                ." AND a.heim = 1"
                ." AND m.man_nr > 0 AND n.man_nr > 0 "
                ." ORDER BY a.paar ASC, a.brett ASC"
            ;
        } else {
            $query = "  SELECT a.zps, a.gzps, a.paar,a.brett,a.spieler,a.PKZ,a.gegner,a.gPKZ,a.ergebnis,a.kampflos, a.dwz_edit, a.dwz_editor, a.weiss,"
                ." a.pgnnr, pg.text,"
                ." m.name, n.name as mgname, m.sname, n.sname as smgname, d.Spielername as hname, d.DWZ as hdwz, d.FIDE_Elo as helo, d.Status as hstatus,"
                ." p.erg_text as erg_text, e.Spielername as gname, e.DWZ as gdwz, e.FIDE_Elo as gelo, e.Status as gstatus, q.erg_text as dwz_text,"
                ." k.snr as hsnr, l.snr as gsnr, k.start_dwz as hstart_dwz, l.start_dwz as gstart_dwz, k.attr as hattr, l.attr as gattr,"
                ." t.man_nr as tmnr, t.Rang as trang, s.man_nr as smnr, s.Rang as srang"
                ." FROM #__clm_rnd_spl as a "
                ." LEFT JOIN #__clm_rnd_man as r ON ( r.lid = a.lid AND r.runde = a.runde AND r.tln_nr = a.tln_nr AND  r.dg = a.dg) "
                ." LEFT JOIN #__clm_mannschaften AS m ON ( m.tln_nr = r.tln_nr AND m.liga = a.lid) "
                ." LEFT JOIN #__clm_mannschaften AS n ON ( n.tln_nr = r.gegner AND n.liga = a.lid) "
                    ." LEFT JOIN #__clm_dwz_spieler AS d ON ( d.Mgl_Nr = a.spieler AND d.ZPS = a.zps AND d.sid = a.sid) "
                    ." LEFT JOIN #__clm_dwz_spieler AS e ON ( e.Mgl_Nr = a.gegner AND e.ZPS = a.gzps AND e.sid = a.sid) "
                ." LEFT JOIN #__clm_ergebnis AS p ON p.eid = a.ergebnis "
                ." LEFT JOIN #__clm_ergebnis AS q ON q.eid = a.dwz_edit "
                ." LEFT JOIN #__clm_meldeliste_spieler as k ON k.sid = a.sid AND k.lid = a.lid AND k.mgl_nr = a.spieler AND k.zps = a.zps AND k.mnr=m.man_nr "  //klkl2
                ." LEFT JOIN #__clm_meldeliste_spieler as l ON l.sid = a.sid AND l.lid = a.lid AND l.mgl_nr = a.gegner AND l.zps = a.gzps AND l.mnr=n.man_nr "  //klkl2
                ." LEFT JOIN #__clm_rangliste_spieler as t on t.ZPSmgl = a.zps AND t.Mgl_Nr = a.spieler AND t.sid = a.sid AND t.Gruppe = ".$rang
                ." LEFT JOIN #__clm_rangliste_spieler as s on s.ZPSmgl = a.gzps AND s.Mgl_Nr = a.gegner AND s.sid = a.sid AND s.Gruppe = ".$rang
                ." LEFT JOIN #__clm_pgn AS pg ON ( pg.id = a.pgnnr) "
                    ." WHERE a.sid =  ".$sid
                    ." AND a.lid = ".$liga
                    ." AND a.runde = ".$runde
                    ." AND a.dg = ".$dg
                    ." AND a.heim = 1"
                    ." ORDER BY a.paar ASC, a.brett ASC"
            ;
        }
        return $query;
    }

    public function getCLMEinzel($options = array())
    {
        $query1	= " SET SESSION SQL_BIG_SELECTS=1 ";
        clm_core::$db->query($query1);

        $query	= $this->_getCLMEinzel($options);
        //$result = $this->_getList( $query );
        $result = clm_core::$db->loadObjectList($query);
        return @$result;
    }

    public function _getCLMSumme(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', 1);
        $liga	= clm_core::$load->request_int('liga', 1);
        $dg 	= clm_core::$load->request_int('dg');
        $runde = clm_core::$load->request_int('runde');

        $db			= JFactory::getDBO();
        //		$id			= @$options['id'];

        $query = " SELECT u.name,a.paar as paarung,a.runde as runde,a.brettpunkte as sum, dwz_editor "
            ." FROM #__clm_rnd_man as a "
            ." LEFT JOIN #__clm_user as u ON (u.jid = a.gemeldet AND a.heim = 1 AND u.sid = $sid)"
                ." WHERE a.lid = ".$liga
                ." AND a.sid = ".$sid
                ." AND a.runde = ".$runde
                ." AND a.dg = ".$dg
                ." ORDER BY a.paar ASC, a.heim DESC"
        ;
        return $query;
    }
    public function getCLMSumme($options = array())
    {
        $query	= $this->_getCLMSumme($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMOK(&$options)
    {
        $db			= JFactory::getDBO();
        //		$id			= @$options['id'];

        $sid	= clm_core::$load->request_int('saison', 1);
        $lid	= clm_core::$load->request_int('liga', 1);
        $dg 	= clm_core::$load->request_int('dg');
        $runde = clm_core::$load->request_int('runde');

        // Anz.Runden und Durchgänge aus #__clm_liga holen
        $query = " SELECT a.runden, a.durchgang "
            ." FROM #__clm_liga as a"
            ." WHERE a.id = ".$lid
        ;
        $db->setQuery($query);
        $liga = $db->loadObjectList();

        if ($dg > 1) {
            $runde = $runde + $liga[0]->runden;
        }

        $query = " SELECT a.sl_ok as sl_ok"
            ." FROM #__clm_runden_termine as a"
                ." WHERE a.liga = ".$lid
                ." AND a.sid = ".$sid
                ." AND a.nr = ".$runde
                ." AND a.published = 1"
        ;
        return $query;
    }
    public function getCLMOK($options = array())
    {
        $query	= $this->_getCLMOK($options);
        $result = $this->_getList($query);
        return @$result;
    }
    ///////////////
    ////////////////
    /////////////
    public function _getCLMSpielfrei(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', 1);
        $liga	= clm_core::$load->request_int('liga', 1);
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query = "SELECT COUNT(tln_nr) AS count FROM #__clm_mannschaften "
            ." WHERE liga = ".$liga
            ." AND sid = ".$sid
            ." AND man_nr = 0"
        ;
        return $query;
    }
    public function getCLMSpielfrei($options = array())
    {
        $query	= $this->_getCLMSpielfrei($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMPunkte(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', 1);
        $liga	= clm_core::$load->request_int('liga', 1);
        $dg 	= clm_core::$load->request_int('dg');
        $runde = clm_core::$load->request_int('runde');
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];
        // ordering für Rangliste -> Ersatz für direkten Vergleich
        $query = "SELECT a.order, a.runden, a.durchgang, a.b_wertung, a.liga_mt, a.runden_modus FROM #__clm_liga as a"
            ." WHERE id = ".$liga
            //." AND sid = ".$sid
        ;
        $db->setQuery($query);
        $order = $db->loadObjectList();
        if (isset($order[0]) and  $order[0]->order == 1) {
            $ordering = " , m.ordering ASC";
        } else {
            $ordering = ' ';
        }
        if (isset($order[0])) {
            $rc = clm_core::$api->db_tournament_ranking_round($liga, true, $runde, $dg);
        }
        if (isset($order[0]) and $order[0]->liga_mt == 0) { // Liga
            $query = " SELECT a.tln_nr as tln_nr,m.name as name, (SUM(a.manpunkte) - m.abzug) as mp, m.abzug as abzug, "
            ." (SUM(a.brettpunkte) - m.bpabzug) as bp, m.bpabzug, SUM(a.wertpunkte) as wp, m.published, m.man_nr, "
            ." COUNT(DISTINCT case when a.gemeldet > 1 then CONCAT(a.dg,' ',a.runde) else null end) as spiele, "
            ." m.sumtiebr1, m.sumtiebr2, m.sumtiebr3, z_rankingpos as rankingpos"
            ." FROM #__clm_rnd_man as a "
            ." LEFT JOIN #__clm_mannschaften as m ON m.liga = $liga AND m.tln_nr = a.tln_nr "
            ." WHERE a.lid = ".$liga
            ." AND m.man_nr <> 0 ";
        } else { // Mannschaftsturnier
            $rc = 999;
            //			if (isset($order[0])) $rc = clm_core::$api->db_tournament_ranking_round($liga,true,$runde,$dg);
            $query = " SELECT a.tln_nr as tln_nr,m.name as name, (SUM(a.manpunkte) - m.abzug) as mp, m.abzug as abzug, "
            ." (SUM(a.brettpunkte) - m.bpabzug) as bp, m.bpabzug, SUM(a.wertpunkte) as wp, m.published, m.man_nr, "
            ." COUNT(DISTINCT case when a.gemeldet > 1 then CONCAT(a.dg,' ',a.runde) else null end) as spiele, "
            ." m.z_sumtiebr1 as sumtiebr1, m.z_sumtiebr2  as sumtiebr2, m.z_sumtiebr3 as sumtiebr3, m.z_rankingpos as rankingpos "
            ." FROM #__clm_rnd_man as a "
            ." LEFT JOIN #__clm_mannschaften as m ON m.liga = $liga AND m.tln_nr = a.tln_nr "
            ." WHERE a.lid = ".$liga
            ." AND m.man_nr <> 0 ";
        }
        if (($runde != "") and ($dg == 1)) {
            $query = $query." AND runde < ".($runde + 1)." AND dg = 1";
        }
        if (($runde != "") and ($dg > 1)) {
            $query = $query." AND (( runde < ".($runde + 1)." AND dg = ".$dg.") OR dg < ".$dg.")";
        }

        $query = $query
            ." GROUP BY a.tln_nr ";
        if (isset($order[0]) and $order[0]->b_wertung == 0 and $order[0]->liga_mt == 0) {
            $query = $query
            ." ORDER BY mp DESC, bp DESC".$ordering;
        }
        if (isset($order[0]) and $order[0]->b_wertung == 3 and $order[0]->liga_mt == 0) {
            $query = $query
            ." ORDER BY mp DESC, bp DESC, wp DESC".$ordering;
        }
        if (isset($order[0]) and $order[0]->b_wertung == 4 and $order[0]->liga_mt == 0) {
            $query = $query
            ." ORDER BY mp DESC, bp DESC ".$ordering.", wp DESC";
        }
        if (isset($order[0]) and $order[0]->liga_mt == 1) {                       //mtmt
            if ($runde = 0) {
                $query = $query." ORDER BY rankingpos ASC";
            } else {
                $query = $query." ORDER BY z_rankingpos ASC";
            }
        }
        $query = $query
            .", a.tln_nr ASC";
        return $query;
    }
    public function getCLMPunkte($options = array())
    {
        $query	= $this->_getCLMPunkte($options);
        $result = $this->_getList($query);
        return @$result;
    }


    public static function punkte_tlnr($sid, $lid, $tlnr, $dg, $runden_modus)
    {
        //defined('_JEXEC') or die('Restricted access');
        $db	= JFactory::getDBO();
        $query = " SELECT a.runde, a.dg, a.tln_nr, a.gegner, a.brettpunkte, m.rankingpos, m.name "
            ." FROM #__clm_rnd_man as a "
            ." LEFT JOIN #__clm_mannschaften AS m ON m.liga = a.lid AND m.tln_nr = a.gegner "
            ." WHERE a.lid = ".$lid
            ." AND a.sid = ".$sid
            ." AND a.tln_nr = ".$tlnr
            ." AND a.dg = $dg "
            //." ORDER BY a.gegner "
        ;
        if ($runden_modus == 3) {
            $query .= " ORDER BY a.runde";
        } else {
            $query .= " ORDER BY a.gegner ";
        }
        $db 	= JFactory::getDBO();
        $db->setQuery($query);
        $runden	= $db->loadObjectList();

        return $runden;
    }

    public static function punkte_text($lid)
    {
        defined('_JEXEC') or die('Restricted access');

        //Konfigurationsparameter laden
        $config			= clm_core::$db->config();

        // Ergebnisliste laden
        $sql = "SELECT a.id, a.erg_text "
            ." FROM #__clm_ergebnis as a "
        ;
        $db 		= JFactory::getDBO();
        $db->setQuery($sql);
        $ergebnis	= $db->loadObjectList();

        // Punktemodus aus #__clm_liga holen
        $query = " SELECT a.sieg, a.remis, a.nieder, a.antritt, a.runden_modus "
            ." FROM #__clm_liga as a"
            ." WHERE a.id = ".$lid
        ;
        $db->setQuery($query);
        $liga = $db->loadObjectList();
        $sieg 		= $liga[0]->sieg;
        $remis 		= $liga[0]->remis;
        $nieder		= $liga[0]->nieder;
        $antritt	= $liga[0]->antritt;

        // Ergebnistexte nach Modus setzen
        $ergebnis[0]->erg_text = ($nieder + $antritt)." - ".($sieg + $antritt);
        $ergebnis[1]->erg_text = ($sieg + $antritt)." - ".($nieder + $antritt);
        $ergebnis[2]->erg_text = ($remis + $antritt)." - ".($remis + $antritt);
        $ergebnis[3]->erg_text = ($nieder + $antritt)." - ".($nieder + $antritt);
        if ($config->fe_display_lose_by_default == 1) {
            $ergebnis[4]->erg_text = round($nieder)." - ".round($antritt + $sieg)." (kl)";
            $ergebnis[5]->erg_text = round($antritt + $sieg)." - ".round($nieder)." (kl)";
            $ergebnis[6]->erg_text = round($nieder)." - ".round($nieder)." (kl)";
        }

        return $ergebnis;
    }

    // Paarungen Folgerunde   klkl
    public function _getCLMPaar1(&$options)
    {
        $sid = clm_core::$load->request_int('saison', 1);
        $lid = clm_core::$load->request_int('liga', 1);
        $dg 	= clm_core::$load->request_int('dg');
        $runde = clm_core::$load->request_int('runde');

        $db			= JFactory::getDBO();
        //	$id			= @$options['id'];

        // Anz.Runden und Durchgänge aus #__clm_liga holen
        $query = " SELECT a.runden, a.durchgang "
            ." FROM #__clm_liga as a"
            ." WHERE a.id = ".$lid
        ;
        $db->setQuery($query);
        $liga = $db->loadObjectList();

        if (($liga[0]->durchgang > "1") && ($dg == 1) && ($liga[0]->runden == $runde)) {
            $dg++;
            $runde = 1;
        } else {
            $runde++;
        }

        $db			= JFactory::getDBO();
        //	$id			= @$options['id'];

        $query = " SELECT a.*,g.id as gid, g.name as gname, g.tln_nr as gtln,g.published as gpublished, "
            ." h.id as hid, h.name as hname, h.tln_nr as htln, h.published as hpublished "
            ." FROM #__clm_rnd_man as a"
            ." LEFT JOIN #__clm_mannschaften AS g ON g.tln_nr = a.gegner"
            ." LEFT JOIN #__clm_mannschaften AS h ON h.tln_nr = a.tln_nr"
                ." WHERE g.liga = ".$lid
                ." AND g.sid = ".$sid
                ." AND h.liga = ".$lid
                ." AND h.sid = ".$sid
                ." AND a.sid = ".$sid
                ." AND a.lid = ".$lid
                ." AND a.runde = ".$runde
                ." AND a.dg = ".$dg
                ." AND a.heim = 1 "
                ." ORDER BY a.paar ASC"
        ;

        return $query;
    }

    public function getCLMPaar1($options = array())
    {
        $query	= $this->_getCLMPaar1($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMClmuser(&$options)
    {
        $user	= JFactory::getUser();
        $jid	= $user->get('id');
        $sid	= clm_core::$load->request_int('saison', 1);

        $db	= JFactory::getDBO();
        //		$id	= @$options['id'];

        $query	= "SELECT zps,published "
            ." FROM #__clm_user "
            ." WHERE jid = $jid "
            ." AND sid = $sid "
        ;
        return $query;
    }

    public function getCLMClmuser($options = array())
    {
        $query	= $this->_getCLMClmuser($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public static function team_tlnr($lid, $tlnr)
    {
        $db	= JFactory::getDBO();
        $query = " SELECT rang "
            ." FROM #__clm_liga as a "
            ." WHERE a.id = ".$lid
        ;
        $db->setQuery($query);
        $man	= $db->loadObjectList();
        $rang	= $man[0]->rang;
        if ($rang == 0) {
            $query = " SELECT a.tln_nr, a.man_nr, m.snr, m.mgl_nr, m.zps, d.Spielername, IF(m.start_dwz IS NULL, d.DWZ, m.start_dwz) as dwz, m.gesperrt "
                ." FROM #__clm_mannschaften as a "
                ." LEFT JOIN #__clm_meldeliste_spieler AS m ON m.lid = a.liga AND m.mnr = a.man_nr "
                        ." AND ( m.zps = a.zps OR FIND_IN_SET(m.zps, a.sg_zps))	"
                ." LEFT JOIN #__clm_dwz_spieler AS d ON d.sid = a.sid AND d.ZPS = m.zps AND d.Mgl_Nr = m.mgl_nr "
                ." WHERE a.liga = ".$lid
                ." AND a.tln_nr = ".$tlnr
                ." ORDER BY m.snr "
            ;
        } else {
            $query = " SELECT a.tln_nr, a.man_nr, m.snr, m.mgl_nr, m.zps, d.Spielername, IF(m.start_dwz IS NULL, d.DWZ, m.start_dwz) as dwz, t.Rang as trang, t.man_nr as tman_nr, m.gesperrt "
                ." FROM #__clm_mannschaften as a "
                ." LEFT JOIN #__clm_meldeliste_spieler AS m ON m.lid = a.liga AND m.mnr = a.man_nr "
                        ." AND ( m.zps = a.zps OR FIND_IN_SET(m.zps, a.sg_zps))	"
                ." LEFT JOIN #__clm_dwz_spieler AS d ON d.sid = a.sid AND d.ZPS = m.zps AND d.Mgl_Nr = m.mgl_nr "
                ." LEFT JOIN #__clm_rangliste_spieler as t on t.ZPS = a.zps AND t.Mgl_Nr = m.mgl_nr AND t.sid = a.sid AND t.Gruppe = ".$rang
                ." WHERE a.liga = ".$lid
                ." AND a.tln_nr = ".$tlnr
                ." AND a.man_nr = t.man_nr "
                ." ORDER BY m.snr "
            ;
        }

        $db 	= JFactory::getDBO();
        $db->setQuery($query);
        $team	= $db->loadObjectList();

        return $team;
    }


}
