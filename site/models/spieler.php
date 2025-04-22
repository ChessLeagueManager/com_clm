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

class CLMModelSpieler extends JModelLegacy
{
    public function _getCLMSpieler(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', '1');
        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $mgl	= clm_core::$load->request_int('mglnr');
        $PKZ	= clm_core::$load->request_string('PKZ');
        //CLM parameter auslesen
        $config = clm_core::$db->config();
        $countryversion = $config->countryversion;

        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query = "SELECT a.Spielername,l.name as liga_name,l.id as liga,a.ZPS,a.Mgl_Nr,a.PKZ,"
            ." a.DWZ as dsbDWZ,a.DWZ_Index,a.FIDE_ELO,a.FIDE_ID,a.Status,"
            ." n.name,n.tln_nr, m.*, s.datum as dsb_datum, s.name as s_name";
        if ($zps != '-1') {
            $query .= ", d.Vereinname";
        }
        $query .= " FROM #__clm_dwz_spieler as a ";
        if ($countryversion == "de") {
            $query .= " LEFT JOIN #__clm_meldeliste_spieler as m ON m.zps = a.ZPS AND m.mgl_nr = a.Mgl_Nr AND m.sid = a.sid ";
        } else {
            $query .= " LEFT JOIN #__clm_meldeliste_spieler as m ON m.zps = a.ZPS AND m.PKZ = a.PKZ AND m.sid = a.sid ";
        }
        if ($zps != '-1') {
            $query .= " LEFT JOIN #__clm_dwz_vereine as d ON a.ZPS = d.ZPS AND d.sid = a.sid";
            $query .= " LEFT JOIN #__clm_mannschaften as n ON ( (n.zps = a.ZPS) OR (FIND_IN_SET(a.ZPS, n.sg_zps) != 0)) AND n.man_nr = m.mnr AND n.liga = m.lid AND n.sid = a.sid";
        } else {
            $query .= " LEFT JOIN #__clm_mannschaften as n ON (n.zps = '0' OR n.zps = '-1') AND n.man_nr = m.mnr AND n.liga = m.lid AND n.sid = a.sid";
        }
        $query .= " LEFT JOIN #__clm_liga l ON l.id = n.liga AND l.sid = n.sid "
            ." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
            ." WHERE a.ZPS = '$zps'";
        if ($countryversion == "de") {
            $query .= " AND a.Mgl_Nr = ".$mgl;
        } else {
            $query .= " AND a.PKZ = '".$PKZ."'";
        }
        $query .= " AND a.sid = ".$sid
            ." AND s.published = 1"
            ." AND l.published = 1"
            ." ORDER BY l.id ASC "
        ;
        return $query;

    }

    public function getCLMSpieler($options = array())
    {
        $query	= $this->_getCLMSpieler($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public static function getCLMLink()
    {
        $sid	= clm_core::$load->request_int('saison', '1');
        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $mgl	= clm_core::$load->request_int('mglnr');
        $PKZ	= clm_core::$load->request_string('PKZ');
        //CLM parameter auslesen
        $config = clm_core::$db->config();
        $countryversion = $config->countryversion;

        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        // Array zum speichern von Liga,Tlnr und Mannschaftsname
        $erg = array();

        // Mannschaften mit Aufstellung nach Meldeliste suchen
        $query = " SELECT a.lid as lid, l.name as liga_name, m.tln_nr as tln_nr, m.name as name FROM #__clm_meldeliste_spieler as a "
            ." LEFT JOIN #__clm_mannschaften as m ON ( (m.zps = a.zps) OR (FIND_IN_SET(a.zps, m.sg_zps) != 0)) AND m.man_nr = a.mnr AND m.sid = a.sid AND m.liga = a.lid "		//neu
            ." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
            ." LEFT JOIN #__clm_liga AS l ON l.id = a.lid AND l.sid = a.sid "
            ." WHERE a.zps = '$zps' ";
        if ($countryversion == "de") {
            $query .= "	AND a.mgl_nr = $mgl ";
        } else {
            $query .= "	AND a.PKZ = '$PKZ' ";
        }
        $query .= "	AND s.published = 1 AND m.published = 1 AND l.published = 1 AND s.id = $sid AND status = 0 "
            ." ORDER BY a.mnr ASC "
        ;
        $db->setQuery($query);
        $melde = $db->loadObjectList();
        $x = 0;

        // Mannschaften mit Aufstellung nach Rangliste suchen
        $query = " SELECT l.id as lid,l.name as liga_name,l.rang, m.tln_nr as tln_nr, m.name as name FROM #__clm_rangliste_spieler as a "
            ." LEFT JOIN #__clm_liga as l ON l.rang = a.Gruppe AND l.sid = a.sid "
            ." LEFT JOIN #__clm_mannschaften AS m ON m.man_nr = a.man_nr AND ( a.ZPS = m.zps OR a.ZPS = m.sg_zps) AND m.liga = l.id "
            ." LEFT JOIN #__clm_rangliste_id AS r ON r.gid = l.rang AND r.sid = a.sid AND r.zps = a.ZPS "
            ." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
            ." WHERE a.ZPS ='$zps' ";
        if ($countryversion == "de") {
            $query .= "	AND a.mgl_nr = $mgl ";
        } else {
            $query .= "	AND a.PKZ = '$PKZ' ";
        }
        $query .= " AND m.id IS NOT NULL "
            ." AND a.sid = '$sid' AND m.published = 1 AND l.published = 1 AND r.published = 1 "
        ;
        $db->setQuery($query);
        $rang_count = $db->loadObjectList();

        $result = array_merge($rang_count, $melde);

        return $result;
    }

    public function _getCLMRunden(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', '1');
        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $mgl	= clm_core::$load->request_int('mglnr');
        $PKZ	= clm_core::$load->request_string('PKZ');
        //CLM parameter auslesen
        $config = clm_core::$db->config();
        $countryversion = $config->countryversion;

        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query = " SELECT l.name as league, s.gegner as Mgl_Nr,s.gPKZ,s.gzps,s.lid,s.runde,s.heim,s.weiss,s.brett,"
            ." s.kampflos,s.punkte,d.Spielername,m.name,d.DWZ,a.dg,a.gegner as tln, ml.start_dwz"
            ." FROM #__clm_rnd_spl as s "
            ." LEFT JOIN #__clm_rnd_man as a ON s.sid = a.sid AND s.lid = a.lid AND s.runde = a.runde AND s.paar = a.paar AND s.dg = a.dg AND s.tln_nr = a.tln_nr ";
        if ($countryversion == "de") {
            $query .= " LEFT JOIN #__clm_dwz_spieler as d ON d.ZPS = s.gzps AND d.Mgl_Nr = s.gegner AND d.sid = a.sid "
                ." LEFT JOIN #__clm_meldeliste_spieler as ml ON ml.zps = s.gzps AND ml.mgl_nr = s.gegner AND ml.sid = s.sid AND ml.lid = s.lid ";
        } else {
            $query .= " LEFT JOIN #__clm_dwz_spieler as d ON d.ZPS = s.gzps AND d.PKZ = s.gPKZ AND d.sid = a.sid "
                ." LEFT JOIN #__clm_meldeliste_spieler as ml ON ml.zps = s.gzps AND ml.PKZ = s.gPKZ AND ml.sid = s.sid AND ml.lid = s.lid ";
        }
        $query .= " LEFT JOIN #__clm_mannschaften as m ON m.liga = a.lid AND m.tln_nr = a.gegner "//AND m.zps = s.gzps
            ." LEFT JOIN #__clm_liga as l ON l.id = s.lid AND l.sid = s.sid "
            ." WHERE s.zps = '$zps'";
        if ($countryversion == "de") {
            $query .= " AND s.spieler =".$mgl;
        } else {
            $query .= " AND s.PKZ = '".$PKZ."'";
        }
        $query .= " AND a.sid =".$sid
            ." AND l.published = 1 "
            ." GROUP BY a.lid, a.dg, a.runde "
            ." ORDER BY a.lid, a.dg ASC, a.runde ASC "
        ;
        return $query;
    }

    public function getCLMRunden($options = array())
    {
        $query	= $this->_getCLMRunden($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMVereinsliste(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', '1');
        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query  = 'SELECT DISTINCT a.zps, a.name FROM #__clm_vereine as a'
            ." WHERE a.published = 1"
            ." ORDER BY a.name ASC "
        ;

        return $query;
    }

    public function getCLMVereinsliste($options = array())
    {
        $query	= $this->_getCLMVereinsliste($options);
        $result = $this->_getList($query);
        return @$result;
    }
    public function _getCLMSaisons(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', '1');
        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query  = ' SELECT a.name, a.id, a.archiv FROM #__clm_saison AS a'
            ." ORDER BY a.name DESC "
        ;

        return $query;
    }

    public function getCLMSaisons($options = array())
    {
        $query	= $this->_getCLMSaisons($options);
        $result = $this->_getList($query);
        return @$result;
    }


    public function _getCLMSpielerliste(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', '1');
        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query  = " SELECT DISTINCT a.Spielername, a.ZPS, a.Mgl_Nr, a.PKZ, a.sid FROM #__clm_dwz_spieler AS a"
            ." WHERE a.sid= '$sid'"
            ." AND ZPS= '$zps'"
            ." GROUP BY Spielername"
            ." ORDER BY a.Spielername ASC "
        ;

        return $query;
    }

    public function getCLMSpielerliste($options = array())
    {
        $query	= $this->_getCLMSpielerliste($options);
        $result = $this->_getList($query);
        return @$result;
    }
}
