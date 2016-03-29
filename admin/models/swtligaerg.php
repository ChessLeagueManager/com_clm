<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');


class CLMModelSWTLigaerg extends JModelLegacy {

	var $debug_ausgaben = -1; // -1 or 1 or 3 or 5
	
    function __construct () {

        parent::__construct ();

		// Mannschafts-Nummer fuer update
		// ...
        // $db_man_nr  = ...
		
        // Daten an View weitergeben
        
    }
    
    function getSpielerliste () {
    
    	if (!empty ($this->_splist)) {
    		return $this->_splist;
    	}
    	set_time_limit(30);
    	$swt_id = clm_escape(JRequest::getVar ('swt_id', 0, 'default', 'int'));
    	
		$swt_db_data = $this->getDataSWTdb ();
		$anz_mannschaften = $swt_db_data['anz_mannschaften'];
		
    	for ($m = 1; $m <= $anz_mannschaften; $m++) {
			
			$sql = ' SELECT s.man_id, s.snr, s.mgl_nr as mgl_nr, s.zps as zps, d.Mgl_Nr, d.ZPS, d.Spielername as name,'
	//				. ' CONCAT_WS(" ", m.tln_nr, "-", s.snr, "&nbsp;", d.Spielername) as text, d.id as id'
					. ' CONCAT_WS(" - ", s.snr, d.Spielername) as text, s.id as id'
					. ' FROM #__clm_swt_meldeliste_spieler as s'
					. ' LEFT JOIN #__clm_swt_mannschaften as m'
					. ' ON s.man_id = m.id'
					. ' LEFT JOIN #__clm_dwz_spieler as d'
					. ' ON s.mgl_nr = d.Mgl_Nr AND s.zps = d.ZPS AND s.sid = d.sid'
					. ' WHERE m.swt_id = '.$swt_id.' AND m.tln_nr = '.$m
					. ' ORDER BY s.snr ASC';
		
			$tmp = $this->_getList ($sql);
			if ($tmp == null) {
				$tmp = array ();
			}
			
			$this->_splist[$m] = $tmp;
			
		}
		
		if ($this->debug_ausgaben > 2) {
    		echo "<pre>_splist: ";
    		print_r ($this->_splist);
    		echo "</pre>";
    	}
    	
		return $this->_splist;
				
    }
    
	function findSpieler ($id) {
	
		$splist = $this->getSpielerliste ();
		
		foreach ($splist as $spielerliste) {
			foreach ($spielerliste as $spieler) {
				if ($spieler->id == $id) {
					return $spieler;
				}
			}
		}
		return false;
		
	}
	
	function getErgebnisliste () {
	
		$swt_db_data = $this->getDataSWTdb ();
		
		$sql = ' SELECT eid, erg_text FROM #__clm_ergebnis'
				. ' ORDER BY eid ASC';
		$ergebnisse = $this->_getList ($sql);

		$sieg	= $swt_db_data['sieg'];
		$remis	= $swt_db_data['remis'];
		$nieder	= $swt_db_data['nieder'];
		$antritt= $swt_db_data['antritt'];
		
		$erg_punkte = array ('1', '0,5', '0', '+', '---', '-', '/', 'tmp');
		$punkte	= array (
					(string) ($sieg + $antritt),
					(string) ($remis + $antritt),
					(string) ($antritt + $nieder),
					(string) ($sieg + $antritt),
					          'tmp',
					(string) (1 + $nieder - 1), // um ggf '.0' zu eliminieren
					          '-',
					          '---'
				);
		
		foreach ($ergebnisse as $ergebnis) {

			unset ($obj); // evtl. überflüssig ?
			$obj = new stdClass ();
			$obj->ergid	= $ergebnis->eid;
			$text = $ergebnis->erg_text;
			
			if (substr ($text, 0, 1) != '-' && substr ($text, -1) != '-') {
				$text = str_replace ('-', '/', $text);
			}
			
			if ($obj->ergid == 4 || $obj->ergid == 5 || $obj->ergid == 6) {
				$kampflos = ' (kampflos)';
			}
			else {
				$kampflos = '';
			}
			
			$obj->text	= str_replace ($erg_punkte, $punkte, $text) . $kampflos;
			
			$ergliste[] = $obj;
			
		}

	/*	
		$sieg	= $swt_db_data['sieg'];
		$remis	= $swt_db_data['remis'];
		$nieder	= $swt_db_data['nieder'];
		$antritt= $swt_db_data['antritt'];
		
		$ergebnisse = array ('3-1', 
		$i = 0;
		foreach ($ergebnisse as $ergebnis) {
			unset ($obj);
			$obj = new stdClass ();
			$obj->erg = $i;
			$obj->text = $ergebnis;
			$i += 1
		}*/
		
		return $ergliste;
	}
	
	function findPlayerID ($name) {
		
		$splist = array_merge ($this->getSpielerlisteHeim (), $this->getSpielerlisteGast ());
		if ($name == '' || $splist == array ()) {
			return 0;
		}
		
		foreach ($splist as $spieler) {
			if ($spieler->name == $name) {
				return $spieler->id;
			}
		}
		return 0;
		
	}
	
	
	
	function store () {
		
		// DB-Zugriff
		$db		= JFactory::getDBO ();
		//$row	=& JTable::getInstance ('ligenSWT', 'TableCLM');
		
		$swt_id	= JRequest::getVar ('swt_id', 0, 'default', 'int');
		$sid	= JRequest::getVar ('sid', 0, 'default', 'int');
		$runde	= 1 + JRequest::getVar ('runde', 0, 'default', 'int');
		$dgang	= 1 + JRequest::getVar ('dgang', 0, 'default', 'int');
		$noOrgReference = JRequest::getVar('noOrgReference', '0', 'default', 'string');
		$noBoardResults = JRequest::getVar('noBoardResults', '0', 'default', 'string');

		
		// zuvor in der DB gespeicherte Daten und (allgemeine) SWT-Daten
		$swt_data		= $this->getDataSWT (1);
		$swt_db_data	= $this->getDataSWTdb ();
		
		//Liga-Parameter aufbereiten
		$swt_id = JRequest::getVar( 'swt_id', '', 'default', 'int' );
		$sql = ' SELECT params'
			. ' FROM #__clm_swt_liga'
			. ' WHERE id = '.$swt_id;
		$objs = $this->_getList ($sql);
		$paramsStringArray = explode("\n", $objs[0]->params);
		$params = array();
		foreach ($paramsStringArray as $value) {
			$ipos = strpos ($value, '=');
			if ($ipos !==false) {
				$key = substr($value,0,$ipos);
				if (substr($key,0,2) == "\'") $key = substr($key,2,strlen($key)-4);
				if (substr($key,0,1) == "'") $key = substr($key,1,strlen($key)-2);
					$params[$key] = substr($value,$ipos+1);
				}
			}	
		if (!isset($params['color_order']))  {   //Standardbelegung
			$params['color_order'] = '1'; }
		switch ($params['color_order']) {
			case '1': $colorstr = '01'; break;
			case '2': $colorstr = '10'; break;
			case '3': $colorstr = '0110'; break;
			case '4': $colorstr = '1001'; break;
			case '5': $colorstr = '00'; break;
			case '6': $colorstr = '11'; break;
			default: $colorstr = '01';	
		}

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
/*		$anz_durchgaenge	= $swt_db_data['anz_durchgaenge'];
		$anz_runden			= $swt_db_data['anz_runden'];*/

		$anz_paarungen = ceil ($anz_mannschaften / 2);
		
		$m_fields = '`sid`, `swt_id`, `runde`, `paar`, `dg`, '
					. '`heim`, `tln_nr`, `gegner`, `ergebnis`, `kampflos`, `brettpunkte`, `manpunkte`, `published`';
		
		for ($p = 1; $p <= $anz_paarungen; $p++) {
			if (!isset($swt_data[$p])) break;
			$htln_nr = $swt_data[$p]['heim']; // tln_nr der Heimmannschaft
			$gtln_nr = $swt_data[$p]['gast']; // tln_nr der Gastmannschaft
			
			$hmanpunkte = 0;
			$gmanpunkte = 0;
			
			$hbrettpunkte = 0;
			$gbrettpunkte = 0;
			
			$keine_ergebnisse = true;
			$y1 = 0;
		  if ($noBoardResults == '0') {
			for ($b = 1; $b <= $anz_bretter; $b++) {
			
				// Einzelergebnisse 
				$hbrett = JRequest::getVar ('hbrett_'.$p.'_'.$b);
				$gbrett = JRequest::getVar ('gbrett_'.$p.'_'.$b);
				
				$hfarbe		= JRequest::getVar ('hfarbe_'.$p.'_'.$b);
				$hweiss		= ($hfarbe == 'w');
				$hspieler	= $this->findSpieler ($hbrett);
				if ($hspieler === false) {
					$hmgl_nr	= '';
					$hzps		= '';
				} else { 
					$hmgl_nr	= $hspieler->mgl_nr;
					$hzps		= $hspieler->zps;

				}
				$gfarbe		= JRequest::getVar ('gfarbe_'.$p.'_'.$b);
				$gweiss		= ($gfarbe == 'w');
				$gspieler	= $this->findSpieler ($gbrett);
				if ($gspieler === false) {
					$gmgl_nr	= '';
					$gzps		= '';
				} else { 
					$gmgl_nr	= $gspieler->mgl_nr;
					$gzps		= $gspieler->zps;
				}
				//Setzen der Spielerfarbe
				$hweiss = substr($colorstr,$y1,1);
				if ($hweiss == 1) $gweiss = 0; else $gweiss = 1;
				$y1++;
				if ($y1 >= strlen($colorstr)) $y1 = 0;
				
				$ergebnis	= JRequest::getVar ('erg_'.$p.'_'.$b);
				$kampflos	= ($ergebnis == 4 || $ergebnis == 5 || $ergebnis == 6) * 2; // 0 (nicht kampflos) oder 2 (kampflos)
				
				
				if		($ergebnis == 0) { $hpunkte = $nieder + $antritt;	$gpunkte = $sieg + $antritt;	}
				elseif	($ergebnis == 1) { $hpunkte = $sieg + $antritt;		$gpunkte = $nieder + $antritt;	}
				elseif	($ergebnis == 2) { $hpunkte = $remis + $antritt;	$gpunkte = $remis + $antritt;	}
				elseif	($ergebnis == 3) { $hpunkte = $nieder + $antritt;	$gpunkte = $nieder + $antritt;	}
				elseif	($ergebnis == 4) { $hpunkte = $nieder;				$gpunkte = $sieg + $antritt;	} // kampflos
				elseif	($ergebnis == 5) { $hpunkte = $sieg + $antritt;		$gpunkte = $nieder;	} // kampflos
				elseif	($ergebnis == 6) { $hpunkte = $nieder;				$gpunkte = $nieder;	} // kampflos
				else { // "---" (eid 7) oder "spielfrei" (eid 8)
					$hpunkte = 0;
					$gpunkte = 0;
				}
				$hmpunkte = $hpunkte;
				$gmpunkte = $gpunkte;
	
				$ergebnisk	= JRequest::getVar ('ergk_'.$p.'_'.$b);
				$dwz_edit   = 0;
				if (isset($ergebnisk) AND $ergebnisk > 0) {
					if		($ergebnisk == 4) { $hmpunkte = $nieder;				$gmpunkte = $sieg + $antritt;	} // kampflos
					elseif	($ergebnisk == 5) { $hmpunkte = $sieg + $antritt;		$gmpunkte = $nieder;	} // kampflos
					elseif	($ergebnisk == 6) { $hmpunkte = $nieder;				$gmpunkte = $nieder;	} // kampflos
					else { // "---" (eid 7) oder "spielfrei" (eid 8)
						$hmpunkte = 0;
						$gmpunkte = 0;
					}
					$dwz_edit = $ergebnisk;
					$dwz_editor = clm_core::$access->getID();
				}
				
				if ($ergebnis != null) {
				
					$keine_ergebnisse = false;
					$sp_fields = '`sid`, `swt_id`, `runde`, `paar`, `dg`, '
						. '`tln_nr`, `brett`, `heim`, `weiss`, `spieler`, `zps`, `gegner`, `gzps`, '
						. '`ergebnis`, `kampflos`, `punkte`, `gemeldet`';
					if ($dwz_edit > 0) $sp_fields .= ', `dwz_edit`, `dwz_editor`';		
					$sp_hvalues = '"'.$sid.'", "'.$swt_id.'", "'.$runde.'", "'.$p.'", "'.$dgang.'", '
								. '"'.$htln_nr.'", "'.$b.'", "1", "'.$hweiss.'", '
								. '"'.$hmgl_nr.'", "'.$hzps.'", "'.$gmgl_nr.'", "'.$gzps.'", '
								. '"'.$ergebnis.'", "'.$kampflos.'", "'.$hpunkte.'", "'.clm_core::$access->getID().'"';
					if ($dwz_edit > 0) $sp_hvalues .= ', "'.$dwz_edit.'", "'.$dwz_editor.'"';					
					$sp_gvalues = '"'.$sid.'", "'.$swt_id.'", "'.$runde.'", "'.$p.'", "'.$dgang.'", '
								. '"'.$gtln_nr.'", "'.$b.'", "0", "'.$gweiss.'", '
								. '"'.$gmgl_nr.'", "'.$gzps.'", "'.$hmgl_nr.'", "'.$hzps.'", '
								. '"'.$ergebnis.'", "'.$kampflos.'", "'.$gpunkte.'", "'.clm_core::$access->getID().'"';
					if ($dwz_edit > 0) $sp_gvalues .= ', "'.$dwz_edit.'", "'.$dwz_editor.'"';							
					$query = ' INSERT IGNORE INTO #__clm_swt_rnd_spl'
							. ' ( ' . $sp_fields . ' ) '
							. ' VALUES ( ' . $sp_hvalues . ' ), ( ' . $sp_gvalues . ' ); ';
				
					$db->setQuery ($query);
					if (!$db->query ()) {
						print $db->getErrorMsg ();
						return false;
					}
					
					$hbrettpunkte += $hmpunkte;
					$gbrettpunkte += $gmpunkte;
					
				}
				
			} // for-Schleife Bretter
		  } elseif ($noBoardResults == '1') {
				$hbrettpunkte = $swt_data[$p]['hmmsum'];
				$gbrettpunkte = $swt_data[$p]['gmmsum'];
		  }
			
			// Mannschaftsergebnisse
			$sieg_bed = $swt_db_data['sieg_bed'];
			$hkampflos = 0;
			$gkampflos = 0;
			
			if ($swt_data[$p]['heim_kampflos'] != 2) { // Heimmannschaft ist angetreten
				$hmanpunkte = $man_antritt;
			} else {
				$hkampflos = 1;
				$gkampflos = 1;
			}
			
			if ($swt_data[$p]['gast_kampflos'] != 2) { // Gastmannschaft ist angetreten
				$gmanpunkte = $man_antritt;
			} else {
				$hkampflos = 1;
				$gkampflos = 1;
			}
			
			if ($sieg_bed == 1) { // Standard: Mannschaft mit mehr Brettpunkten gewinnt, bei BP-Gleichheit wird geteilt
			
				if ($hbrettpunkte > $gbrettpunkte) {
					$hmanpunkte += $man_sieg;
					$gmanpunkte += $man_nieder;
					$hergebnis = 1;
					$gergebnis = 0;
				}
				elseif ($hbrettpunkte == $gbrettpunkte) {
					$hmanpunkte += $man_remis;
					$gmanpunkte += $man_remis;
					$hergebnis = 2;
					$gergebnis = 2;
				}
				else { // $hbrettpunkte < $gbrettpunkte
					$hmanpunkte += $man_nieder;
					$gmanpunkte += $man_sieg;
					$hergebnis = 0;
					$gergebnis = 1;
				}
			
			}
			elseif ($sieg_bed == 2) { // erw. Standard: Sieg bei mehr als 50% der BP, Hälfte der MP bei Hälfte der BP
				
				$max_brettpunkte = $anz_bretter * ($sieg + $antritt);
				$haelfte = $max_brettpunkte / 2;
				
				if ($hbrettpunkte > $haelfte) {
					$hmanpunkte += $man_sieg;
					$hergebnis = 1;				
				} elseif ($hbrettpunkte == $haelfe) {
					$hmanpunkte += $man_remis;
					$hergebnis = 2;				
				} else { // $hbrettpunkte < $haelfte
					$hmanpunkte += $man_nieder;
					$hergebnis = 0;				
				}
				
				if ($gbrettpunkte > $haelfte) {
					$gmanpunkte += $man_sieg;
					$gergebnis = 1;
				} elseif ($gbrettpunkte == $haelfe) {
					$gmanpunkte += $man_remis;
					$gergebnis = 2;
				} else { // $gbrettpunkte < $haelfte
					$gmanpunkte += $man_nieder;
					$gergebnis = 0;
				}
				
			}
			
			if ($hbrettpunkte == 0 && $gbrettpunkte == 0) { // Kampf hat (noch) nicht stattgefunden
				$hmanpunkte = 0;
				$gmanpunkte = 0;
				$hergebnis = null;
				$gergebnis = null;
			}
			
			if ($keine_ergebnisse AND $noBoardResults == '0') {
				$hbrettpunkte = null;
				$gbrettpunkte = null;
				$hmanpunkte = null;
				$gmanpunkte = null;
				$hergebnis = null;
				$gergebnis = null;
			}
			
			if ($hkampflos == 1) {
				if ($hergebnis == 0) $hergebnis = 4;
				if ($hergebnis == 1) $hergebnis = 5;
				if ($hergebnis == 2) $hergebnis = 6;
			}
			if ($gkampflos == 1) {
				if ($gergebnis == 0) $gergebnis = 4;
				if ($gergebnis == 1) $gergebnis = 5;
				if ($gergebnis == 2) $gergebnis = 6;
			}
			
			$m_hvalues = '"'.$sid.'", "'.$swt_id.'", "'.$runde.'", "'.$p.'", "'.$dgang.'", '
						. '"1", "'.$htln_nr.'", "'.$gtln_nr.'", "'.$hergebnis.'", "'.$hkampflos.'", "'
						.$hbrettpunkte.'", "'.$hmanpunkte . '", "1"';
						
			$m_gvalues = '"'.$sid.'", "'.$swt_id.'", "'.$runde.'", "'.$p.'", "'.$dgang.'", '
						. '"0", "'.$gtln_nr.'", "'.$htln_nr.'", "'.$gergebnis.'", "'.$gkampflos.'", "'
						.$gbrettpunkte.'", "'.$gmanpunkte . '", "1"';
						
			$query = ' INSERT IGNORE INTO #__clm_swt_rnd_man'
					. ' ( ' . $m_fields . ' ) '
					. ' VALUES ( ' . $m_hvalues . ' ), ( ' . $m_gvalues . ' ); ';
			
			$db->setQuery ($query);
			if (!$db->query ()) {
				print $db->getErrorMsg ();
				return false;
			}
			
		} // for-Schleife Paarungen
		
		return true;
	}

	function getDataSWT ($during_store = 0) {
	
		if ($this->debug_ausgaben > 0) {
			echo "in _getDataSWT ()<br/>";
		}
		
		if (empty ($this->_swt_data)) {
		
			jimport( 'joomla.filesystem.file' );
		
			// Namen und Verzeichnis der SWT-Datei auslesen
			$filename = JRequest::getVar( 'swt', '', 'default', 'string' );
			$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		
			$swt = $path.$filename;
			
			// allgemeine SWT-Daten setzen, die nicht von der Paarung abhängen
			// ACHTUNG!! Hier nur Strings als Keys verwenden um Überschneidung
			// mit Paarungsdaten ($swt_data[1], $swt_data[2], ...) zu verhindern!
			$swt_data['gesp_runden']		= CLMSWT::readInt ($swt, 3);
			$swt_data['ausgeloste_runden']	= CLMSWT::readInt ($swt, 5);
			$swt_data_man = $this->_getDataSWTman ();
			$swt_data_spl = $this->_getDataSWTspl ($during_store);
			
			if ($this->debug_ausgaben > 2) {
				echo "<pre>swt_data_man: "; //DBG
				print_r ($swt_data_man); //DBG
				echo "</pre>"; //DBG*/

				echo "<pre>swt_data_spl: "; //DBG
				print_r ($swt_data_spl); //DBG
				echo "</pre>"; //DBG*/
			}
			
			$length = max (count ($swt_data_man), count ($swt_data_spl));
			for ($p = 1; $p <= $length; $p++) { // !! swt_data_man und swt_data_spl beginnen mit Index 1
				if ($this->debug_ausgaben > 1) {
					echo "paarung: $p<br/>";
				}
				if (isset($swt_data_man[$p]) AND isset($swt_data_spl[$p])) {
				$swt_data[$p] = array_merge ($swt_data_man[$p], $swt_data_spl[$p]);	}
			}
			$this->_swt_data = $swt_data;
			
		}
		return $this->_swt_data;
		
	}
	
    function _getDataSWTman () {
    
		if ($this->debug_ausgaben > 0) {
			echo "in _getDataSWTman ()<br/>";
		}
		
		if (!empty ($this->_swt_data_man)) {
			return $this->_swt_data_man;
		}
		
		jimport( 'joomla.filesystem.file' );
		
		// Namen und Verzeichnis der SWT-Datei auslesen
		$filename = JRequest::getVar( 'swt', '', 'default', 'string' );
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		
		$swt = $path.$filename;

		// Aktuell zu bearbeitende/r Runde und Durchgang sowie swt_id
		$runde	= JRequest::getVar ('runde', 0, 'default', 'int');
		$dgang	= JRequest::getVar ('dgang', 0, 'default', 'int');
		$swt_id = JRequest::getVar ('swt_id', 0, 'default', 'int');
		
		// schon gespeicherte SWT-Daten aus der DB holen
		$swt_db_data = $this->getDataSWTdb ();

		if ($this->debug_ausgaben > 2) {
			echo "<pre>swt_db_data:";
			print_r ($swt_db_data);
			echo "</pre>";
		}
		
		$anz_mannschaften	= $swt_db_data['anz_mannschaften'];
		$anz_bretter		= $swt_db_data['anz_bretter'];
		$anz_durchgaenge	= $swt_db_data['anz_durchgaenge'];
		$anz_runden			= $swt_db_data['anz_runden'];
		$ausgeloste_runden	= CLMSWT::readInt ($swt, 5);
		$anz_spieler		= CLMSWT::readInt ($swt, 7, 2);
		
		if ($this->debug_ausgaben > 1) {
			echo "anz_spieler: $anz_spieler<br/>";
		}
		
		
		$offset_rdaten	= $anz_spieler * $anz_durchgaenge * $anz_runden * 19;
		$offset_runde	= ($dgang * $anz_runden + $runde) * 19;
		$offset			= 13384 + $offset_rdaten + $offset_runde;
		
		$abstand		= $anz_durchgaenge * $anz_runden * 19;
		
		$sql = ' SELECT name FROM #__clm_swt_mannschaften'
				. ' WHERE swt_id = '. $swt_id
				. ' ORDER BY tln_nr ASC';
		
		$mannschaft = $this->_getList ($sql);
				
		for ($m = 1; $m <= $anz_mannschaften; $m++) {

			if ($this->debug_ausgaben > 1) {
				echo "offset: $offset<br/>";
			}
			$farbe		= CLMSWT::readInt ($swt, $offset + 8);
			if ($farbe == 2 OR $farbe == 4) { 
				$farbe = $farbe - 1;
				$kampflos = 2;
			} else {
				$kampflos = 0;
			}
			$heimrecht	= ($farbe == 1);
			$gegner		= CLMSWT::readInt ($swt, $offset + 9) - 230;
			$paarung	= CLMSWT::readInt ($swt, $offset + 13);
			//$kampflos	= CLMSWT::readInt ($swt, $offset + 15);			

			$swt_data[$paarung]['farbe'] = $farbe;
			
			if ($heimrecht) {
				$swt_data[$paarung]['heim_kampflos'] = $kampflos;
				$swt_data[$paarung]['gast_kampflos'] = $kampflos;
				$heim = $m;
				$swt_data[$paarung]['heim'] = $heim;
				if(isset($mannschaft[$heim-1])) {
					$swt_data[$paarung]['heim_mannschaft'] = $mannschaft[$heim-1]->name; }
				if ($gegner >= 0) {
					$gast = $gegner;
					$swt_data[$paarung]['gast'] = $gast;
					if(isset($mannschaft[$gast-1])) {
						$swt_data[$paarung]['gast_mannschaft'] = $mannschaft[$gast-1]->name; }
				}
			}
			else {
				$swt_data[$paarung]['gast_kampflos'] = $kampflos;
				$swt_data[$paarung]['heim_kampflos'] = $kampflos;
				$gast = $m;
				$swt_data[$paarung]['gast'] = $gast;
				if(isset($mannschaft[$gast-1])) {
					$swt_data[$paarung]['gast_mannschaft'] = $mannschaft[$gast-1]->name; }
				if ($gegner >= 0) {
					$heim = $gegner;
					$swt_data[$paarung]['heim'] = $heim;
					if(isset($mannschaft[$heim-1])) {
						$swt_data[$paarung]['heim_mannschaft'] = $mannschaft[$heim-1]->name; }
				}
			}
			$offset += $abstand;
			
		}
		
		if ($this->debug_ausgaben > 2) {
			echo "<pre>swt_data in getDataSWTman (): ";
			print_r ($swt_data);
			echo "</pre>";
		}
		
		$this->_swt_data_man = $swt_data;
		return $this->_swt_data_man;
		
	}
	
	function _getDataSWTspl ($during_store = 0) {
	
		if ($this->debug_ausgaben > 0) {
			echo "in _getDataSWTspl ()<br/>";
		}
		
		if (!empty ($this->_swt_data_spl)) {
			return $this->_swt_data_spl;
		}
		
		jimport( 'joomla.filesystem.file' );

		// Namen und Verzeichnis der SWT-Datei auslesen
		$filename = JRequest::getVar( 'swt', '', 'default', 'string' );
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		
		$swt = $path.$filename;

		// Aktuell zu bearbeitende(r) Paarung, Runde und Durchgang
		$par	= JRequest::getVar ('par', 0, 'default', 'int');
		$runde	= JRequest::getVar ('runde', 0, 'default', 'int');
		$dgang	= JRequest::getVar ('dgang', 0, 'default', 'int');
		$mturnier = JRequest::getVar ('mturnier', 0, 'default', 'int');
		$ungerade = JRequest::getVar ('ungerade', false, 'default', 'bool');
		$noOrgReference = JRequest::getVar('noOrgReference', '0', 'default', 'string');
		$noBoardResults = JRequest::getVar('noBoardResults', '0', 'default', 'string');
		
		// schon gespeicherte SWT-Daten aus der DB holen
		$swt_db_data = $this->getDataSWTdb ();

		$anz_mannschaften	= $swt_db_data['anz_mannschaften'];
		$anz_bretter		= $swt_db_data['anz_bretter'];
		$anz_durchgaenge	= $swt_db_data['anz_durchgaenge'];
		$anz_runden			= $swt_db_data['anz_runden'];
		$ausgeloste_runden	= CLMSWT::readInt ($swt, 5);
		$anz_spieler		= CLMSWT::readInt ($swt, 7, 2);
		
		if ($this->debug_ausgaben > 1) {
			echo "anz_spieler: $anz_spieler<br/>";
		}
		
		$anz_paarungen		= ceil ($anz_mannschaften / 2);
		
		$offset_runde	= ($dgang * $anz_runden + $runde) * 19;
		$offset			= 13384 + $offset_runde;
		if ($this->debug_ausgaben > 1) {
			echo "dgang: $dgang<br/>";
			echo "anz_runden: $anz_runden<br/>";
			echo "runde: $runde<br/>";
			echo "offset_runde: $offset_runde<br/>";
			echo "offset: $offset<br/>";
		}
		
		$abstand		= $anz_durchgaenge * $anz_runden * 19;
		
		// SWT-Mannschaftsdaten
		$swt_man = $this->_getDataSWTman ();
		
		// Spielerpaarungen
		$spieler = $swt_db_data['spieler'];
		if ($this->debug_ausgaben > 1) {
			echo "spieler: "; var_dump($spieler); echo "<br>";
			for ($s = 1; $s <= count($spieler); $s++) {
				echo "<br>spieler-s: ".$s."  "; var_dump($spieler[$s-1]); 
			}
		}
		unset ($swt_data); // ACHTUNG!! vor der Schleife keine Werte einfügen!!
		$tln_paarung = array();
		for ($s = 1; $s <= $anz_spieler; $s++) {
		  if (isset($spieler[$s-1])) {
			// echo "<br>spieler-s: ".$s."  "; var_dump($spieler[$s-1]); echo "<br>";
			
			if ($this->debug_ausgaben > 1) {
				echo "offset: $offset<br/>";
			}
			
			$swtfarbe	= CLMSWT::readInt ($swt, $offset + 8);
			if ($swtfarbe == 1)		{ $farbe = 'w'; $gegfarbe = 's'; }
			elseif ($swtfarbe == 3)	{ $farbe = 's'; $gegfarbe = 'w'; }
			else					{ $farbe = '';  $gegfarbe = ''; }
			$gegner		= CLMSWT::readInt ($swt, $offset + 9, 2);
			//if (!empty ($gegner) && $mturnier != 1) {
			//	// bei Ligen ist der Gegner mit einem Offset von 230 gespeichert
			//	$gegner -= 230;
			//}
			$ergebnis	= CLMSWT::readInt ($swt, $offset + 11);
			//$paarung	= CLMSWT::readInt ($swt, $offset + 13);
			$paarung	= 0; 	// leider war die Paarungsnummer in Einzelergebnissen nicht immer korrekt auch wenn eingetragen
								// deshalb wird diese ignoriert und die Paarung aus den Mannschaftsergebnissen verwendet
			if ($paarung > 0) {
				$tln_paarung[$spieler[$s-1]->tln_nr] = $paarung;
			} else {
				if (isset($tln_paarung[$spieler[$s-1]->tln_nr])) $paarung = $tln_paarung[$spieler[$s-1]->tln_nr];
			}
			if ($paarung == 0 AND 
				isset($spieler[$s-1]->tln_nr) AND  
				$spieler[$s-1]->tln_nr > 0) {
				for ($ip = 1; $ip <= count($swt_man); $ip++) {
					if (!isset($swt_man[$ip]['heim']) OR !isset($swt_man[$ip]['gast'])) break; 
					if ($spieler[$s-1]->tln_nr == $swt_man[$ip]['heim'] OR $spieler[$s-1]->tln_nr == $swt_man[$ip]['gast']) {
						$paarung = $ip;
					}
					if ($paarung > 0) break;
				}
			}
		if ($paarung != 0) {
			$attribut	= CLMSWT::readInt ($swt, $offset + 15);
			if ($attribut == 34 OR $attribut == 2) $kampflos	= 2; else $kampflos = 0;
			$spielfrei	= ($attribut == 3 || $attribut == 51);
			$brett		= CLMSWT::readInt ($swt, $offset + 18);
			
			$mannschaft = $spieler[$s-1]->tln_nr;
			if ($gegner > 0 AND isset($spieler[$gegner-1])) $gegner_mannschaft = $spieler[$gegner-1]->tln_nr;
			else $gegner_mannschaft = 0;
			if (empty ($paarung)) {
				for ($p = 1; $p <= $anz_paarungen; $p++) {
					$heim = $swt_man[$p]['heim'];
					$gast = $swt_man[$p]['gast'];
					
					if ($gegner_mannschaft == $heim) {
						$paarung = $p;
						$mannschaft = $gast;
						break;
					}
					if ($gegner_mannschaft == $gast) {
						$paarung = $p;
						$mannschaft = $heim;
						break;
					}
				}
			}
			
			if ($this->debug_ausgaben > 5) {
				echo "<pre>leere_bretter[$mannschaft]: ";
				print_r ($leere_bretter[$mannschaft]);
				echo "</pre>";
			}
			
			if (isset ($leere_bretter[$mannschaft])) { // es gibt leere Bretter (vorher!)
				if ($this->debug_ausgaben > 1) {
					echo "es gibt leere Bretter";
				}
				$verschieben = 0;
				foreach ($leere_bretter[$mannschaft] as $leeres_brett) {
					if ($leeres_brett < $brett) {
						$verschieben++;
					}
				}
				$brett -= $verschieben;
			}
			
			if ($spielfrei) { // spielfrei
				//echo "attribut: $attribut";
				$leere_bretter[$mannschaft][] = $brett;
				$brett = $anz_bretter + count ($leere_bretter[$mannschaft]);
			}
			
			if ($this->debug_ausgaben > 2) {
				echo "<pre>paarung, brett, spieler, mannschaft, ergebnis: $paarung, $brett, $s, $mannschaft, $ergebnis<br/>";
				echo "offset: $offset";
				for ($ofi = 0; $ofi <= 18; $ofi++) {
					$byte_value = CLMSWT::readInt ($swt, $offset + $ofi);
					if (!empty ($byte_value)) {
						echo "  swtfile byte $ofi: $byte_value<br/>";
					}
				}
				echo "</pre>";
			}

			/* if ($paarung == 0) { // Fallback für falsch (?) abgespeicherte Paarungsnummer 00 in SWT-Datei
//				echo "offset: $offset "; //DBG
				if ($gegner != 0) {
					$geg_id = $spieler[$gegner-1]->id;
					if ($brett != 0) {
						for ($p = 1; $p <= $anz_paarungen; $p++) {
							$heimspieler = $swt_data[$p]['hbrett_'.$brett];
							$gastspieler = $swt_data[$p]['gbrett_'.$brett];
							if ($heimspieler == $geg_id || $gastspieler == $geg_id) {
								$paarung = $p;
							//	$tmp_erg[$paarung][$brett][] = end ($tmp_erg[0][$brett]);
							}
						}
					}
					else { // Brettnummer ist ebenfalls 00 in der SWT-Datei
						for ($p = 1; $p <= $anz_paarungen; $p++) {
							for ($b = 1; $b <= $anz_bretter; $b++) {
								$heimspieler = $swt_data[$p]['hbrett_'.$brett];
								$gastspieler = $swt_data[$p]['gbrett_'.$brett];
								if ($heimspieler == $geg_id || $gastspieler == $geg_id) {
									$paarung = $p;
									$brett = $b;
								//	$tmp_erg[$paarung][$brett][] = end ($tmp_erg[0][0]);
								}
							}
						}
					}
					//$heim = $swt_man[$paarung]['heim'];
					//$gast = $swt_man[$paarung]['gast'];
				}
				unset ($tmp_erg[0]);
//				echo "<br/>paarung neu: $paarung"; //DBG
//				echo "heim: $heim, gast: $gast, "; //DBG
//				echo "mannschaft: $mannschaft"; //DBG
			} */
			}


			if ($paarung != 0) {
			
				$offsets[$paarung][$brett][] = $offset;
			
				// Heim- und Gastmannschaft setzen
				$heim = $swt_man[$paarung]['heim'];
				$gast = $swt_man[$paarung]['gast'];
			if ($this->debug_ausgaben > 2) {
				echo "<pre>paarung, brett, spieler: $paarung, $brett, $s -- ergebnis, attribut, kampflos: $ergebnis, $attribut, $kampflos<br/>"; echo "</pre>";
			}
			  if ($noBoardResults == '0') {
				// Einzelergebnis speichern				
				if		($ergebnis == 0)					{ $teil_erg = '?';		$trenn = '';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 1  && $kampflos == 0) { $teil_erg = '0';		$trenn = '-';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 2  && $kampflos == 0) { $teil_erg = '0,5';	$trenn = '-';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 3  && $kampflos == 0) { $teil_erg = '1';		$trenn = '-';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 4  && $kampflos == 0) { $teil_erg = '?';		$trenn = '';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 5  && $kampflos == 0) { $teil_erg = '0';		$trenn = '-';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 6  && $kampflos == 0) { $teil_erg = '0,5';	$trenn = '-';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 7  && $kampflos == 0) { $teil_erg = '1';		$trenn = '-';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 8  && $kampflos == 0) { $teil_erg = '?';		$trenn = '';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 9  && $kampflos == 0) { $teil_erg = '0';		$trenn = '-';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 10 && $kampflos == 0) { $teil_erg = '0,5';	$trenn = '-';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 11 && $kampflos == 0) { $teil_erg = '1';		$trenn = '-';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 12 && $kampflos == 0) { $teil_erg = '?';		$trenn = '';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 13 && $kampflos == 0) { $teil_erg = '0';		$trenn = '-';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 14 && $kampflos == 0) { $teil_erg = '0,5';	$trenn = '-';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 15 && $kampflos == 0) { $teil_erg = '1';		$trenn = '-';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 1  && $kampflos == 2) { $teil_erg = '-';		$trenn = '/';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 3  && $kampflos == 2) { $teil_erg = '+';		$trenn = '/';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 5  && $kampflos == 2) { $teil_erg = '-';		$trenn = '/';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 10 && $kampflos == 2) { $teil_erg = '-';		$trenn = '/';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 13 && $kampflos == 2) { $teil_erg = '-';		$trenn = '/';  	$teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 15 && $kampflos == 2) { $teil_erg = '+';		$trenn = '/';  	$teil_ergk = '';		$trennk = ''; }
				else										{ $teil_erg = '?';		$trenn = '';  	$teil_ergk = '';		$trennk = ''; }
				// Mannschaftsergebnis speichern
				if ($teil_erg != '?') {
				if		($ergebnis == 0)					{ $teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 1  && $kampflos == 0) { $teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 2  && $kampflos == 0) { $teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 3  && $kampflos == 0) { $teil_ergk = '';		$trennk = ''; }
				elseif	($ergebnis == 4  && $kampflos == 0) { $teil_ergk = '0';		$trennk = '-'; }
				elseif	($ergebnis == 5  && $kampflos == 0) { $teil_ergk = '0';		$trennk = '-'; }
				elseif	($ergebnis == 6  && $kampflos == 0) { $teil_ergk = '0.5';		$trennk = '-'; }
				elseif	($ergebnis == 7  && $kampflos == 0) { $teil_ergk = '0';		$trennk = '-'; }
				elseif	($ergebnis == 8  && $kampflos == 0) { $teil_ergk = '0,5';		$trennk = '-'; }
				elseif	($ergebnis == 9  && $kampflos == 0) { $teil_ergk = '0,5';		$trennk = '-'; }
				elseif	($ergebnis == 10 && $kampflos == 0) { $teil_ergk = '0,5';		$trennk = '-'; }
				elseif	($ergebnis == 11 && $kampflos == 0) { $teil_ergk = '0,5';		$trennk = '-'; }
				elseif	($ergebnis == 12 && $kampflos == 0) { $teil_ergk = '1';		$trennk = '-'; }
				elseif	($ergebnis == 13 && $kampflos == 0) { $teil_ergk = '1';		$trennk = '-'; }
				elseif	($ergebnis == 14 && $kampflos == 0) { $teil_ergk = '1';		$trennk = '-'; }
				elseif	($ergebnis == 15 && $kampflos == 0) { $teil_ergk = '1';		$trennk = '-'; }
				elseif	($ergebnis == 1  && $kampflos == 2) { $teil_ergk = '-';		$trennk = '/'; }
				elseif	($ergebnis == 3  && $kampflos == 2) { $teil_ergk = '';		$trennk = '/'; }
				elseif	($ergebnis == 5  && $kampflos == 2) { $teil_ergk = '-';		$trennk = '/'; }
				elseif	($ergebnis == 10 && $kampflos == 2) { $teil_ergk = '-';		$trennk = '/'; }
				elseif	($ergebnis == 13 && $kampflos == 2) { $teil_ergk = '';		$trennk = '/'; }
				elseif	($ergebnis == 15 && $kampflos == 2) { $teil_ergk = '+';		$trennk = '/'; }
				else										{ $teil_ergk = '';		$trennk = ''; }
				//if		($ergebnis >= 8 AND $ergebnis <=11) $emsum = .5;
				//elseif	($ergebnis >= 12 AND $ergebnis <=15) $emsum = 1;
				//else	$emsum = 0;
				} //else {
				if		($ergebnis == 2 OR $ergebnis == 6 OR $ergebnis == 10 OR $ergebnis == 14) $emsum = .5;
				elseif	($ergebnis == 3 OR $ergebnis == 7 OR $ergebnis == 11 OR $ergebnis == 15) $emsum = 1;
				else	$emsum = 0;
				//}
			  }
				if		($ergebnis >= 8 AND $ergebnis <=11) $mmsum = .5;
				elseif	($ergebnis >= 12 AND $ergebnis <=15) $mmsum = 1;
				else	$mmsum = 0;
			  if ($noBoardResults == '0') {
				$tmp_erg[$paarung][$brett][] = $teil_erg;
				if (!isset ($tmp_trenn[$paarung][$brett]) OR $tmp_trenn[$paarung][$brett] == '-') {
					$tmp_trenn[$paarung][$brett] = $trenn;
				}
				$tmp_ergk[$paarung][$brett][] = $teil_ergk;
				if (!isset ($tmp_trennk[$paarung][$brett]) OR $tmp_trennk[$paarung][$brett] == '-') {
					$tmp_trennk[$paarung][$brett] = $trennk;
				}
			
			
				//if (!isset ($swt_data[$paarung]['hbrett_'.$brett])) {
	
				if (!isset ($swt_data[$paarung]['hbrett_'.$brett]) AND !isset ($swt_data[$paarung]['gbrett_'.$brett])) {
					if ($mannschaft == $heim) {
						$swt_data[$paarung]['hfarbe_'.$brett] = $farbe;
						$swt_data[$paarung]['gfarbe_'.$brett] = $gegfarbe;
						if ($ergebnis != 0) $swt_data[$paarung]['hbrett_'.$brett] = $spieler[$s-1]->id;
						else $swt_data[$paarung]['hbrett_'.$brett] = 0;
						if (isset($spieler[$gegner-1])) $swt_data[$paarung]['gbrett_'.$brett] = $spieler[$gegner-1]->id;
						else $swt_data[$paarung]['gbrett_'.$brett] = 0;
						$heim_erg[$paarung][$brett] = $teil_erg;
						if (!isset($swt_data[$paarung]['hemsum'])) $swt_data[$paarung]['hemsum'] = 0;
						$swt_data[$paarung]['hemsum'] += $emsum;
						$heim_ergk[$paarung][$brett] = $teil_ergk;
						if (!isset($swt_data[$paarung]['hmmsum'])) $swt_data[$paarung]['hmmsum'] = 0;
						$swt_data[$paarung]['hmmsum'] += $mmsum;
					}
					elseif ($mannschaft == $gast) {
						$swt_data[$paarung]['hfarbe_'.$brett] = $gegfarbe;					
						$swt_data[$paarung]['gfarbe_'.$brett] = $farbe;
						if (isset($spieler[$gegner-1])) $swt_data[$paarung]['hbrett_'.$brett] = $spieler[$gegner-1]->id;
						else $swt_data[$paarung]['hbrett_'.$brett] = 0;
						if ($ergebnis != 0) $swt_data[$paarung]['gbrett_'.$brett] = $spieler[$s-1]->id;
						else $swt_data[$paarung]['gbrett_'.$brett] = 0;
						$gast_erg[$paarung][$brett] = $teil_erg;
						if (!isset($swt_data[$paarung]['gemsum'])) $swt_data[$paarung]['gemsum'] = 0;
						$swt_data[$paarung]['gemsum'] += $emsum;
						$gast_ergk[$paarung][$brett] = $teil_ergk;
						if (!isset($swt_data[$paarung]['gmmsum'])) $swt_data[$paarung]['gmmsum'] = 0;
						$swt_data[$paarung]['gmmsum'] += $mmsum;
					}
//					echo "p,s,b: $paarung,$s,$brett "; //DBG
				}
				else {
					// ggf. noch prüfen ob konsistent mit den zuvor eingelesenen Daten
					// ...
				
					if ($mannschaft == $heim) {
						//$swt_data[$paarung]['hfarbe_'.$brett] = $farbe;
						$erg_str[$paarung][$brett] = $tmp_erg[$paarung][$brett][1] . $trenn . $tmp_erg[$paarung][$brett][0];
						if (!isset($swt_data[$paarung]['hemsum'])) $swt_data[$paarung]['hemsum'] = 0;
						$swt_data[$paarung]['hemsum'] += $emsum;
						$erg_strk[$paarung][$brett] = $tmp_ergk[$paarung][$brett][1] . $trennk . $tmp_ergk[$paarung][$brett][0];
						if (!isset($swt_data[$paarung]['hmmsum'])) $swt_data[$paarung]['hmmsum'] = 0;
						$swt_data[$paarung]['hmmsum'] += $mmsum;
					}
					elseif ($mannschaft == $gast) {
						//$swt_data[$paarung]['gfarbe_'.$brett] = $farbe;
						$erg_str[$paarung][$brett] = $tmp_erg[$paarung][$brett][0] . $trenn . $tmp_erg[$paarung][$brett][1];
						if (!isset($swt_data[$paarung]['gemsum'])) $swt_data[$paarung]['gemsum'] = 0;
						$swt_data[$paarung]['gemsum'] += $emsum;
						$erg_strk[$paarung][$brett] = $tmp_ergk[$paarung][$brett][0] . $trennk . $tmp_ergk[$paarung][$brett][1];
						if (!isset($swt_data[$paarung]['gmmsum'])) $swt_data[$paarung]['gmmsum'] = 0;
						$swt_data[$paarung]['gmmsum'] += $mmsum;
					}
//					else { //DBG
//						echo "offset: $offset"; //DBG
//					} //DBG
				}
			  }
			  if ($noBoardResults == '1') {
					if ($mannschaft == $heim) {
						if (!isset($swt_data[$paarung]['hmmsum'])) $swt_data[$paarung]['hmmsum'] = 0;
						$swt_data[$paarung]['hmmsum'] += $mmsum;
					} elseif ($mannschaft == $gast) {
						if (!isset($swt_data[$paarung]['gmmsum'])) $swt_data[$paarung]['gmmsum'] = 0;
						$swt_data[$paarung]['gmmsum'] += $mmsum;
					}
			  }
			} // ENDE if ($paarung != 0)
			
		  }
			$offset += $abstand;

		}
	  if ($noBoardResults == '0') {
		for ($p = 1; $p <= $anz_paarungen; $p++) {
		
			// bei ungerader Mannschaftsanzahl: spielfreie Paarung korrigieren
/*			if ($ungerade && $p == $anz_paarungen) {
				for ($b = 1; $b <= $anz_bretter; $b++) {
					if ($mturnier == 0) { // in einer vollrundigen Liga spielfrei
						$erg_str[$p][$b] = 'spielfrei';
					}
					else { // sonst kampfloser Sieg +/-
						$erg_str[$p][$b] = '+/-';
					}
				}
			} */
			$erg_string_default = '---';
			$erg_string_switch = 0;
			for ($b = 1; $b <= $anz_bretter; $b++) {
				
				if (isset ($erg_str[$p][$b])) {
					$erg_string = $erg_str[$p][$b];
					$fixed[$p][$b] = '';
				}
				else {
					
					if (isset ($heim_erg[$p][$b])) {
						switch ($heim_erg[$p][$b]) {
						
							case '0':
								$erg_string = '0-1';
								break;
								
							case '0,5':
								$erg_string = '0,5-0,5';
								break;
								
							case '1':
								$erg_string = '1-0';
								break;
								
							case '-':
								if ($tmp_trenn[$p][$b] == '-') {
									$erg_string = '---';
								}
								else {
									$erg_string = '-/+';
								}
								break;
								
							case '+':
								$erg_string = '+/-';
								break;
								
							case '':
								$erg_string = 'spielfrei';
								break;
								
							default:
								$erg_string = '---';
								break;
							
						}
					}
					elseif (isset ($gast_erg[$p][$b])) {
						switch ($gast_erg[$p][$b]) {
						
							case '0':
								$erg_string = '1-0';
								break;
								
							case '0,5':
								$erg_string = '0,5-0,5';
								break;
								
							case '1':
								$erg_string = '0-1';
								break;
								
							case '-':
								if ($tmp_trenn[$p][$b] == '-') {
									$erg_string = '---';
								}
								else {
									$erg_string = '+/-';
								}
								break;
								
							case '+':
								$erg_string = '-/+';
								break;
								
							case '':
								$erg_string = 'spielfrei';
								break;
								
							default:
								$erg_string = '---';
								break;
							
						}
					}
					else {
						$erg_string = '---';
					}				
					if (isset($erg_str[$p][$b])) $fixed[$p][$b] = ' (war: ' . $erg_str[$p][$b] . ')';
					else $fixed[$p][$b] = ' (war: leer )';
					$erg_str[$p][$b] = $erg_string;	
				}
				
				if ($erg_string != $erg_string_default) { $erg_string_switch = 1;}
				elseif ($erg_string_switch == 1) { $erg_string = '-/-'; }    // aus --- wird -/-
				
				$sql = ' SELECT eid FROM #__clm_ergebnis'
						. ' WHERE erg_text = "'.$erg_string.'"';
				$objs = $this->_getList ($sql);
				if (!isset($objs[0])) {
					$erg_id = 7;
				} else {
					$erg_id = $objs[0]->eid;
				}

				$swt_data[$p]['ergstr_'.$b] = $erg_string;
				$swt_data[$p]['erg_'.$b] = $erg_id;
			}
		}
		//echo "<br>korr: ";
		for ($p = 1; $p <= $anz_paarungen; $p++) {
		
			$erg_string_default = '---';
			$erg_string_switch = 0;
			for ($b = 1; $b <= $anz_bretter; $b++) {
				//echo "<br>erg_strk[$p][$b] ".$erg_strk[$p][$b]."  heim_ergk[$p][$b] ".$heim_ergk[$p][$b]."  gast_ergk[$p][$b] ".$heim_ergk[$p][$b];
				if (isset ($erg_strk[$p][$b])) {
					$erg_string = $erg_strk[$p][$b];
					$fixedk[$p][$b] = '';
				}
				else {
					
					if (isset ($heim_ergk[$p][$b])) {
						switch ($heim_ergk[$p][$b]) {
							case '-':
								if ($tmp_trennk[$p][$b] == '-') {
									$erg_string = '---';
								}
								else {
									$erg_string = '-/+';
								}
								break;
								
							case '+':
								$erg_string = '+/-';
								break;
								
							case '':
								$erg_string = '';
								break;
								
							default:
								$erg_string = '---';
								break;
							
						}
					}
					elseif (isset ($gast_ergk[$p][$b])) {
						switch ($gast_ergk[$p][$b]) {
						
							case '-':
								if ($tmp_trennk[$p][$b] == '-') {
									$erg_string = '---';
								}
								else {
									$erg_string = '+/-';
								}
								break;
								
							case '+':
								$erg_string = '-/+';
								break;
								
							case '':
								$erg_string = '';
								break;
								
							default:
								$erg_string = '---';
								break;
							
						}
					}
					else {
						$erg_string = '';
					}
					if (isset($erg_strk[$p][$b])) $fixedk[$p][$b] = ' (war: ' . $erg_strk[$p][$b] . ')';
					else $fixedk[$p][$b] = ' (war: leer )';
					$erg_strk[$p][$b] = $erg_string;	
				}
				
				if ($erg_string != $erg_string_default) { $erg_string_switch = 1;}
				elseif ($erg_string_switch == 1) { $erg_string = '-/-'; }    // aus --- wird -/-
				
				if ($swt_data[$p]['ergstr_'.$b] == $erg_string) $erg_string = '';
				if ($erg_string == '1-0') $erg_string = '+/-';
				if ($erg_string == '0-1') $erg_string = '-/+';
				$swt_data[$p]['ergstrk_'.$b] = $erg_string;
				
				$sql = ' SELECT eid FROM #__clm_ergebnis'
						. ' WHERE erg_text = "'.$erg_string.'"';
				$objs = $this->_getList ($sql);
				if (isset($objs[0])) $erg_id = $objs[0]->eid;
				else $erg_id = '';
		
				$swt_data[$p]['ergk_'.$b] = $erg_id;
				//echo "<br>swt_data[$p][ergstrk_$b] ".$swt_data[$p]['ergstrk_'.$b]."  "."swt_data[$p][ergk_$b] ".$swt_data[$p]['ergk_'.$b];
			}
		}
	  }
		if ($this->debug_ausgaben > 2) {
			echo "<pre>swt_data in getDataSWTspl (): ";
			print_r ($swt_data);
			echo "</pre>";
		}
		if ($this->debug_ausgaben > -1 && $during_store == 0) {
		
			$swt_id = JRequest::getVar ('swt_id', 0, 'default', 'int');
			$sql = ' SELECT name FROM #__clm_swt_mannschaften'
					. ' WHERE swt_id = "'.$swt_id.'"'
					. ' ORDER BY tln_nr ASC';
			$tmp_man = $this->_getList ($sql);
			$sql2 = ' SELECT d.Spielername as name, m.id as id, m.snr as snr'
					. ' FROM #__clm_dwz_spieler as d'
					. ' LEFT JOIN #__clm_swt_meldeliste_spieler as m'
					. ' ON m.zps = d.ZPS AND m.mgl_nr = d.Mgl_Nr'
					. ' WHERE swt_id = "'.$swt_id.'"';
			$tmp_spl = $this->_getList ($sql2);

			echo "<br/><br/>";
			for ($p = 1; $p <= $anz_paarungen; $p++) {
				$heim_name = $tmp_man[$swt_man[$p]['heim']-1]->name;
				$gast_name = $tmp_man[$swt_man[$p]['gast']-1]->name;
				echo "<table class='sjnrw_table'>";
				echo "<tr><td colspan=6>Paarung $p</td></tr>";
				echo "<tr><td></td>";
				echo "<td>".$swt_man[$p]['heim']."</td><td>".$heim_name."</td>";
				echo "<td>".$swt_man[$p]['gast']."</td><td>".$gast_name."</td>";
				echo "<td>Ergebnis</td></tr>";
				echo "<br><br>p $p:"; var_dump($swt_man[$p]);
				if ($noBoardResults == '0') {
				for ($b = 1; $b <=  $anz_bretter; $b++) {
					foreach ($tmp_spl as $spl) {
						if ($spl->id == $swt_data[$p]['hbrett_'.$b]) {
							$hname = $spl->name;
							$hnr = $spl->snr;
						}
						elseif ($spl->id == $swt_data[$p]['gbrett_'.$b]) {
							$gname = $spl->name;
							$gnr = $spl->snr;
						}
						if (isset ($gname) && isset ($hname)) {
							break;
						}
					}
					echo "<tr>";
					echo "<td>$b</td>";
					echo "<td>".$swt_data[$p]['hbrett_'.$b]."</td><td>".$hnr." - ".$hname."</td>";
					echo "<td>".$swt_data[$p]['gbrett_'.$b]."</td><td>".$gnr." - ".$gname."</td>";
					echo "<td>".$erg_str[$p][$b].$fixed[$p][$b]." swt_data: ";
					echo $swt_data[$p]['ergstr_'.$b]." (".$swt_data[$p]['erg_'.$b].")</td>";
					echo "<td>".$erg_strk[$p][$b].$fixedk[$p][$b]." swt_data: ";
					echo $swt_data[$p]['ergstrk_'.$b]." (".$swt_data[$p]['ergk_'.$b].")</td>";
					echo "<td>".$offsets[$p][$b][0].", ".$offsets[$p][$b][1]."</td>";
					echo "</tr>";
					unset ($hname);
					unset ($gname);
			  } }
				echo "</table>";
				echo "<br/><br/>";
			}
		}
			

		$this->_swt_data_spl = $swt_data;
		return $this->_swt_data_spl;
	
    }
    

	function getDataSWTdb () {

		if (!empty ($this->_swt_db_data)) {
			return $this->_swt_db_data;
		}
		
		$swt_id = JRequest::getVar( 'swt_id', '', 'default', 'int' );
		$sql = ' SELECT id, name as liga_name, teil as anz_mannschaften, stamm as anz_bretter, ersatz as anz_ersatzspieler, durchgang as anz_durchgaenge, runden as anz_runden, sieg, remis, nieder, antritt, man_sieg, man_remis, man_nieder, man_antritt, sieg_bed'
				. ' FROM #__clm_swt_liga'
				. ' WHERE id = '.$swt_id;

		$objs = $this->_getList ($sql);
		$obj = $objs[0];

		$spalten = array ('liga_name', 'anz_mannschaften', 'anz_bretter', 'anz_ersatzspieler', 'anz_durchgaenge', 'anz_runden', 'sieg', 'remis', 'nieder', 'antritt', 'man_sieg', 'man_remis', 'man_nieder', 'man_antritt', 'sieg_bed');
		foreach ($spalten as $spalte) {
			$swt_db_data[$spalte] = $obj->$spalte;
		}
		
		
		$sql = ' SELECT s.spielerid, s.id as id, m.tln_nr as tln_nr, s.zps as zps, s.mgl_nr as mgl_nr'
				. ' FROM #__clm_swt_meldeliste_spieler as s'
				. ' LEFT JOIN #__clm_swt_mannschaften as m'
				. ' ON s.man_id = m.id'
				. ' WHERE s.swt_id = '.$swt_id
				. ' AND m.swt_id = '.$swt_id
				. ' ORDER BY s.spielerid ASC';
		
		$objs = $this->_getList ($sql);
		//$swt_db_data['spieler'] = $objs;
		$swt_db_data['spieler'] = array();
		foreach ($objs as $objs1) {
			$swt_db_data['spieler'][$objs1->spielerid - 1] = $objs1;
			//echo "<br>spieler-o: ".($objs1->spielerid - 1)."  "; var_dump($swt_db_data['spieler'][$objs1->spielerid - 1]); 
			}
		
		$this->_swt_db_data = $swt_db_data;
		return $swt_db_data;

	}

}
