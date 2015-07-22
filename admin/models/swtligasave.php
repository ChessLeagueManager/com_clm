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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class CLMModelSWTLigasave extends JModel {

    function __construct () {

        parent::__construct ();

		// Mannschafts-Nummer fuer update
		// ...
        // $db_man_nr  = ...
		
        // Daten an View weitergeben
        
    }
    
    /*	function leereRundenErstellen ($letzteRunde, $letzterDgang) {
		
		// DB-Zugriff
		$db		=& JFactory::getDBO ();
		//$row	=& JTable::getInstance ('ligenSWT', 'TableCLM');
		
		$swt_id	= JRequest::getVar ('swt_id', 0, 'default', 'int');
		$sid	= JRequest::getVar ('sid', 0, 'default', 'int');
		
		// zuvor in der DB gespeicherte Daten und (allgemeine) SWT-Daten
		$swt_data		= $this->getDataSWT ();
		$swt_db_data	= $this->getDataSWTdb ();

		$sieg		= $swt_db_data['sieg'];
		$remis		= $swt_db_data['remis'];
		$nieder		= $swt_db_data['nieder'];
		$antritt	= $swt_db_data['antritt'];
		
		$man_sieg		= $swt_db_data['man_sieg'];
		$man_remis		= $swt_db_data['man_remis'];
		$man_nieder		= $swt_db_data['man_nieder'];
		$man_antritt	= $swt_db_data['man_antritt'];
		
		$anz_mannschaften	= $swt_db_data['anz_mannschaften'];
		$anz_bretter		= $swt_db_data['anz_bretter'];
		$anz_durchgaenge	= $swt_db_data['anz_durchgaenge'];
		$anz_runden			= $swt_db_data['anz_runden'];

		$anz_paarungen = ceil ($anz_mannschaften / 2);
		
		$m_fields = '`sid`, `swt_id`, `runde`, `paar`, `dg`, `comment`, '
					. '`heim`, `tln_nr`, `gegner`, `brettpunkte`, `manpunkte`, `published`';
		
		$sp_fields = '`sid`, `swt_id`, `runde`, `paar`, `dg`, '
					. '`tln_nr`, `brett`, `heim`, `weiss`, `spieler`, `zps`, `gegner`, `gzps`, '
					. '`ergebnis`, `kampflos`, `punkte`';
		
		for ($p = 1; $p <= $anz_paarungen; $p++) {
			
			$htln_nr = $swt_data[$p]['heim']; // tln_nr der Heimmannschaft
			$gtln_nr = $swt_data[$p]['gast']; // tln_nr der Gastmannschaft
			
			$hmanpunkte = 0;
			$gmanpunkte = 0;
			
			$hbrettpunkte = 0;
			$gbrettpunkte = 0;
			
			for ($b = 1; $b <= $anz_bretter; $b++) {
			
				// Einzelergebnisse 
				$hbrett = JRequest::getVar ('hbrett_'.$p.'_'.$b);
				$gbrett = JRequest::getVar ('gbrett_'.$p.'_'.$b);
				
				$hfarbe		= JRequest::getVar ('hfarbe_'.$p.'_'.$b);
				$hweiss		= ($hfarbe == 'w');
				$hspieler	= $this->findSpieler ($hbrett);
				$hmgl_nr	= $hspieler->mgl_nr;
				$hzps		= $hspieler->zps;
				
				$gfarbe		= JRequest::getVar ('gfarbe_'.$p.'_'.$b);
				$gweiss		= ($gfarbe == 'w');
				$gspieler	= $this->findSpieler ($gbrett);
				$gmgl_nr	= $gspieler->mgl_nr;
				$gzps		= $gspieler->zps;
				
				$ergebnis	= JRequest::getVar ('erg_'.$p.'_'.$b);
				$kampflos	= ($ergebnis == 4 || $ergebnis == 5 || $ergebnis == 6) * 2; // 0 (nicht kampflos) oder 2 (kampflos)
				
				if		($ergebnis == 0) { $hpunkte = $nieder + $antritt;	$gpunkte = $sieg + $antritt;	}
				elseif	($ergebnis == 1) { $hpunkte = $sieg + $antritt;		$gpunkte = $nieder + $antritt;	}
				elseif	($ergebnis == 2) { $hpunkte = $remis + $antritt;	$gpunkte = $remis + $antritt;	}
				elseif	($ergebnis == 3) { $hpunkte = $nieder + $antritt;	$gpunkte = $nieder + $antritt;	}
				elseif	($ergebnis == 4) { $hpunkte = $nieder;				$gpunkte = $sieg;	} // kampflos
				elseif	($ergebnis == 5) { $hpunkte = $sieg;				$gpunkte = $nieder;	} // kampflos
				elseif	($ergebnis == 6) { $hpunkte = $nieder;				$gpunkte = $nieder;	} // kampflos
				else { // "---" (eid 7) oder "spielfrei" (eid 8)
					$hpunkte = 0;
					$gpunkte = 0;
				}
			
				$sp_hvalues = '"'.$sid.'", "'.$swt_id.'", "'.$runde.'", "'.$p.'", "'.$dgang.'", '
							. '"'.$htln_nr.'", "'.$b.'", "1", "'.$hweiss.'", '
							. '"'.$hmgl_nr.'", "'.$hzps.'", "'.$gmgl_nr.'", "'.$gzps.'", '
							. '"'.$ergebnis.'", "'.$kampflos.'", "'.$hpunkte.'"';
				
				$sp_gvalues = '"'.$sid.'", "'.$swt_id.'", "'.$runde.'", "'.$p.'", "'.$dgang.'", '
							. '"'.$gtln_nr.'", "'.$b.'", "0", "'.$gweiss.'", '
							. '"'.$gmgl_nr.'", "'.$gzps.'", "'.$hmgl_nr.'", "'.$hzps.'", '
							. '"'.$ergebnis.'", "'.$kampflos.'", "'.$gpunkte.'"';
				
				$query = ' INSERT INTO #__clm_swt_rnd_spl'
						. ' ( ' . $sp_fields . ' ) '
						. ' VALUES ( ' . $sp_hvalues . ' ), ( ' . $sp_gvalues . ' ) ';
				
				$db->setQuery ($query);
				if (!$db->query ()) {
					print $db->getErrorMsg ();
					return false;
				}
				
				$hbrettpunkte += $hpunkte;
				$gbrettpunkte += $gpunkte;
				
			} // for-Schleife Bretter
			
			// Mannschaftsergebnisse
			$sieg_bed = $swt_db_data['sieg_bed'];
			
			if ($swt_data[$p]['heim_kampflos'] != 2) { // Heimmannschaft ist angetreten
				$hmanpunkte = $man_antritt;
			}
			
			if ($swt_data[$p]['gast_kampflos'] != 2) { // Gastmannschaft ist angetreten
				$gmanpunkte = $man_antritt;
			}
			
			
			if ($sieg_bed == 1) { // Standard: Mannschaft mit mehr Brettpunkten gewinnt, bei BP-Gleichheit wird geteilt
			
				if ($hbrettpunkte > $gbrettpunkte) {
					$hmanpunkte += $man_sieg;
					$gmanpunkte += $man_nieder;
				}
				elseif ($hbrettpunkte == $gbrettpunkte) {
					$hmanpunkte += $man_remis;
					$gmanpunkte += $man_remis;
				}
				else { // $hbrettpunkte < $gbrettpunkte
					$hmanpunkte += $man_nieder;
					$gmanpunkte += $man_sieg;
				}
			
			}
			elseif ($sieg_bed == 2) { // erw. Standard: Sieg bei mehr als 50% der BP, Hälfte der MP bei Hälfte der BP
				
				$max_brettpunkte = $anz_bretter * ($sieg + $antritt);
				$haelfte = $max_brettpunkte / 2;
				
				if ($hbrettpunkte > $haelfte) {
					$hmanpunkte += $man_sieg;
				}
				elseif ($hbrettpunkte == $haelfe) {
					$hmanpunkte += $man_remis;
				}
				else { // $hbrettpunkte < $haelfte
					$hmanpunkte += $man_nieder;
				}
				
				if ($gbrettpunkte > $haelfte) {
					$gmanpunkte += $man_sieg;
				}
				elseif ($gbrettpunkte == $haelfe) {
					$gmanpunkte += $man_remis;
				}
				else { // $gbrettpunkte < $haelfte
					$gmanpunkte += $man_nieder;
				}
				
			}
			
			$m_hvalues = '"'.$sid.'", "'.$swt_id.'", "'.$runde.'", "'.$p.'", "'.$dgang.'", "Aus SWT-Datei importiert!", '
						. '"1", "'.$htln_nr.'", "'.$gtln_nr.'", "'.$hbrettpunkte.'", "'.$hmanpunkte . '", "1"';
						
			$m_gvalues = '"'.$sid.'", "'.$swt_id.'", "'.$runde.'", "'.$p.'", "'.$dgang.'", "Aus SWT-Datei importiert!", '
						. '"0", "'.$gtln_nr.'", "'.$htln_nr.'", "'.$gbrettpunkte.'", "'.$gmanpunkte . '", "1"';
						
			$query = ' INSERT INTO #__clm_swt_rnd_man'
					. ' ( ' . $m_fields . ' ) '
					. ' VALUES ( ' . $m_hvalues . ' ), ( ' . $m_gvalues . ' ) ';
			
			$db->setQuery ($query);
			if (!$db->query ()) {
				print $db->getErrorMsg ();
				return false;
			}
			
		} // for-Schleife Paarungen
		
		return true;
	}*/
	
	function finalCopy () {

		//echo "in finalCopy ()<br/>";
		
		$db		=& JFactory::getDBO ();
		$conf =& JFactory::getConfig();   
		
		$date =& JFactory::getDate ();
		$zeit = $date->toMySQL ();
		
		$swt_id	= JRequest::getVar ('swt_id', 0, 'default', 'int');
		$sid	= JRequest::getVar ('sid', 0, 'default', 'int');
		$liga_id	= JRequest::getVar ('lid', 0, 'default', 'int');
		$update	= JRequest::getVar ('update', 0, 'default', 'int');
		
		$swt_data		= $this->getDataSWT ();
		$gesp_runden	= $swt_data['gesp_runden'];
				
		$clm_prefix = $db->getPrefix () . "clm_";
		$swt_prefix = $db->getPrefix () . "clm_swt_";
		
		$tables = array ( 'liga', 'mannschaften', 'meldeliste_spieler', 'rnd_man', 'rnd_spl' );
		
		$where['liga']					= '`id` = "' . $swt_id . '"';
		$where['mannschaften']			= '`swt_id` = "' . $swt_id . '"';
		$where['meldeliste_spieler']	= '`swt_id` = "' . $swt_id . '"';
		$where['rnd_man']				= '`swt_id` = "' . $swt_id . '"';
		$where['rnd_spl']				= '`swt_id` = "' . $swt_id . '"';
		
		if ($liga_id > 0 AND $update == 1) { $col_condition['liga']	= '`COLUMN_NAME` != "id"';
										$upd = 	' UPDATE ' . $swt_prefix . 'liga'
												. ' SET  lid = ' . $liga_id  
												. ' WHERE ' . $where['liga'];
										$db->setQuery ($upd);
										if (!$db->query ()) {
											print $db->getErrorMsg ();
											return false;
										}
		}
		else 						   $col_condition['liga']	= '`COLUMN_NAME` != "id" AND `COLUMN_NAME` != "lid"';
		//$col_condition['mannschaften']			= '`COLUMN_NAME` != "man_nr" AND `COLUMN_NAME` != "swt_id"';
		$col_condition['mannschaften']			= '`COLUMN_NAME` != "id" AND `COLUMN_NAME` != "swt_id"';
		$col_condition['meldeliste_spieler']	= '`COLUMN_NAME` != "id" AND `COLUMN_NAME` != "swt_id" AND `COLUMN_NAME` != "spielerid"';
		$col_condition['rnd_man']				= '`COLUMN_NAME` != "id" AND `COLUMN_NAME` != "swt_id"';
		$col_condition['rnd_spl']				= '`COLUMN_NAME` != "id" AND `COLUMN_NAME` != "swt_id"';
		
		$liga_col['liga']				= 'id';
		$liga_col['mannschaften']		= 'liga';
		$liga_col['meldeliste_spieler']	= 'lid';
		$liga_col['rnd_man']			= 'lid';
		$liga_col['rnd_spl']			= 'lid';
		
		$upd_set['liga']				= '`rnd` = "1"'; // Runden erstellt
		$upd_set['mannschaften']		= '`man_nr` = CONCAT(`liga`, IF(`tln_nr` < 10,CONCAT("0",`tln_nr`),`tln_nr`)),'
										.' `liste` = "9997", `published` = "1"';
		//$upd_set['mannschaften']		= '`liste` = "9997", `published` = "1"';
		$upd_set['meldeliste_spieler']	= '`id` = `id`'; // leere Abfrage ginge nur mit if-Unterscheidung unten
		$upd_set['rnd_man']				= '`gemeldet` = "9997", `zeit` = "'.$zeit.'"';
		$upd_set['rnd_spl']				= '`gemeldet` = "9997"';
		
		// Den Kopiervorgang durchführen
		foreach ($tables as $table) {
			$columns[$table] = '';
			$select_columns[$table] = '';
			//echo 'Kopiere Tabelle '.$swt_prefix.$table.' nach '.$clm_prefix.$table.' ...<br/>';
			$col_query = ' SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS`'
						. ' WHERE `TABLE_NAME` = "' . $swt_prefix . $table . '"'
						. ' AND `TABLE_SCHEMA` = "'.$conf->getValue('config.db').'"'       
						. ' AND ' . $col_condition[$table];
			
//			echo "col_query: $col_query"; //DBG
			$db->setQuery ($col_query);
			$cols = $db->loadObjectList ();
//			echo "cols: "; //DBG
//			print_r ($cols); //DBG
			foreach ($cols as $column) {

				if ($table == 'liga') {
					
					if ($column->COLUMN_NAME == 'lid') {
						$select_columns[$table] .= '`lid` as `id`, ';
						$columns[$table] .= '`id`, ';
					}
					else {
						$select_columns[$table] .= '`'.$column->COLUMN_NAME.'`, ';
						$columns[$table] .= '`'.$column->COLUMN_NAME.'`, ';
					}
					
				}
/*				elseif ($table == 'mannschaften11') {
					
					if ($column->COLUMN_NAME == 'id') {
						$select_columns[$table] .= '`id` as `man_nr`, ';
						$columns[$table] .= '`man_nr`, ';
					}
					else {
						$select_columns[$table] .= '`'.$column->COLUMN_NAME.'`, ';
						$columns[$table] .= '`'.$column->COLUMN_NAME.'`, ';
					}
					
				}
*/				elseif ($table == 'meldeliste_spieler') {
				
					if ($column->COLUMN_NAME == 'man_id') {
						$select_columns[$table] .= '`man_id` as `mnr`, ';
						$columns[$table] .= '`mnr`, ';
					}
					else {
						$select_columns[$table] .= '`'.$column->COLUMN_NAME.'`, ';
						$columns[$table] .= '`'.$column->COLUMN_NAME.'`, ';	
					}
					
				}
				else {
					$columns[$table] .= '`'.$column->COLUMN_NAME.'`, ';
				}
				
			}
			if ($table != 'liga' && $table != 'meldeliste_spieler') {
				$select_columns[$table] = $columns[$table];
			}
			$select_columns[$table] = substr ($select_columns[$table], 0, -2);
			$columns[$table] = substr ($columns[$table], 0, -2);
			
			if ($table != 'liga') {
				$upd = ' UPDATE ' . $swt_prefix . $table
						. ' SET `' . $liga_col[$table] . '` = "' . $liga_id . '"'
						. ' WHERE ' . $where[$table];
				
				$db->setQuery ($upd);
				if (!$db->query ()) {
					print $db->getErrorMsg ();
					return false;
				}
			}
			if ($liga_id > 0 AND $table != 'liga') {
				$delete_query = ' DELETE FROM ' . $clm_prefix . $table
								. ' WHERE '.$liga_col[$table].' = '.$liga_id;
				$db->setQuery($delete_query);
				if(!$db->query()) {
					print $db->getErrorMsg ();
					return false;
				}
			}
 
			if ($liga_id > 0 AND $table == 'liga') {
				$copy[$table] = ' REPLACE ' . $clm_prefix . $table
							. ' ( ' . $columns[$table] . ' ) '
							. ' SELECT ' . $select_columns[$table]
							. ' FROM ' . $swt_prefix . $table
							. ' WHERE ' . $where[$table];
			} else {
			$copy[$table] = ' INSERT INTO ' . $clm_prefix . $table
							. ' ( ' . $columns[$table] . ' ) '
							. ' SELECT ' . $select_columns[$table]
							. ' FROM ' . $swt_prefix . $table
							. ' WHERE ' . $where[$table];
			}

				//echo "<br/><br/>query: $query"; //DBG
				
					
			$db->setQuery ($copy[$table]);
			if (!$db->query ()) {
				print $db->getErrorMsg ();
				return false;
			}
			if ($liga_id < 1 AND $table == 'liga') {
				$liga_id = $db->insertid ();
				JRequest::setVar ('lid', $liga_id);
			}
			
		}
		
		foreach ($tables as $table) {	// muss in eigene Schleife, damit man_id in man_nr
										// (in der Tabelle mannschaften) gespeichert bleibt
										// bis die man_nr korrekt in meldeliste_spieler gespeichert ist.
			
			// gemeldet nur für bereits gespielte Runden setzen
			if ($table == 'rnd_man' || $table == 'rnd_spl') {
				$where_add = ' AND `runde` <= "' . $gesp_runden . '"';
			}
			else {
				$where_add = '';
			}
			
			$upd_query[$table] = ' UPDATE ' . $clm_prefix . $table
								. ' SET ' . $upd_set[$table]
								. ' WHERE `'.$liga_col[$table].'` = "' . $liga_id . '"' . $where_add;
			
			//echo "<br/><br/>upd_query[$table]: " . $upd_query[$table]; //DBG
		
			$db->setQuery ($upd_query[$table]);
			if (!$db->query ()) {
				print $db->getErrorMsg ();
				return false;
			}
			
			if ($table == 'rnd_man') { // Brett- und Mannschaftspunkte für noch nicht gespielte Runden löschen
				$upd_query2 = ' UPDATE ' . $clm_prefix . $table
								. ' SET `brettpunkte` = NULL, `manpunkte` = NULL'
								. ' WHERE `'.$liga_col[$table] .'` = "' . $liga_id . '" AND `runde` > "' . $gesp_runden . '"';
				$db->setQuery ($upd_query2);
				if (!$db->query ()) {
					print $db->getErrorMsg ();
					return false;
				}
			} 
		
			if ($table == 'rnd_spl') { // Brettergebnisse für noch nicht gespielte Runden löschen
				$upd_query2 = ' DELETE FROM ' . $clm_prefix . $table
								. ' WHERE `'.$liga_col[$table] .'` = "' . $liga_id . '" AND `runde` > "' . $gesp_runden . '"';
				$db->setQuery ($upd_query2);
				if (!$db->query ()) {
					print $db->getErrorMsg ();
					return false;
				}
			} 
		
		}
		
		//mannschaftnummer nach 'meldeliste_spieler') {
	
				$query = ' SELECT `liga`, `tln_nr`, `id` as `man_id`, CONCAT(`liga`, IF(`tln_nr` < 10,CONCAT("0",`tln_nr`),`tln_nr`)) as `mnr`'
						.' FROM ' . $swt_prefix . 'mannschaften'.' as m '
						. ' WHERE `liga` = "' . $liga_id . '"';
				
				//echo "<br/><br/>query: $query"; //DBG
				
				$objs = $this->_getList ($query);
				
				//echo "objs: "; //DBG
				//print_r ($objs); //DBG
				
				foreach ($objs as $obj) {
				
					$sql = ' UPDATE ' . $clm_prefix . 'meldeliste_spieler'
							. ' SET `mnr` = "' . $obj->mnr . '"'  
							. ' WHERE `mnr` = "' . $obj->man_id . '"'
							. ' AND `lid` = "' . $liga_id . '"';
				//echo "<br/><br/>query: $query"; //DBG

					$db->setQuery ($sql);
					if (!$db->query ()) {
						print $db->getErrorMsg ();
						return false;
					}
					
				}


		$swt_db_data = $this->getDataSWTdb ();
		$anz_mannschaften = $swt_db_data['anz_mannschaften'];
		// man_nr für spielfrei zurücksetzen
		$sql_fix = ' UPDATE #__clm_mannschaften'
					. ' SET `man_nr` = "0"'
					. ' WHERE `liga` = "' . $liga_id . '"'
					. ' AND `name` = "spielfrei" '
					. ' AND `zps` = "0"';
		//			. ' AND `man_nr` = CONCAT(`liga`, "'.$anz_mannschaften.'")'
		$db->setQuery ($sql_fix);
		if (!$db->query ()) {
			print $db->getErrorMsg ();
			return false;
		}
		
	require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'ergebnisse.php');
	CLMControllerErgebnisse::calculateRanking($sid,$liga_id);
	
	require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'runden.php');
	CLMControllerRunden::dwz(0, $sid, $liga_id); 
		
		return true;
		
	}
	
	function userAnlegen () {
	
		$db		=& JFactory::getDBO ();
		$sid	= JRequest::getVar ('sid', 0, 'default', 'int');
		
		$sql = ' SELECT `id`, `jid` FROM #__clm_user '
				. ' WHERE `email` = "swt_import@clm.de"'
				. ' AND `sid` = "' . $sid . '"';
		$objs = $this->_getList ($sql);
		
		if (count ($objs) < 1) {
		
			$row =& JTable::getInstance ('users', 'TableCLM');
			$row->sid			= $sid;
			$row->jid			= '9997';
			$row->name			= 'SWT-Import';
			$row->username		= 'SWT-Import Saison '.$sid;
			$row->aktive		= "1";
			$row->email			= "swt_import@clm.de";
			$row->usertype		= "sl";
			$row->user_clm		= "70";
			$row->zps			= "1";
			$row->published		= "1";
			$row->bemerkungen	= "Dieser User ist nur für Importzwecke gedacht!";
			$row->bem_int		= "Dieser User ist nur für Importzwecke gedacht!";
			
			if (!$row->store()) {
				print $row->getError();
				return false;
			}
			
		}
		
		return true;
		
	}
	
	
	function rundenTermine () {
		
		$db		=& JFactory::getDBO ();
		
		$date =& JFactory::getDate ();
		$zeit = $date->toMySQL ();
		
		$swt_id	= JRequest::getVar ('swt_id', 0, 'default', 'int');
		$sid	= JRequest::getVar ('sid', 0, 'default', 'int');
		$update	= JRequest::getVar ('update', 0, 'default', 'int');
		$liga_id= JRequest::getVar ('lid', 0, 'default', 'int');

		$swt_db_data	= $this->getDataSWTdb ();		
		$swt_data		= $this->getDataSWT ();

		
		$anz_runden			= $swt_db_data['anz_runden'];
		$anz_durchgaenge	= $swt_db_data['anz_durchgaenge'];
		$gesp_runden		= $swt_data['gesp_runden'];
		$termineFromDatabase = array();
		if ($update == 1 AND $liga_id > 0) {
			
			$db		=& JFactory::getDBO ();
			$select_query = ' SELECT * FROM #__clm_runden_termine '
							.' WHERE liga = '.$liga_id;
			$db->setQuery ($select_query);
			$dbtermineFromDatabase = $db->loadObjectList();

			foreach ( $dbtermineFromDatabase as $trunde) {
				$termineFromDatabase[$trunde->nr] = $trunde;
			}
			$delete_query = ' DELETE FROM #__clm_runden_termine '
							. ' WHERE liga = '.$liga_id;
				$db->setQuery($delete_query);
				if(!$db->query()) {
					print $db->getErrorMsg ();
					return false;
				}
		}
		$fields = '`sid`, `name`, `liga`, `nr`, `datum`, `startzeit`, `published`, `bem_int`, `gemeldet`, `zeit`';
		$values = '';
		for ($d = 1; $d <= $anz_durchgaenge; $d++) {
		
			for ($r = 1; $r <= $anz_runden; $r++) {
		
				if ($r <= $gesp_runden) {
					$published = 1;
				}
				else {
					$published = 0;
				}
				
				$rt = (($d - 1) *  $anz_runden) + $r;
				
				if ($anz_durchgaenge == 1) {
					$name = 'Runde ' . $r;
				}
				else {
					$name = 'Durchgang ' . $d . ', Runde ' . $r;
				}
				if (isset($swt_data['runden_datum'][$rt]) AND $swt_data['runden_datum'][$rt] != "0000-00-00") {
					$values .= ' ( "'.$sid.'", "'.$name.'", "'.$liga_id.'", "'.$rt.'", "'.$swt_data['runden_datum'][$rt].'", "'.$swt_data['runden_beginn'][$rt].'", "'.$published.'", '
							. '"Import durch SWT Datei.", "9997", "'.$zeit.'" ), ';
				} elseif (isset($termineFromDatabase[$rt]) AND $termineFromDatabase[$rt]->datum != "0000-00-00") {
					$values .= ' ( "'.$sid.'", "'.$name.'", "'.$liga_id.'", "'.$rt.'", "'.$termineFromDatabase[$rt]->datum.'", "'.$termineFromDatabase[$rt]->startzeit.'", "'.$published.'", '
							. '"Import durch SWT Datei.", "9997", "'.$zeit.'" ), ';
				} else {
					$values .= ' ( "'.$sid.'", "'.$name.'", "'.$liga_id.'", "'.$rt.'", "0000-00-00", "00:00:00", "'.$published.'", '
							. '"Import durch SWT Datei.", "9997", "'.$zeit.'" ), ';
				}
			}
			
		}
		$values = substr ($values, 0, -2);
		
		$sql = ' INSERT INTO #__clm_runden_termine'
						. ' ( ' . $fields . ' ) '
						. ' VALUES ' . $values;
		
		$db->setQuery ($sql);
		if (!$db->query ()) {
			print $db->getErrorMsg ();
			return false;
		}
		
		return true;
		
	}
	
	function getDataSWT () {
	
		if (empty ($this->_swt_data)) {
		
			jimport( 'joomla.filesystem.file' );
		
			// Namen und Verzeichnis der SWT-Datei auslesen
			$filename = JRequest::getVar( 'swt', '', 'default', 'string' );
			$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		
			$swt = $path.$filename;
			
			$swt_data['gesp_runden'] = CLMSWT::readInt ($swt, 3);
			
			// Rundentermine
			$j = 0;
			for ($ii = 0; $ii < 40; $ii++) { 
				$test = 'datum'.$ii;
				$d1 = $this->_SWTReadInt ($swt, 11457 +$j);
				$d2 = $this->_SWTReadInt ($swt, 11457 +$j +1);
				$hh = $this->_SWTReadInt ($swt, 11457 +$j +2);
				$mm = $this->_SWTReadInt ($swt, 11457 +$j +3);
				$j += 4;
				$lt = $d1 + ($d2 * 256);
				if ($lt > 0) {
					$rdate = date_create('1899-12-30');
					$ltstring = $lt." days";
					//date_add($rdate, date_interval_create_from_date_string($ltstring));  	// for >= php 5.3.0 
					date_modify($rdate, '+'.$lt.' days');									// for >= php 5.2.0 too
					$swt_data['runden_datum'][$ii+1] = date_format($rdate, 'Y-m-d');
					$swt_data['runden_beginn'][$ii+1] = sprintf('%02d', $hh).':'.sprintf('%02d', $mm).':00';
				}
			}

			$this->_swt_data = $swt_data;
			
		}
		return $this->_swt_data;
		
	}
	
	function getDataSWTdb () {

		if (!empty ($this->_swt_db_data)) {
			return $this->_swt_db_data;
		}
		
		$swt_id = JRequest::getVar( 'swt_id', '', 'default', 'int' );
		$sql = ' SELECT id, teil as anz_mannschaften, stamm as anz_bretter, ersatz as anz_ersatzspieler, ' 
				. 'durchgang as anz_durchgaenge, runden as anz_runden, sieg, remis, nieder, antritt, man_sieg, '
				. 'man_remis, man_nieder, man_antritt, sieg_bed'
				. ' FROM #__clm_swt_liga'
				. ' WHERE id = '.$swt_id;

		$objs = $this->_getList ($sql);
		$obj = $objs[0];

		$spalten = array ('anz_mannschaften', 'anz_bretter', 'anz_ersatzspieler', 'anz_durchgaenge', 'anz_runden', 'sieg', 'remis', 'nieder', 'antritt', 'man_sieg', 'man_remis', 'man_nieder', 'man_antritt', 'sieg_bed');
		foreach ($spalten as $spalte) {
			$swt_db_data[$spalte] = $obj->$spalte;
		}
		
		
		$sql = ' SELECT s.id as id, m.tln_nr as tln_nr, s.zps as zps, s.mgl_nr as mgl_nr'
				. ' FROM #__clm_swt_meldeliste_spieler as s'
				. ' LEFT JOIN #__clm_swt_mannschaften as m'
				. ' ON s.man_id = m.id'
				. ' WHERE s.swt_id = '.$swt_id
				. ' AND m.swt_id = '.$swt_id
				. ' ORDER BY spielerid ASC';
		
		$objs = $this->_getList ($sql);
		$swt_db_data['spieler'] = $objs;
		
		$this->_swt_db_data = $swt_db_data;
		return $swt_db_data;

	}
	
	function _SWTReadInt ($file, $offset, $length = 1) {
		$value = 0;
		for ($i = 0; $i < $length; $i++) {
			$cur = ord (JFile::read ($file, false, 1, 8192, $offset+$i));
			$value += $cur * pow (256, $i);
		}
		return $value;
	}

	
}
?>
