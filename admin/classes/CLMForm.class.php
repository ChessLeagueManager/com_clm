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

/**
 * Klassenbibliothek CLMForm für verschiedene Eingabemasken in Formularen
 * jede Methode benötigt als ersten Parameter die Übergabe des Namens des Formularwertes ($name)
 * optional als zweiter Parameter der voreingestellte Wert ($value)
 * Verwendung: CLMForm::methodenname('name', 'wert');
*/
class CLMForm
{
    // Formularelement hidden
    public static function hidden($name, $value, $id = false)
    {

        $parts = array();
        $parts[] = 'name="'.$name.'"';
        $parts[] = 'value="'.$value.'"';

        if ($id) {
            $parts[] = 'id="'.$id.'"';
        }

        $add = implode(" ", $parts);

        return '<input type="HIDDEN" '.$add.'>';

    }


    public static function selectSeason($name, $value = 0, $filter = false, $only = true)
    {

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $_db				= JFactory::getDBO();

        $saisonlist[]	= JHTML::_('select.option', '0', CLMText::selectOpener(JText::_('SELECT_SEASON')), 'id', 'name');
        if ($only === true) {
            $query = 'SELECT id, name FROM #__clm_saison WHERE archiv = 0';
        } elseif ($only > 0 and $only < 1000) {
            $query = 'SELECT id, name FROM #__clm_saison WHERE id = '.$only;
        } else {
            $query = 'SELECT id, name FROM #__clm_saison';
        }
        $_db->setQuery($query);
        $saisonlist		= array_merge($saisonlist, $_db->loadObjectList());

        //		return JHTML::_('select.genericlist', $saisonlist, $name, 'class="js-example-basic-single" style="width:350px" size="1"'.CLMText::stringOnchange($filter),'id', 'name', intval($value) );
        return JHTML::_('select.genericlist', $saisonlist, $name, 'class="'.$field_search.'" style="width:350px" size="1"'.CLMText::stringOnchange($filter), 'id', 'name', intval($value));

    }


    public static function selectModus($name, $value = 0, $filter = false, $more = '')
    {

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $modi = array();
        $modi[0] = CLMText::selectOpener(JText::_('SELECT_MODUS'));
        $modi[1] = JText::_('MODUS_TYP_1');
        $modi[2] = JText::_('MODUS_TYP_2');
        $modi[3] = JText::_('MODUS_TYP_3');
        $modi[5] = JText::_('MODUS_TYP_5');
        $modi[6] = JText::_('MODUS_TYP_6');
        foreach ($modi as $key => $val) {
            $modilist[]	= JHTML::_('select.option', $key, $val, 'id', 'name');
        }

        //		return JHTML::_('select.genericlist', $modilist, $name, 'class="js-example-basic-single" style="width:350px" size="1"'.CLMText::stringOnchange($filter).$more,'id', 'name', intval($value) );
        return JHTML::_('select.genericlist', $modilist, $name, 'class="'.$field_search.'" style="width:350px" size="1"'.CLMText::stringOnchange($filter).$more, 'id', 'name', intval($value));

    }


    public static function selectTiebreakers($name, $value = 0, $filter = false)
    {

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $tiebr = array();
        $tiebr[0] = CLMText::selectOpener(JText::_('SELECT_TIEBREAKER'));
        $tiebr[1] = JText::_('TIEBR_1');
        $tiebr[11] = JText::_('TIEBR_11');
        $tiebr[2] = JText::_('TIEBR_2');
        $tiebr[12] = JText::_('TIEBR_12');
        $tiebr[3] = JText::_('TIEBR_3');
        $tiebr[13] = JText::_('TIEBR_13');
        $tiebr[4] = JText::_('TIEBR_4');
        $tiebr[5] = JText::_('TIEBR_5');
        //$tiebr[15] = JText::_('TIEBR_15');
        $tiebr[6] = JText::_('TIEBR_6');
        $tiebr[16] = JText::_('TIEBR_16');
        $tiebr[7] = JText::_('TIEBR_7');
        $tiebr[8] = JText::_('TIEBR_8');
        $tiebr[18] = JText::_('TIEBR_18');
        $tiebr[9] = JText::_('TIEBR_9');
        $tiebr[19] = JText::_('TIEBR_19');
        $tiebr[25] = JText::_('TIEBR_25');
        $tiebr[29] = JText::_('TIEBR_29');
        $tiebr[30] = JText::_('TIEBR_30');
        $tiebr[51] = JText::_('TIEBR_51');
        foreach ($tiebr as $key => $val) {
            $tiebrlist[]	= JHTML::_('select.option', $key, $val, 'id', 'name');
        }

        //		return JHTML::_('select.genericlist', $tiebrlist, $name, 'class="js-example-basic-single" size="1" style="width:350px;"'.CLMText::stringOnchange($filter),'id', 'name', intval($value) );
        return JHTML::_('select.genericlist', $tiebrlist, $name, 'class="'.$field_search.'" size="1" style="width:350px;"'.CLMText::stringOnchange($filter), 'id', 'name', intval($value));

    }


    public static function selectStages($name, $value = 0, $filter = false)
    {

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        // $stagelist[]	= JHTML::_('select.option',  '0', CLMText::selectOpener(JText::_( 'SELECT_STAGES' )), 'id', 'name' );
        $stagelist[]	= JHTML::_('select.option', '1', '1', 'id', 'name');
        $stagelist[]	= JHTML::_('select.option', '2', '2', 'id', 'name');
        $stagelist[]	= JHTML::_('select.option', '3', '3', 'id', 'name');
        $stagelist[]	= JHTML::_('select.option', '4', '4', 'id', 'name');

        //		return JHTML::_('select.genericlist', $stagelist, $name, 'class="js-example-basic-single" size="1" style="width:50px"'.CLMText::stringOnchange($filter),'id', 'name', intval($value) );
        return JHTML::_('select.genericlist', $stagelist, $name, 'class="'.$field_search.'" size="1" style="width:50px"'.CLMText::stringOnchange($filter), 'id', 'name', intval($value));

    }


    public static function selectDirector($name, $value = 0, $filter = false)
    {

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $clmAccess = clm_core::$access;

        $userlist = $clmAccess->userlist('BE_tournament_edit_result', '>0');

        if ($userlist === false) {
            echo "<br>cl: ";
            var_dump($userlist);
            die('clcl');
        }

        if ($name == 'torg') {
            $tllist[]	= JHTML::_('select.option', '0', CLMText::selectOpener(JText::_('SELECT_ORGANIZER')), 'jid', 'name');
        } else {
            $tllist[]	= JHTML::_('select.option', '0', CLMText::selectOpener(JText::_('SELECT_DIRECTOR')), 'jid', 'name');
        }
        $tllist		= array_merge($tllist, $userlist);

        //		return JHTML::_('select.genericlist', $tllist, $name, 'class="js-example-basic-single" style="width:300px" size="1"'.CLMText::stringOnchange($filter), 'jid', 'name', $value );
        return JHTML::_('select.genericlist', $tllist, $name, 'class="'.$field_search.'" style="width:300px" size="1"'.CLMText::stringOnchange($filter), 'jid', 'name', $value);

    }


    public static function selectDistrict($name, $value = '0', $filter = false)
    {

        $_db				= JFactory::getDBO();

        // Bezirksveranstaltung
        $query = "SELECT ZPS, Vereinname FROM #__clm_dwz_vereine";
        $_db->setQuery($query);
        $veranstalter[]	= JHTML::_('select.option', '0', CLMText::selectOpener(JText::_('SELECT_ORGANIZER')), 'ZPS', 'Vereinname');
        $veranstalter[]	= JHTML::_('select.option', '-1', JText::_(' ---------------------------- '), 'ZPS', 'Vereinname');
        $veranstalter[]	= JHTML::_('select.option', '1', JText::_('DISTRICT_EVENT'), 'ZPS', 'Vereinname');
        $veranstalter[]	= JHTML::_('select.option', '-1', JText::_(' ---------------------------- '), 'ZPS', 'Vereinname');
        $veranstalter	= array_merge($veranstalter, $_db->loadObjectList());

        return JHTML::_('select.genericlist', $veranstalter, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter), 'ZPS', 'Vereinname', $value);

    }


    public static function selectAssociation($name, $value = '0', $filter = false)
    {

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $_db				= JFactory::getDBO();

        // Verbandfilter
        $query = 'SELECT Verband, Verbandname FROM #__clm_dwz_verbaende';
        $_db->setQuery($query);
        $verbandlist[] = JHTML::_('select.option', '0', JText::_('SELECT_ASSOCIATION'), 'Verband', 'Verbandname');
        $verbandlist = array_merge($verbandlist, $_db->loadObjectList());

        //		return JHTML::_('select.genericlist', $verbandlist, $name, 'class="js-example-basic-single" size="1" onchange="document.adminForm.submit();"','Verband', 'Verbandname', $value );
        return JHTML::_('select.genericlist', $verbandlist, $name, 'class="'.$field_search.'" size="1" onchange="document.adminForm.submit();"', 'Verband', 'Verbandname', $value);

    }


    public static function selectVereinZPS($name, $value = null, $filter = false)
    {

        $_db				= JFactory::getDBO();

        $query = "SELECT ZPS, Vereinname FROM #__clm_dwz_vereine GROUP BY ZPS";
        $_db->setQuery($query);
        $veranstalter[]	= JHTML::_('select.option', '0', CLMText::selectOpener(JText::_('SELECT_CLUB')), 'ZPS', 'Vereinname');
        $veranstalter	= array_merge($veranstalter, $_db->loadObjectList());

        return JHTML::_('select.genericlist', $veranstalter, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter), 'ZPS', 'Vereinname', $value);

    }

    public static function selectVerband($name, $value = null, $filter = false)
    {

        $_db				= JFactory::getDBO();

        $query = " (SELECT v2.Verband AS ZPS, v2.Verbandname AS Vereinname FROM #__clm_dwz_verbaende AS v2 GROUP BY ZPS) "
        ;
        $_db->setQuery($query);
        $veranstalter[]	= JHTML::_('select.option', '0', CLMText::selectOpener(JText::_('SELECT_VERBAND')), 'ZPS', 'Vereinname');
        $veranstalter	= array_merge($veranstalter, $_db->loadObjectList());

        return JHTML::_('select.genericlist', $veranstalter, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter), 'ZPS', 'Vereinname', $value);

    }

    public static function selectVereinZPSuVerband($name, $value = null, $filter = false)
    {

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $_db				= JFactory::getDBO();

        $query = " (SELECT v2.Verband AS ZPS, v2.Verbandname AS Vereinname FROM #__clm_dwz_verbaende AS v2 GROUP BY ZPS) "

                ." UNION ALL "

                ." (SELECT v1.ZPS AS ZPS, v1.Vereinname AS Vereinname FROM #__clm_dwz_vereine AS v1 WHERE sid = ".clm_core::$access->getSeason()." GROUP BY ZPS) "

        ;
        $_db->setQuery($query);
        $veranstalter[]	= JHTML::_('select.option', '0', CLMText::selectOpener(JText::_('SELECT_HOST')), 'ZPS', 'Vereinname');
        $veranstalter	= array_merge($veranstalter, $_db->loadObjectList());

        //		return JHTML::_('select.genericlist', $veranstalter, $name, 'class="js-example-basic-single" style="width:300px" size="1"'.CLMText::stringOnchange($filter),'ZPS', 'Vereinname', $value );
        return JHTML::_('select.genericlist', $veranstalter, $name, 'class="'.$field_search.'" style="width:300px" size="1"'.CLMText::stringOnchange($filter), 'ZPS', 'Vereinname', $value);

    }

    public static function selectVereinZPSinAssoc($name, $value = null, $verband = '000', $filter = false)
    {

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $_db				= JFactory::getDBO();

        if ($verband != '000') {
            $temp = $verband;
            while (substr($temp, -1) == '0') {
                $temp = substr_replace($temp, "", -1);
            }
            $query = "SELECT ZPS, Vereinname FROM #__clm_dwz_vereine WHERE Verband LIKE '".$temp."%' AND sid = ".clm_core::$access->getSeason();
        } else {
            $query = "SELECT ZPS, Vereinname FROM #__clm_dwz_vereine WHERE sid = ".clm_core::$access->getSeason();
        }
        $_db->setQuery($query);
        $veranstalter[]	= JHTML::_('select.option', '0', CLMText::selectOpener(JText::_('SELECT_CLUB')), 'ZPS', 'Vereinname');
        $veranstalter	= array_merge($veranstalter, $_db->loadObjectList());

        //		return JHTML::_('select.genericlist', $veranstalter, $name, 'class="js-example-basic-single" size="1"'.CLMText::stringOnchange($filter),'ZPS', 'Vereinname', $value );
        return JHTML::_('select.genericlist', $veranstalter, $name, 'class="'.$field_search.'" size="1"'.CLMText::stringOnchange($filter), 'ZPS', 'Vereinname', $value);

    }


    public static function radioPublished($name, $value = 0)
    {

        return JHTML::_('select.booleanlist', $name, 'class="inputbox"', $value);

    }

    // ersetzt CLMFilterVerein::filter_vereine, aber ohne Option von $vl = 1, also nur DB-Auswahl
    public static function selectVerein($name, $value = 0, $filter = false)
    {

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $_db				= JFactory::getDBO();

        $config = clm_core::$db->config();
        $lv	= $config->lv;
        $vl	= $config->vereineliste;
        $vs	= $config->verein_sort;
        $sid = clm_core::$access->getSeason();
        $countryversion = $config->countryversion;

        if ($countryversion == "de") {
            $out = clm_core::$load->unit_range($lv);
        }

        $sql = "SELECT ZPS as zps, Vereinname as name FROM #__clm_dwz_vereine as a "
            ." LEFT JOIN #__clm_saison as s ON s.id= a.sid "
            ." WHERE a.Verband >= '$out[0]' AND a.Verband <= '$out[1]' "
            ." AND s.id = '$sid' ORDER BY ";
        if ($vs == "1") {
            $sql = $sql."a.ZPS ASC";
        } else {
            $sql = $sql." a.Vereinname ASC";
        }

        $_db->setQuery($sql);
        $vereine = $_db->loadObjectList();

        $vlist[]	= JHTML::_('select.option', '0', CLMText::selectOpener(JText::_('SELECT_CLUB')), 'zps', 'name');
        $vlist		= array_merge($vlist, $vereine);

        //		return JHTML::_('select.genericlist', $vlist, $name, 'class="js-example-basic-single" size="1"'.CLMText::stringOnchange($filter), 'zps', 'name', $value );
        return JHTML::_('select.genericlist', $vlist, $name, 'class="'.$field_search.'" size="1"'.CLMText::stringOnchange($filter), 'zps', 'name', $value);

    }


    // neu
    public static function selectVereinTournament($name, $value = 0, $turnier = 0, $filter = false)
    {

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $sql = "SELECT a.zps, v.Vereinname as name FROM #__clm_turniere_tlnr as a "
            ." LEFT JOIN #__clm_dwz_vereine as v ON v.sid= a.sid AND v.ZPS = a.zps"
            ." WHERE a.turnier = '$turnier'"
            ." GROUP BY v.Vereinname, a.zps";

        $vereine = clm_core::$db->loadObjectList($sql);
        foreach ($vereine as $verein) {
            if (is_null($verein->name)) {
                $verein->name = "";
            }
        }

        $vlist[]	= JHTML::_('select.option', '0', CLMText::selectOpener(JText::_('SELECT_CLUB')), 'zps', 'name');
        $vlist		= array_merge($vlist, $vereine);

        //		return JHTML::_('select.genericlist', $vlist, $name, 'class="js-example-basic-single" size="1"'.CLMText::stringOnchange($filter), 'zps', 'name', $value );
        return JHTML::_('select.genericlist', $vlist, $name, 'class="'.$field_search.'" size="1"'.CLMText::stringOnchange($filter), 'zps', 'name', $value);

    }

    public static function selectMatchPlayer($name, $selected, $players = array())
    {

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $pllist[] = JHTML::_('select.option', '0', CLMText::selectOpener(JText::_('SELECT_PLAYER_'.strtoupper(substr($name, 0, 1)))), 'snr', 'name');
        $pllist[] = JHTML::_('select.option', '-1', JText::_(' ---------------------------- '), 'snr', 'name');

        foreach ($players as $key => $value) {
            $pllist[]	= JHTML::_('select.option', $value['snr'], $value['snr']." - ".$value['name'], 'snr', 'name');
        }

        //		return JHTML::_('select.genericlist', $pllist, $name, 'class="js-example-basic-single" size="1" style="width:250px"', 'snr', 'name', $selected );
        return JHTML::_('select.genericlist', $pllist, $name, 'class="'.$field_search.'" size="1" style="width:250px"', 'snr', 'name', $selected);

    }


    public static function selectMatchResult($name, $value)
    {

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $resultlist[] = JHTML::_('select.option', '-1', CLMText::selectOpener(JText::_('SELECT_RESULT')), 'eid', 'ergebnis');
        $resultlist[] = JHTML::_('select.option', '-2', JText::_(' ---------------- '), 'eid', 'ergebnis');

        for ($r = 0; $r <= 13; $r++) {
            $resultlist[] = JHTML::_('select.option', $r, JText::_('RESULT_EID_'.$r), 'eid', 'ergebnis');
        }

        //		return JHTML::_('select.genericlist', $resultlist, $name, 'class="js-example-basic-single" size="1" style="width:170px"', 'eid', 'ergebnis', $value );
        return JHTML::_('select.genericlist', $resultlist, $name, 'class="'.$field_search.'" size="1" style="width:170px"', 'eid', 'ergebnis', $value);

    }

    public static function selectDWZRanges($name, $value = 0, $filter = false)
    {

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $countryversion = $clm_config->countryversion;
        if ($countryversion == "de") {
            $dwzlist[]	= JHTML::_('select.option', '0', CLMText::selectOpener(JText::_('SELECT_RATING')), 'id', 'name');
            $dwzlist[]	= JHTML::_('select.option', '56', 'DWZ >= 2600', 'id', 'name');
            for ($r = 52; $r >= 20; $r -= 4) {
                $dwzlist[]	= JHTML::_('select.option', $r, 'DWZ < '.($r * 50), 'id', 'name');
            }
        } else {	// engl.Anwendung
            $dwzlist[]	= JHTML::_('select.option', '0', CLMText::selectOpener(JText::_('SELECT_RATING_GRADE')), 'id', 'name');
            //$dwzlist[]	= JHTML::_('select.option',  '6', 'grade >= 250', 'id', 'name' );
            for ($r = 5; $r >= 1; $r -= 1) {
                $dwzlist[]	= JHTML::_('select.option', $r, 'grade < '.($r * 50), 'id', 'name');
            }
        }

        //		return JHTML::_('select.genericlist', $dwzlist, $name, 'class="js-example-basic-single" size="1"'.CLMText::stringOnchange($filter),'id', 'name', intval($value) );
        return JHTML::_('select.genericlist', $dwzlist, $name, 'class="'.$field_search.'" size="1"'.CLMText::stringOnchange($filter), 'id', 'name', intval($value));

    }


    public static function selectPriority($name, $value = 0, $filter = false)
    {

        $list[]	= JHTML::_('select.option', '0', CLMText::selectOpener(JText::_('SELECT_PRIORITY')), 'id', 'name');

        for ($p = 35; $p >= 5; $p -= 5) {
            $list[]	= JHTML::_('select.option', $p, JText::_('TODO_PRIO_'.$p), 'id', 'name');
        }

        return JHTML::_('select.genericlist', $list, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter), 'id', 'name', intval($value));

    }


    public static function calendar($value, $name, $id, $format = '%Y-%m-%d', $attribs = null)
    {
        if ($value == '0000-00-00' or $value == '1970-01-01') {
            $value = '';
        }

        return JHTML::_('calendar', $value, $name, $id, $format, $attribs);
    }


    /**
     * copy of Joomla 3.9  function state  in libraries/cms/html/grid.php  	line231ff
     *
     * Method to create a select list of states for filtering
     * By default the filter shows only published and unpublished items
     *
     * @param   string  $filterState  The initial filter state
     * @param   string  $published    The JText string for published
     * @param   string  $unpublished  The JText string for Unpublished
     * @param   string  $archived     The JText string for Archived
     * @param   string  $trashed      The JText string for Trashed
     *
     * @return  string
     *
     * @since   1.5
     */
    public static function selectState($filterState = '*', $published = 'JPUBLISHED', $unpublished = 'JUNPUBLISHED', $archived = null, $trashed = null)
    {
        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $state = array('' => '- ' . JText::_('JLIB_HTML_SELECT_STATE') . ' -', 'P' => JText::_($published), 'U' => JText::_($unpublished));

        if ($archived) {
            $state['A'] = JText::_($archived);
        }

        if ($trashed) {
            $state['T'] = JText::_($trashed);
        }

        return JHtml::_(
            'select.genericlist',
            $state,
            'filter_state',
            array(
                'list.attr' => 'class="'.$field_search.'" size="1" onchange="Joomla.submitform();"',
                'list.select' => $filterState,
                'option.key' => null,
            )
        );
    }

}
