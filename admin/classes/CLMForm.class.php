<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
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
class CLMForm {

	// Formularelement hidden
	function hidden ($name, $value, $id = FALSE) {
	
		$parts = array();
		$parts[] = 'name="'.$name.'"';
		$parts[] = 'value="'.$value.'"';
		
		if ($id) {
			$parts[] = 'id="'.$id.'"';
		}
		
		$add = implode (" ", $parts);
		
		return '<input type="HIDDEN" '.$add.'>';
		
	}
	
	
	function selectSeason ($name, $value = 0, $filter = FALSE) {
	
		$_db				= & JFactory::getDBO();
		
		$saisonlist[]	= JHTML::_('select.option',  '0', CLMText::selectOpener(JText::_( 'SELECT_SEASON' )), 'id', 'name' );
		
		$query = 'SELECT id, name FROM #__clm_saison WHERE archiv = 0';
		$_db->setQuery($query);
		$saisonlist		= array_merge( $saisonlist, $_db->loadObjectList() );
		
		return JHTML::_('select.genericlist', $saisonlist, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter),'id', 'name', intval($value) );
	
	}
	

	function selectModus ($name, $value = 0, $filter = FALSE, $more = '') {
	
		$modi = array();
		$modi[0] = CLMText::selectOpener(JText::_('SELECT_MODUS'));
		$modi[1] = JText::_('MODUS_TYP_1');
		$modi[2] = JText::_('MODUS_TYP_2');
		$modi[3] = JText::_('MODUS_TYP_3');
		$modi[5] = JText::_('MODUS_TYP_5');
		$modi[6] = JText::_('MODUS_TYP_6');
		foreach ($modi as $key => $val) {
			$modilist[]	= JHTML::_('select.option', $key, $val, 'id', 'name' );
		}
	
		return JHTML::_('select.genericlist', $modilist, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter).$more,'id', 'name', intval($value) );
		
	}

	
	function selectTiebreakers ($name, $value = 0, $filter = FALSE) {
	
		$modi = array();
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
		$tiebr[51] = JText::_('TIEBR_51');
		foreach ($tiebr as $key => $val) {
			$tiebrlist[]	= JHTML::_('select.option', $key, $val, 'id', 'name' );
		}
	
		return JHTML::_('select.genericlist', $tiebrlist, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter),'id', 'name', intval($value) );
		
	}
	
	
	function selectStages ($name, $value = 0, $filter = FALSE) {
	
		// $stagelist[]	= JHTML::_('select.option',  '0', CLMText::selectOpener(JText::_( 'SELECT_STAGES' )), 'id', 'name' );
		$stagelist[]	= JHTML::_('select.option',  '1', '1', 'id', 'name' );
		$stagelist[]	= JHTML::_('select.option',  '2', '2', 'id', 'name' );
		$stagelist[]	= JHTML::_('select.option',  '3', '3', 'id', 'name' );
		$stagelist[]	= JHTML::_('select.option',  '4', '4', 'id', 'name' );
	
		return JHTML::_('select.genericlist', $stagelist, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter),'id', 'name', intval($value) );
	
	}


	function selectDirector ($name, $value = 0, $filter = FALSE) {

		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'classes'.DS.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		
		/* $_db				= & JFactory::getDBO();
		// TL Liste
		//$query =  " SELECT a.jid,a.name "
		//	." FROM #__clm_user as a"
		//	." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		//	//." WHERE usertype = 'tl' OR usertype = 'admin' AND s.published = 1 "
		//	." WHERE (usertype = 'tl' OR usertype = 'admin')"   //klkl
		//	." AND s.published = 1 "                            //klkl
		//	." AND s.archiv = 0"
		//	;
		$_db->setQuery($query); */
		$clmAccess->accesspoint = 'BE_tournament_edit_result';  //nur wer wenigstens Ergebnisse im BE pflegen darf, kann SL sein
		$clmAccess->accessvalue = '>0';  						//für alle ligen oder nur ausgewählte
		if($clmAccess->userlist() === false) {
			echo "<br>cl: "; var_dump($clmAccess->userlist()); die('clcl'); }
		
		$tllist[]	= JHTML::_('select.option',  '0', CLMText::selectOpener(JText::_( 'SELECT_DIRECTOR' )), 'jid', 'name' );
		//$tllist		= array_merge( $tllist, $_db->loadObjectList() );
		$tllist		= array_merge( $tllist, $clmAccess->userlist() );
		
		return JHTML::_('select.genericlist', $tllist, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter), 'jid', 'name', $value );
	
	}


	function selectDistrict ($name, $value = '0', $filter = FALSE) {

		$_db				= & JFactory::getDBO();
		
		// Bezirksveranstaltung
		$query = "SELECT ZPS, Vereinname FROM #__clm_dwz_vereine";
		$_db->setQuery($query);
		$veranstalter[]	= JHTML::_('select.option',  '0', CLMText::selectOpener(JText::_( 'SELECT_ORGANIZER' )), 'ZPS', 'Vereinname' );
		$veranstalter[]	= JHTML::_('select.option',  '-1', JText::_( ' ---------------------------- ' ), 'ZPS', 'Vereinname' );
		$veranstalter[]	= JHTML::_('select.option',  '1', JText::_( 'DISTRICT_EVENT' ), 'ZPS', 'Vereinname' );
		$veranstalter[]	= JHTML::_('select.option',  '-1', JText::_( ' ---------------------------- ' ), 'ZPS', 'Vereinname' );
		$veranstalter	= array_merge( $veranstalter, $_db->loadObjectList() );
		
		return JHTML::_('select.genericlist', $veranstalter, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter),'ZPS', 'Vereinname', $value );

	}


	function selectAssociation ($name, $value = '0', $filter = FALSE) {
		
		$_db				= & JFactory::getDBO();
		
		// Verbandfilter
		$query = 'SELECT Verband, Verbandname FROM dwz_verbaende';
		$_db->setQuery($query);
		$verbandlist[] = JHTML::_('select.option',  '0', JText::_( 'SELECT_ASSOCIATION' ), 'Verband', 'Verbandname' );
		$verbandlist = array_merge( $verbandlist, $_db->loadObjectList() );
		
		return JHTML::_('select.genericlist', $verbandlist, $name, 'class="inputbox" size="1" onchange="document.adminForm.submit();"','Verband', 'Verbandname', $value );

	}


	function selectVereinZPS ($name, $value = NULL, $filter = FALSE) {
	
		$_db				= & JFactory::getDBO();
		
		$query = "SELECT ZPS, Vereinname FROM #__clm_dwz_vereine GROUP BY ZPS";
		$_db->setQuery($query);
		$veranstalter[]	= JHTML::_('select.option',  '0', CLMText::selectOpener(JText::_( 'SELECT_CLUB' )), 'ZPS', 'Vereinname' );
		$veranstalter	= array_merge( $veranstalter, $_db->loadObjectList() );
		
		return JHTML::_('select.genericlist', $veranstalter, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter),'ZPS', 'Vereinname', $value );
	
	}

	function selectVerband ($name, $value = NULL, $filter = FALSE) {
	
		$_db				= & JFactory::getDBO();
		
		$query = " (SELECT v2.Verband AS ZPS, v2.Verbandname AS Vereinname FROM dwz_verbaende AS v2 GROUP BY ZPS) "
				;
		$_db->setQuery($query);
		$veranstalter[]	= JHTML::_('select.option',  '0', CLMText::selectOpener(JText::_( 'SELECT_VERBAND' )), 'ZPS', 'Vereinname' );
		$veranstalter	= array_merge( $veranstalter, $_db->loadObjectList() );
		
		return JHTML::_('select.genericlist', $veranstalter, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter),'ZPS', 'Vereinname', $value );
	
	}

	function selectVereinZPSuVerband ($name, $value = NULL, $filter = FALSE) {
	
		$_db				= & JFactory::getDBO();
		
		$query = " (SELECT v2.Verband AS ZPS, v2.Verbandname AS Vereinname FROM dwz_verbaende AS v2 GROUP BY ZPS) "

				." UNION ALL "
				
				." (SELECT v1.ZPS AS ZPS, v1.Vereinname AS Vereinname FROM #__clm_dwz_vereine AS v1 WHERE sid = ".CLM_SEASON." GROUP BY ZPS) "
 
				;
		$_db->setQuery($query);
		$veranstalter[]	= JHTML::_('select.option',  '0', CLMText::selectOpener(JText::_( 'SELECT_HOST' )), 'ZPS', 'Vereinname' );
		$veranstalter	= array_merge( $veranstalter, $_db->loadObjectList() );
		
		return JHTML::_('select.genericlist', $veranstalter, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter),'ZPS', 'Vereinname', $value );
	
	}

	function selectVereinZPSinAssoc ($name, $value = NULL, $verband = '000', $filter = FALSE) {
	
		$_db				= & JFactory::getDBO();
		
		if ($verband != '000') {
			$temp = $verband;
			WHILE (substr($temp, -1) == '0') {
				$temp = substr_replace($temp, "", -1);
			}
			$query = "SELECT ZPS, Vereinname FROM #__clm_dwz_vereine WHERE Verband LIKE '".$temp."%' AND sid = ".CLM_SEASON;
		} else {
			$query = "SELECT ZPS, Vereinname FROM #__clm_dwz_vereine WHERE sid = ".CLM_SEASON;
		}
		$_db->setQuery($query);
		$veranstalter[]	= JHTML::_('select.option',  '0', CLMText::selectOpener(JText::_( 'SELECT_CLUB' )), 'ZPS', 'Vereinname' );
		$veranstalter	= array_merge( $veranstalter, $_db->loadObjectList() );
		
		return JHTML::_('select.genericlist', $veranstalter, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter),'ZPS', 'Vereinname', $value );
	
	}


	function radioPublished ($name, $value = 0) {
	
		return JHTML::_('select.booleanlist', $name, 'class="inputbox"', $value);
	
	}


	// ersetzt CLMFilterVerein::filter_vereine, aber ohne Option von $vl = 1, also nur DB-Auswahl
	function selectVerein ($name, $value = 0, $filter = FALSE) {
		
		$_db				= & JFactory::getDBO();
	
		$config	= &JComponentHelper::getParams( 'com_clm' );
		$lv	= $config->get('lv', 705);
		$vl	= $config->get('vereineliste', 1);
		$vs	= $config->get('verein_sort', 1);
		$version = $config->get('version', 0);
		$dat	= substr($lv, 1);
		$dat2	= substr($lv, 2);
	
		// 1 = Auswahl DB obwohl manuell aktiviert wurde ! (z.B. Vereine anlegen !!!)
		/*
		if ($override == 1) {
			$vl = 0;
		}
		*/
	
		// Vereinefilter
		// 0 = DB ; 1 = manuell
		// if ($vl =="0") {
			$sql = 'SELECT id FROM #__clm_saison WHERE archiv = 0 and published = 1';
			$_db->setQuery($sql);
			$sid = $_db->loadResult();
	
			if ($version == "0") {
				if ($dat == "00") {
					$ug = (substr($lv, 0, 1)).'0000';
					$og = (substr($lv, 0, 1)).'9999';
				}
				if ($dat2 =="0" AND $dat !="00") {
					$ug = (substr($lv, 0, 2)).'000';
					$og = (substr($lv, 0, 2)).'999';
				}
				if ($dat2 !="0" AND $dat !="00") {
					$ug =$lv.'00';
					$og =$lv.'99';
				}
			}
		
			if($version == "1"){
				if($lv=="00") {
					$ug =$lv;
					$og ="99";
				} else {
					$ug =$lv;
					$og =$lv;
				}
			}
			$sql = "SELECT ZPS as zps, Vereinname as name FROM #__clm_dwz_vereine as a "
				." LEFT JOIN #__clm_saison as s ON s.id= a.sid "
				." WHERE a.ZPS BETWEEN '$ug' AND '$og' "
				." AND s.archiv = 0 AND s.published = 1 ORDER BY ";
			
			if ($vs =="1") { 
				$sql =$sql."a.ZPS ASC";
			} else {
				$sql = $sql." a.Vereinname ASC";
			}
		/*
		} else {
			$sql = 'SELECT a.zps, a.name FROM #__clm_vereine as a'
				.' LEFT JOIN #__clm_saison AS s ON s.id = a.sid'
				." WHERE s.archiv = 0";
		}
		*/
		$_db->setQuery($sql);
		$vereine = $_db->loadObjectList();
	
		/*
		// Hinweis setzen wenn Filter leer !
		if (count($vereine) == 0 AND $vl == 1) {
		JError::raiseWarning( 500,  JText::_( ' Vereineliste (Filter) ist leer !'));
		JError::raiseNotice( 6000,  JText::_( ' Ursache : Es wurde kein Verein angelegt und die Auswahl steht auf manuell !'));
		}
		if (count($vereine) == 0 AND $vl == 0) {
		JError::raiseWarning( 500,  JText::_( ' Vereineliste (Filter) ist leer !'));
		JError::raiseNotice( 6000,  JText::_( ' Ursache : Die Datenbank enthält keinen Verein dieses Verbandes und die Auswahl ist auf Datenbank eingestellt ! Falsche Verbandeinstellungen !?!'));
		}
		*/
	
		$vlist[]	= JHTML::_('select.option',  '0', CLMText::selectOpener(JText::_( 'SELECT_CLUB' )), 'zps', 'name' );
		$vlist		= array_merge( $vlist, $vereine);
	
		return JHTML::_('select.genericlist', $vlist, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter), 'zps', 'name', $value );
	
	}

	function selectMatchPlayer($name, $selected, $players = array()) {
	
		$pllist[] = JHTML::_('select.option', '0', CLMText::selectOpener(JText::_( 'SELECT_PLAYER_'.strtoupper(substr($name, 0, 1)) ) ), 'snr', 'name' );
		$pllist[] = JHTML::_('select.option', '-1', JText::_( ' ---------------------------- ' ), 'snr', 'name');
	
		foreach ($players as $key => $value) {
			$pllist[]	= JHTML::_('select.option', $value['snr'], $value['snr']." - ".$value['name'], 'snr', 'name');
		}
	
		return JHTML::_('select.genericlist', $pllist, $name, 'class="inputbox" size="1"', 'snr', 'name', $selected );
	
	}


	function selectMatchResult($name, $value) {
	
		$resultlist[] = JHTML::_('select.option', '-1', CLMText::selectOpener(JText::_( 'SELECT_RESULT' ) ), 'eid', 'ergebnis' );
		$resultlist[] = JHTML::_('select.option', '-2', JText::_( ' ---------------- ' ), 'eid', 'ergebnis');
		
		for ($r=0; $r<=8; $r++) {
			$resultlist[] = JHTML::_('select.option', $r, JText::_( 'RESULT_EID_'.$r ), 'eid', 'ergebnis' );
		}
	
		return JHTML::_('select.genericlist', $resultlist, $name, 'class="inputbox" size="1"', 'eid', 'ergebnis', $value );
	
	}

	function selectDWZRanges ($name, $value = 0, $filter = FALSE) {
	
		$dwzlist[]	= JHTML::_('select.option',  '0', CLMText::selectOpener(JText::_( 'SELECT_RATING' )), 'id', 'name' );
		$dwzlist[]	= JHTML::_('select.option',  '28', 'DWZ >= 2600', 'id', 'name' );
		for ($r=26; $r>=10; $r-=2) {
			$dwzlist[]	= JHTML::_('select.option',  $r, 'DWZ < '.($r*100), 'id', 'name' );
		}
	
		return JHTML::_('select.genericlist', $dwzlist, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter),'id', 'name', intval($value) );
	
	}


	function selectPriority ($name, $value = 0, $filter = FALSE) {
	
		$list[]	= JHTML::_('select.option',  '0', CLMText::selectOpener(JText::_( 'SELECT_PRIORITY' )), 'id', 'name' );
		
		for ($p=35; $p>=5; $p-=5) {
			$list[]	= JHTML::_('select.option',  $p, JText::_('TODO_PRIO_'.$p), 'id', 'name' );
		}
	
		return JHTML::_('select.genericlist', $list, $name, 'class="inputbox" size="1"'.CLMText::stringOnchange($filter),'id', 'name', intval($value) );
	
	}

}
?>