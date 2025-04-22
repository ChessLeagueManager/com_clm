<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class CLMModelVereinsliste extends JModelLegacy
{
    public function _getCLMVereine(&$options)
    {

        $mainframe	= JFactory::getApplication();
        $option 	= clm_core::$load->request_string('option');

        $sid	= clm_core::$load->request_int('saison', 0);
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        if (!$sid or $sid < 1) { // keine Saison vorgegeben
            // aktuelle Saison holen
            $query = 'SELECT id FROM #__clm_saison WHERE archiv=0 AND published=1 ORDER BY id DESC LIMIT 1';
            $db->setQuery($query);
            $sid = $db->loadResult();
            if (!$sid or $sid < 1) { // keine Saison aktuell !
                $sid = 1;
            }
            $_GET['saison'] = $sid;
        }

        $query = "SELECT DISTINCT b.ZPS, b.Status, a.zps, a.name, a.homepage, a.vs, a.vs_mail, c.*, d.*, "
            ." COUNT(Geschlecht) as MGL_SUM," // Mitglieder insgesamt
            ." COUNT(case Geschlecht when 'M' then 1 else NULL end) as MGL_M," // M�nnliche Mitglieder
            ." COUNT(case Geschlecht when 'W' then 1 when 'F' then 1 else NULL end) as MGL_W," // Weibliche Miglieder
            ." COUNT(case Status when 'P' then 1 else NULL end) as MGL_P," // Passive Miglieder
            ." avg(case DWZ when 0 then NULL else DWZ end) as DWZ," // DWZ Durchschnitt
            ." avg(case FIDE_Elo when 0 then NULL else FIDE_Elo end) as FIDE_Elo," // ELO Durchschnitt
            ." COUNT(case DWZ when 0 then NULL else DWZ end) as DWZ_SUM," // ELO Spieler
            ." COUNT(case FIDE_ID when 0 then NULL else FIDE_ID end) as ELO_SUM" // DWZ Spieler
            ." FROM #__clm_vereine AS a"
            ." LEFT JOIN #__clm_dwz_spieler AS b ON b.ZPS = a.zps"
            ." LEFT JOIN #__clm_dwz_vereine AS c ON c.ZPS = a.zps AND c.sid = a.sid"
            ." LEFT JOIN #__clm_dwz_verbaende AS d ON d.Verband = c.Verband"
            ." LEFT JOIN #__clm_saison AS e ON e.id = a.sid AND e.id = b.sid"
            ." WHERE a.sid = ".$sid
            ." AND b.sid = ".$sid
            ." GROUP BY b.ZPS"
        ;

        $filter_order     = $mainframe->getUserStateFromRequest($option.'filter_order_vl', 'filter_order', 'name', 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($option.'filter_order_Dir_vl', 'filter_order_Dir', '', 'word');

        if (!empty($filter_order) && !empty($filter_order_Dir)) {
            $query .= ' ORDER BY c.Verband ASC, '.$filter_order.' '.$filter_order_Dir;
        }

        //$cnt_qry = count ($query);

        return $query;
    }

    public function getCLMVereine($options = array())
    {
        $query	= $this->_getCLMVereine($options);
        $result = $this->_getList($query);
        return @$result;
    }

    public function _getCLMVerband(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', 1);
        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $db	= JFactory::getDBO();

        $query = 'SELECT  a.zps, b.*, c.*'
            ." FROM #__clm_vereine AS a"
            ." LEFT JOIN #__clm_dwz_vereine AS b ON b.ZPS = a.zps AND b.sid = a.sid"
            ." LEFT JOIN #__clm_dwz_verbaende AS c ON c.LV = b.LV"
            ." WHERE c.Uebergeordnet = 000"
            ." AND a.sid = ".$sid
        ;

        return $query;
    }

    public function getCLMVerband($options = array())
    {
        $query	= $this->_getCLMVerband($options);
        $result = $this->_getList($query);
        return @$result;
    }


    public function _getCLMVereinsliste(&$options)
    {
        $sid	= clm_core::$load->request_int('saison', 1);
        $zps	= clm_escape(clm_core::$load->request_string('zps'));
        $db	= JFactory::getDBO();
        //	$id	= @$options['id'];

        $query = 'SELECT DISTINCT a.zps, a.name, a.sid FROM #__clm_vereine as a'
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

    public function getSaisonID()
    {
        $db = JFactory::getDBO();

        $query = "SELECT id, archiv FROM #__clm_saison  WHERE archiv = 0 ";
        $db->setQuery($query);
        $saisonid = $db->loadResult();

        return $saisonid;
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
}
