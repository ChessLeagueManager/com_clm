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

class CLMModelPaarungsliste extends JModelLegacy
{
    public function _getCLMLiga(&$options)
    {
        //	$sid	= clm_core::$load->request_int('saison',1);
        $liga	= clm_core::$load->request_int('liga', 0);
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query = " SELECT a.*, u.name as sl, u.email FROM #__clm_liga as a"
            ." LEFT JOIN #__clm_user as u ON a.sl = u.jid AND u.sid = a.sid"
            ." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
            ." WHERE a.id = ".$liga
            //." AND a.sid = ".$sid         //da die liga-nummer saisonübergreifend vergeben wird, kann auf die test bzgl. sid verzichtet werden
            ." AND s.published = 1"
        ;
        return $query;
    }
    public function getCLMLiga($options = array())
    {
        $query	= $this->_getCLMLiga($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMTermin(&$options)
    {
        //	$sid	= clm_core::$load->request_int('saison',1);
        $liga	= clm_core::$load->request_int('liga', 0);
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query = "SELECT nr, name, datum, startzeit, enddatum, bemerkungen, published FROM #__clm_runden_termine "
            ." WHERE liga = ".$liga
            //." AND sid = ".$sid
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

    public function _getCLMMannschaft(&$options)
    {
        //	$sid	= clm_core::$load->request_int('saison',1);
        $liga	= clm_core::$load->request_int('liga', 0);
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query = " SELECT * FROM #__clm_mannschaften "
            ." WHERE liga = ".$liga
            //." AND sid = ".$sid
            ." AND published = 1 "
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
        //	$sid	= clm_core::$load->request_int('saison',1);
        $liga	= clm_core::$load->request_int('liga', 0);
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query = " SELECT teil FROM #__clm_liga "
            ." WHERE id = ".$liga
            //." AND sid = ".$sid
        ;
        $db->setQuery($query);
        $row_tln = $db->loadObjectList();
        if (isset($row_tln[0])) {
            $tln = $row_tln[0]->teil;
        } else {
            $tln = 0;
        }

        $query = " SELECT a.*,g.id as gid, g.name as gname, g.tln_nr as gtln, g.published as gpublished, g.rankingpos as grank, "
            ." g.man_nr as gmnr, h.id as hid, h.name as hname, h.tln_nr as htln, h.rankingpos as hrank, b.wertpunkte as gwertpunkte, "
            ." h.published as hpublished, h.man_nr as hmnr "
                ." FROM #__clm_rnd_man as a"
                ." LEFT JOIN #__clm_mannschaften AS g ON g.tln_nr = a.gegner"
                ." LEFT JOIN #__clm_mannschaften AS h ON h.tln_nr = a.tln_nr"
//			." LEFT JOIN #__clm_rnd_man AS b ON b.sid = ".$sid." AND b.lid = ".$liga." AND b.runde = a.runde AND b.dg = a.dg AND b.paar = a.paar AND b.heim = 0 "
                ." LEFT JOIN #__clm_rnd_man AS b ON b.lid = ".$liga." AND b.runde = a.runde AND b.dg = a.dg AND b.paar = a.paar AND b.heim = 0 "
                ." WHERE g.liga = ".$liga
                //." AND g.sid = ".$sid
                ." AND h.liga = ".$liga
                //." AND h.sid = ".$sid
                //." AND a.sid = ".$sid
                ." AND a.lid = ".$liga
                ." AND a.heim = 1 "
                ." AND (g.man_nr > 0 OR h.man_nr > 0) "
                ." ORDER BY a.dg ASC,a.runde ASC, a.paar ASC"
        ;
        return $query;
    }
    public function getCLMPaar($options = array())
    {
        $query	= $this->_getCLMPaar($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMSumme(&$options)
    {
        //	$sid	= clm_core::$load->request_int('saison',1);
        $liga	= clm_core::$load->request_int('liga', 0);
        $runde	= clm_core::$load->request_int('runde');
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query = " SELECT a.dg,a.paar as paarung,a.runde as runde,a.brettpunkte as sum "
            ." FROM #__clm_rnd_man as a "
                ." WHERE a.lid = ".$liga
                //." AND a.sid = ".$sid
                ." ORDER BY a.dg ASC ,a.runde ASC, a.paar ASC, a.heim DESC "
        ;
        return $query;
    }
    public function getCLMSumme($options = array())
    {
        $query	= $this->_getCLMSumme($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMRundensumme(&$options)
    {
        //	$sid	= clm_core::$load->request_int('saison',1);
        $liga	= clm_core::$load->request_int('liga', 0);
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query = " SELECT a.nr, a.sl_ok as sl_ok "
            ." FROM #__clm_runden_termine as a"
                ." WHERE a.liga = ".$liga
                //." AND a.sid = ".$sid
                ." ORDER BY a.nr ASC"
        ;
        return $query;
    }
    public function getCLMRundensumme($options = array())
    {
        $query	= $this->_getCLMRundensumme($options);
        $result = $this->_getList($query);
        return @$result;
    }

}
