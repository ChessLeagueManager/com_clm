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

class CLMModelVerein extends JModelLegacy
{
    public function _getCLMVereinstats(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', 1);
        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query = " SELECT a.ZPS, a.sid, a.Geschlecht, a.DWZ, a.FIDE_Elo, a.FIDE_ID,"
            ." COUNT(Geschlecht) as Mgl,"
            ." COUNT(case Geschlecht when 'M' then 1 else NULL end) as Mgl_m," // Männliche Mitglieder
            ." COUNT(case Geschlecht when 'W' then 1 when 'F' then 1 else NULL end) as Mgl_w," // Weibliche Miglieder
            ." avg(case DWZ when 0 then NULL else DWZ end) as DWZ," // DWZ Durchschnitt
            ." avg(case FIDE_Elo when 0 then NULL else FIDE_Elo end) as FIDE_Elo," // ELO Durchschnitt
            ." COUNT(case DWZ when 0 then NULL else DWZ end) as DWZ_SUM," // ELO Spieler
            ." COUNT(case FIDE_ID when 0 then NULL else FIDE_ID end) as ELO_SUM" // DWZ Spieler
            ." FROM #__clm_dwz_spieler as a "
            ." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
            ." WHERE a.ZPS = '$zps'"
            ." AND a.sid = ".$sid
            ." GROUP BY a.ZPS"
        ;
        return $query;
    }

    public function getCLMVereinstats($options = array())
    {
        $query	= $this->_getCLMVereinstats($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMVerein(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', 1);
        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query = " SELECT a.* "
            ." FROM #__clm_vereine as a "
            ." WHERE a.zps = '$zps'"
            ." AND a.sid = ".$sid
            ." AND a.published = 1"
        ;
        return $query;
    }



    public function getCLMVerein($options = array())
    {
        $query	= $this->_getCLMVerein($options);
        $result = $this->_getList($query);
        if (is_array($result) and count($result) == 1) {
            //Adress Handling
            $addressHandler = new AddressHandler();
            $addressHandler->queryLocation($result, 1);
        }
        return @$result;
    }

    public function _getCLMMannschaft(&$options)
    {
        $sid	= clm_core::$load->request_int('saison');
        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query = "SELECT a.*, l.name as liga_name "
            ." FROM #__clm_mannschaften as a "
            ." LEFT JOIN #__clm_liga as l on l.id = a.liga AND l.sid = a.sid "
            ." WHERE a.zps = '$zps'"
            ." AND a.sid = ".$sid
            ." AND a.published = 1 AND l.published = 1 "
            ." ORDER BY a.man_nr ASC "
        ;
        return $query;
    }

    public function getCLMMannschaft($options = array())
    {
        $query	= $this->_getCLMMannschaft($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMVereinsliste(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', 1);
        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query  = 'SELECT DISTINCT a.zps, a.name, a.published FROM #__clm_vereine as a'
            .' WHERE published = 1'
            ." AND a.sid = ".$sid
            .' ORDER BY a.name ASC '
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
        $sid	= clm_core::$load->request_int('saison', 1);
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

    public function _getCLMTurniere(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', 1);
        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query  = ' SELECT a.name, a.id, a.sid, a.vereinZPS FROM #__clm_turniere AS a'
            ." WHERE a.vereinZPS = '$zps'"
            ." AND a.sid = ".$sid
            ." ORDER BY a.name DESC "
        ;

        return $query;
    }

    public function getCLMTurniere($options = array())
    {
        $query	= $this->_getCLMTurniere($options);
        $result = $this->_getList($query);
        return @$result;
    }


    public function _getCLMData(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', 1);
        $zps	= clm_escape(clm_core::$load->request_string('zps'));

        $db			= JFactory::getDBO();
        //		$id			= @$options['id'];

        $query	= "SELECT * "
            ." FROM #__clm_vereine "
            ." WHERE zps = '$zps' "
            ." AND sid = ". $sid
        ;

        return $query;
    }

    public function getCLMData($options = array())
    {
        $query	= $this->_getCLMData($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMName(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', 1);
        $zps	= clm_escape(clm_core::$load->request_string('zps'));

        $db			= JFactory::getDBO();
        //		$id			= @$options['id'];

        $query	= "SELECT Vereinname "
            ." FROM #__clm_dwz_vereine "
            ." WHERE zps = '$zps' "
            ." AND sid = ". $sid
        ;

        return $query;
    }

    public function getCLMName($options = array())
    {
        $query	= $this->_getCLMName($options);
        $result = $this->_getList($query);
        return @$result;
    }

    ////// Prüfen ob User berechtigt ist Daten zu ändern ///////////////////////////////////
    public function _getCLMClmuser(&$options)
    {
        $user = JFactory::getUser();
        $jid = $user->get('id');
        $sid	= clm_core::$load->request_int('saison', 1);

        $db			= JFactory::getDBO();
        //		$id			= @$options['id'];

        $query	= "SELECT * "
            ." FROM #__clm_user "
            ." WHERE jid = $jid "
            ." AND sid = " .$sid
        ;

        return $query;
    }

    public function getCLMClmuser($options = array())
    {
        $query	= $this->_getCLMClmuser($options);
        $result = $this->_getList($query);
        return @$result;
    }



}
