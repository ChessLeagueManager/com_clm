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

require_once JPATH_COMPONENT_ADMINISTRATOR. '/helpers/addresshandler.php';

class CLMModelMannschaft extends JModelLegacy
{
    public function _getCLMMannschaft(&$options)
    {
        $sid = clm_core::$load->request_int('saison', 1);
        $liga = clm_core::$load->request_int('liga', 1);
        $tln = clm_core::$load->request_int('tlnr');

        $db			= JFactory::getDBO();
        //		$id			= @$options['id'];

        $query = "SELECT a.zps,a.sg_zps,u.name as mf_name,u.email as email, a.lokal_coord, "
            ." u.tel_mobil,u.tel_fest, l.durchgang as dg, l.rang as lrang, l.params, l.stamm, "
            ." l.name as liga_name, l.runden as runden, l.published as lpublished, l.anzeige_ma as anzeige_ma, a.* "
            ." FROM #__clm_mannschaften as a "
            ." LEFT JOIN #__clm_user AS u ON u.jid = a.mf AND  u.sid = a.sid "
            ." LEFT JOIN #__clm_liga AS l ON l.id = a.liga"
            ." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
            ." WHERE a.liga = ".$liga
            ." AND a.sid = ".$sid
            ." AND a.tln_nr = ".$tln
            ." AND s.published = 1"
        ;
        return $query;
    }

    public function getCLMMannschaft($options = array())
    {
        $query	= $this->_getCLMMannschaft($options);
        $result = $this->_getList($query);
        $addressHandler = new AddressHandler();
        $addressHandler->queryLocation($result, 0);
        return @$result;
    }

    public function _getCLMVereine(&$options)
    {
        $sid = clm_core::$load->request_int('saison', 1);
        $liga = clm_core::$load->request_int('liga', 1);
        $tln = clm_core::$load->request_int('tlnr');

        $db			= JFactory::getDBO();
        //		$id			= @$options['id'];

        $query = "SELECT a.zps,a.sg_zps,v.zps as vzps,v.name "
            ." FROM #__clm_mannschaften as a "
            ." LEFT JOIN #__clm_vereine AS v ON (a.zps = v.zps OR FIND_IN_SET(v.zps,a.sg_zps) != 0 ) AND  v.sid = a.sid "
            ." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
            ." WHERE a.liga = ".$liga
            ." AND a.sid = ".$sid
            ." AND a.tln_nr = ".$tln
            ." AND s.published = 1"
        ;
        return $query;
    }

    public function getCLMVereine($options = array())
    {
        $query	= $this->_getCLMVereine($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMCount(&$options)
    {
        $sid = clm_core::$load->request_int('saison', 1);
        $liga = clm_core::$load->request_int('liga', 1);
        $tln = clm_core::$load->request_int('tlnr');
        // Konfigurationsparameter auslesen
        $config = clm_core::$db->config();
        $countryversion = $config->countryversion;

        $db			= JFactory::getDBO();
        //		$id			= @$options['id'];

        $query = " SELECT l.rang,a.zps as zps, a.sg_zps as sgzps, a.man_nr as man_nr, l.ersatz_regel as ersatz_regel"
            ." FROM #__clm_mannschaften as a "
            ." LEFT JOIN #__clm_liga as l ON l.id =".$liga
            ." WHERE a.liga = ".$liga
            ." AND a.sid = ".$sid
            ." AND a.tln_nr = ".$tln
        ;
        $db->setQuery($query);
        $man	= $db->loadObjectList();
        $zps	= $man[0]->zps;
        $sgzps	= $man[0]->sgzps;
        $mnr	= $man[0]->man_nr;

        if (!$mnr) {
            $mnr = 0;
        }
        $rang	= $man[0]->rang;
        $ersatz_regel	= $man[0]->ersatz_regel;
        if ($rang > 0) {
            $query = " SELECT a.start_dwz, m.tln_nr as tln_nr,a.snr,a.dwz,a.mgl_nr,a.zps, '' as PKZ, d.Spielername as name,d.DWZ as dwz,d.FIDE_Titel,d.Status,d.gesperrt "
                .",r.man_nr as rmnr, r.Rang as rrang "
                ." FROM #__clm_meldeliste_spieler as a "
//			." LEFT JOIN #__clm_rangliste_spieler as r on r.ZPS = a.zps AND r.Mgl_Nr = a.mgl_nr AND r.sid = a.sid "
                ." LEFT JOIN #__clm_rangliste_spieler as r on r.ZPSmgl = a.zps AND r.Mgl_Nr = a.mgl_nr AND r.sid = a.sid "
                ." LEFT JOIN #__clm_rangliste_id as i on i.ZPS = a.zps AND i.gid = r.Gruppe AND i.sid = a.sid "
                ." LEFT JOIN #__clm_dwz_spieler as d on d.zps = a.zps AND d.mgl_nr = a.mgl_nr AND d.sid = a.sid"
                ." LEFT JOIN #__clm_mannschaften as m on m.liga = a.lid AND (m.zps = a.zps OR m.sg_zps = a.zps) AND m.man_nr = a.mnr AND m.sid = a.sid"
                ." WHERE a.sid = ".$sid
                ." AND (( a.zps = '$zps' AND a.mnr = $mnr) OR ( a.zps='$sgzps' AND a.mnr = $mnr )) "
                ." AND a.lid = ".$liga
                ." AND r.Gruppe = $rang ";
            if ($ersatz_regel == 0) {
                $query .= " AND r.man_nr NOT IN ( SELECT aa.man_nr FROM #__clm_mannschaften as aa "
                            ." WHERE aa.liga = ".$liga
                            ." AND aa.sid = ".$sid
                            ." AND ( aa.zps = r.ZPS )"
                            ." AND aa.man_nr <> a.mnr )";
            }
            $query .= " ORDER BY rmnr ASC, rrang ASC ";
        } else {
            if ($zps != "0") { //normal
                $query = " SELECT a.start_dwz,a.mgl_nr,a.zps,a.PKZ,a.attr, d.Spielername as name,d.DWZ as dwz,d.FIDE_Titel,d.Status,d.gesperrt "
                    ." FROM #__clm_meldeliste_spieler as a ";
                if ($countryversion == "de") {
                    $query .= " LEFT JOIN #__clm_dwz_spieler as d on d.zps = a.zps AND d.mgl_nr = a.mgl_nr AND d.sid = a.sid";
                } else {
                    $query .= " LEFT JOIN #__clm_dwz_spieler as d on d.zps = a.zps AND d.PKZ = a.PKZ AND d.sid = a.sid";
                }
                $query .= " WHERE a.sid = ".$sid
                    ." AND (( a.zps = '$zps' AND a.mnr = $mnr) OR ( '$sgzps' LIKE CONCAT('%', a.zps, '%') AND a.mnr = $mnr )) " //neu
                    ." AND a.lid = ".$liga
                    ." AND a.zps != '' "
                    ." ORDER BY a.mnr ASC, a.snr ASC ";
            } else {	//Schulschach u.ä.
                $zps = "-1";
                $query = " SELECT a.start_dwz,a.mgl_nr,a.zps,a.PKZ,a.attr, d.Spielername as name,d.DWZ as dwz,d.FIDE_Titel "
                    ." FROM #__clm_meldeliste_spieler as a ";
                $query .= " LEFT JOIN #__clm_dwz_spieler as d on d.zps = a.zps AND d.mgl_nr = a.mgl_nr AND d.sid = a.sid";
                $query .= " WHERE a.sid = ".$sid
                    //." AND a.zps = '".$zps."' AND a.mnr = $mnr"
                    ." AND a.mnr = $mnr"
                    ." AND a.lid = ".$liga
                    ." ORDER BY a.mnr ASC, a.snr ASC ";
            }
        }
        return $query;
    }

    public function getCLMCount($options = array())
    {
        $query	= $this->_getCLMCount($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMBP(&$options)
    {
        $sid = clm_core::$load->request_int('saison', 1);
        $liga = clm_core::$load->request_int('liga', 1);
        $tln = clm_core::$load->request_int('tlnr');

        $db			= JFactory::getDBO();
        //		$id			= @$options['id'];

        $query = " SELECT a.tln_nr,a.gegner,a.brettpunkte,a.runde,a.dg,a.paar,a.heim "
            ." FROM #__clm_rnd_man as a "
            ." WHERE a.lid = ".$liga
            ." AND a.sid = ".$sid
            ." AND (a.tln_nr = ".$tln." or a.gegner = ".$tln.")"
            ." ORDER BY a.dg ASC ,a.runde ASC "
        ;
        return $query;
    }

    public function getCLMBP($options = array())
    {
        $query	= $this->_getCLMBP($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMSumBP(&$options)
    {
        $sid = clm_core::$load->request_int('saison', 1);
        $liga = clm_core::$load->request_int('liga', 1);
        $tln = clm_core::$load->request_int('tlnr');

        $db			= JFactory::getDBO();
        //		$id			= @$options['id'];

        $query = " SELECT SUM(a.brettpunkte) as summe "
            ." FROM #__clm_rnd_man as a "
            ." WHERE a.lid = ".$liga
            ." AND a.sid = ".$sid
            ." AND a.tln_nr = ".$tln
        ;
        return $query;
    }

    public function getCLMSumBP($options = array())
    {
        $query	= $this->_getCLMSumBP($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMSumPlan(&$options)
    {
        $sid = clm_core::$load->request_int('saison', 1);
        $liga = clm_core::$load->request_int('liga', 1);
        $tln = clm_core::$load->request_int('tlnr');
        $db			= JFactory::getDBO();

        // Rundenanzahl pro Durchgang aus Liga
        $query = "SELECT a.* FROM #__clm_liga as a"
            ." WHERE id = ".$liga
        ;
        $db->setQuery($query);
        $sliga = $db->loadObjectList();
        if (isset($sliga[0]->runden)) {
            $arunden = $sliga[0]->runden;
        } else {
            $arunden = 0;
        }

        $query = " SELECT a.dg,a.lid,a.sid,a.runde,a.paar,a.tln_nr,a.gegner "
            //." ,t.name as dat_name, t.datum as datum "
            ." ,m.name as hname, n.name as gname, m.published as hpublished, "
            ." n.published as gpublished "
            ." FROM #__clm_rnd_man as a "
            ." LEFT JOIN #__clm_mannschaften as m ON m.tln_nr = a.tln_nr AND m.sid = a.sid AND m.liga = a.lid "
            ." LEFT JOIN #__clm_mannschaften as n ON n.tln_nr = a.gegner AND n.sid = a.sid AND n.liga = a.lid"
            ." LEFT JOIN #__clm_runden_termine as t ON t.nr = (((a.dg - 1) * $arunden) + a.runde) AND t.liga = $liga AND t.sid = a.sid "
            ." WHERE a.lid =".$liga
            ." AND a.sid =".$sid
            ." AND a.heim = 1"
            ." AND (a.tln_nr = $tln OR a.gegner = $tln) "
            ." AND t.published = 1"
            ." ORDER BY a.dg ASC,a.runde ASC, a.paar ASC "
        ;
        return $query;
    }

    public function getCLMSumPlan($options = array())
    {
        $query	= $this->_getCLMSumPlan($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMTermin(&$options)
    {
        $sid = clm_core::$load->request_int('saison', 1);
        $liga = clm_core::$load->request_int('liga', 1);
        $tln = clm_core::$load->request_int('tlnr');

        $db			= JFactory::getDBO();
        //		$id			= @$options['id'];

        $query = "SELECT a.nr, a.datum, a.startzeit, m.pdate, m.ptime FROM #__clm_runden_termine as a "
            ." LEFT JOIN #__clm_liga as l ON l.id = a.liga "
            ." LEFT JOIN #__clm_rnd_man as m ON m.tln_nr = ".$tln." AND m.sid = a.sid AND m.lid = a.liga AND (m.runde + ((m.dg - 1) * l.runden)) = a.nr "
            ." WHERE a.liga = ".$liga
            ." AND a.sid = ".$sid
            ." ORDER BY nr "
        ;
        return $query;
    }
    public function getCLMTermin($options = array())
    {
        $query	= $this->_getCLMTermin($options);
        $result = $this->_getList($query);
        return @$result;
    }

    //Einzelergebnisse
    public function _getCLMEinzel(&$options)
    {
        $sid = clm_core::$load->request_int('saison', 1);
        $liga = clm_core::$load->request_int('liga', 1);
        $tln = clm_core::$load->request_int('tlnr');
        // Konfigurationsparameter auslesen
        $config = clm_core::$db->config();
        $countryversion = $config->countryversion;

        $db			= JFactory::getDBO();
        //		$id			= @$options['id'];

        $query = " SELECT l.rang,a.zps as zps, a.sg_zps as sgzps, a.man_nr as man_nr"
            ." FROM #__clm_mannschaften as a "
            ." LEFT JOIN #__clm_liga as l ON l.id =".$liga
            ." WHERE a.liga = ".$liga
            ." AND a.sid = ".$sid
        ;
        $db->setQuery($query);
        $man	= $db->loadObjectList();
        $rang	= $man[0]->rang;

        if ($rang > 0) {
            $query = " SELECT a.*, "
                ." m.snr as snr ,r.man_nr as rmnr, r.Rang as rrang "
                ." FROM #__clm_rnd_spl as a "
                ." LEFT JOIN #__clm_mannschaften as m1 ON m1.sid = a.sid AND m1.liga = a.lid AND m1.tln_nr = a.tln_nr"
                ." LEFT JOIN #__clm_meldeliste_spieler as m ON m.sid = a.sid AND m.lid = a.lid AND m.mgl_nr = a.spieler AND m.zps = a.zps AND m.mnr = m1.man_nr"
//			." LEFT JOIN #__clm_rangliste_spieler as r on r.ZPS = a.zps AND r.Mgl_Nr = a.spieler AND r.sid = a.sid AND r.Gruppe = ".$rang
                ." LEFT JOIN #__clm_rangliste_spieler as r on r.ZPSmgl = a.zps AND r.Mgl_Nr = a.spieler AND r.sid = a.sid AND r.Gruppe = ".$rang
                //." LEFT JOIN #__clm_rangliste_id as i on i.ZPS = a.zps AND i.gid = r.Gruppe AND i.sid = a.sid "
                ." WHERE m.lid = ".$liga
                ." AND m.sid =".$sid
                ." AND a.lid =".$liga
                ." AND a.sid =".$sid
                ." AND a.tln_nr =".$tln
                ." ORDER BY rmnr ASC, rrang ASC, a.dg ASC, a.runde ASC "
            ;
        } else {
            $query = " SELECT a.*, "
                ." m.snr as snr "
                ." FROM #__clm_rnd_spl as a "
                ." LEFT JOIN #__clm_mannschaften as m1 ON m1.sid = a.sid AND m1.liga = a.lid AND m1.tln_nr = a.tln_nr";
            if ($countryversion == "de") {
                $query .= " LEFT JOIN #__clm_meldeliste_spieler as m ON m.sid = a.sid AND m.lid = a.lid AND m.mgl_nr = a.spieler AND m.zps = a.zps AND m.mnr = m1.man_nr";
            } else {
                $query .= " LEFT JOIN #__clm_meldeliste_spieler as m ON m.sid = a.sid AND m.lid = a.lid AND m.PKZ = a.PKZ AND m.zps = a.zps AND m.mnr = m1.man_nr";
            }
            $query .= " WHERE m.lid = ".$liga
                ." AND m.sid =".$sid
                ." AND a.lid =".$liga
                ." AND a.sid =".$sid
                ." AND a.tln_nr =".$tln
                ." ORDER BY snr ASC,a.dg ASC,a.runde ASC "
            ;
        }
        return $query;
    }

    public function getCLMEinzel($options = array())
    {
        $query	= $this->_getCLMEinzel($options);
        $result = $this->_getList($query);
        return @$result;
    }
    //Saison
    public function _getCLMSaison(&$options)
    {
        $sid = clm_core::$load->request_int('saison', 1);

        $db			= JFactory::getDBO();
        //		$id			= @$options['id'];

        $query = " SELECT s.name, s.datum as dsb_datum "
            ." FROM #__clm_saison as s "
            ." WHERE s.id = ".$sid
        ;
        return $query;
    }

    public function getCLMSaison($options = array())
    {
        $query	= $this->_getCLMSaison($options);
        $result = $this->_getList($query);
        return @$result;
    }

}
