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
defined('_JEXEC') or die('Restricted access');

class CLMModelSWTLigainfo extends JModelLegacy {

	function __construct () {
	
		parent::__construct ();
		
		// Konfigurationsparameter auslesen
		$config = clm_core::$db->config();
		$rang	= $config->rangliste;
		$sl_mail= $config->sl_mail;
		
		// Listen aus der Datenbank auslesen
		$db_data = $this->dbQuery ();
		$db_sllist = $db_data['sllist'];
		$db_saisonlist = $db_data['saisonlist'];
		$db_glist = $db_data['glist'];
		
		// Daten an View weitergeben
		$this->setState ('rang', $rang);
		$this->setState ('sl_mail', $sl_mail);

//		$this->setState ('saison_id', clm_core::$load->request_int('filter_saison', 0));
		$this->setState ('saison_id', clm_core::$load->request_int('sid', 0));
				
		$this->setState ('db_sllist', $db_sllist);
		$this->setState ('db_saisonlist', $db_saisonlist);
		$this->setState	('db_glist', $db_glist);

	}
	
	function getDefault () {
	
		$default['catidAlltime'] 			= '0';
		$default['catidEdition'] 			= '0';
		$default['sl']						= '0';
		$default['runden_modus']			= '0';
		$default['anz_aufsteiger']			= '0';
		$default['anz_moegl_aufsteiger']	= '0';
		$default['anz_absteiger']			= '0';
		$default['anz_moegl_absteiger']		= '0';
		
		$default['mail']					= '0';
		$default['order']					= '0';
		$default['published']				= '0';
		$default['ordering']				= '0';
		$default['ersatz_regel']			= '0';
		$default['anzeige_ma']				= '0';
		$default['params']					= '';
		//Mit Daten aus Datenbank überschreiben, falls ein Liga geupdated wird
		if(clm_core::$load->request_int('update') == 1) {
			if ($lid = clm_core::$load->request_int('lid')) {
				$db		=JFactory::getDBO ();
				$select_query = '  SELECT * FROM #__clm_liga '
								.' WHERE id = '.$lid.'; ';
				$db->setQuery ($select_query);
				$ligaFromDatabase = $db->loadObject();
				//Standardwerte werden überschrieben
				$default['name'] 		= $ligaFromDatabase->name;
				$default['sid'] 		= $ligaFromDatabase->sid;
				$default['catidAlltime'] 		= $ligaFromDatabase->catidAlltime;
				$default['catidEdition'] 		= $ligaFromDatabase->catidEdition;
				$default['sl'] 			= $ligaFromDatabase->sl;
				$default['runden_modus']			= $ligaFromDatabase->runden_modus;
				$default['anz_aufsteiger']			= $ligaFromDatabase->auf;
				$default['anz_moegl_aufsteiger']	= $ligaFromDatabase->auf_evtl;
				$default['anz_absteiger']			= $ligaFromDatabase->ab;
				$default['anz_moegl_absteiger']		= $ligaFromDatabase->ab_evtl;
				$default['mail']					= $ligaFromDatabase->mail;
				$default['order']		= $ligaFromDatabase->order;
				$default['published'] 	= $ligaFromDatabase->published;
				$default['ordering']	= $ligaFromDatabase->ordering;
				$default['ersatz_regel'] = $ligaFromDatabase->ersatz_regel;
				$default['anzeige_ma']	= $ligaFromDatabase->anzeige_ma;
				$default['bem_int'] 	= $ligaFromDatabase->bem_int;
				$default['bemerkungen'] = $ligaFromDatabase->bemerkungen;
				$default['params'] 		= $ligaFromDatabase->params;			
			}
		}
		$_POST['str_params'] = $default['params'];

		//Liga-Parameter aufbereiten
		$paramsStringArray = explode("\n", $default['params']);
		$default['params'] = array();
		foreach ($paramsStringArray as $value) {
			$ipos = strpos ($value, '=');
			if ($ipos !==false) {
				$key = substr($value,0,$ipos);
				if (substr($key,0,2) == "\'") $key = substr($key,2,strlen($key)-4);
				if (substr($key,0,1) == "'") $key = substr($key,1,strlen($key)-2);
				$default['params'][$key] = substr($value,$ipos+1);
			}
		}	
		if (!isset($default['params']['anz_sgp']))  {   //Standardbelegung
			$default['params']['anz_sgp'] = 1; }
		if (!isset($default['params']['color_order']))  {   //Standardbelegung
			$default['params']['color_order'] = '1'; }

		return $default;
	}
	
	function getDataSWT () {
		
		jimport( 'joomla.filesystem.file' );
		
		$mturnier = clm_core::$load->request_int('mturnier', 0);
		
		// Namen und Verzeichnis der SWT-Datei auslesen
		$filename = clm_core::$load->request_string('swt_file', '');
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'swt' . DIRECTORY_SEPARATOR;
		
		$swt = $path.$filename;
		
		// Allgemeine Turnierdaten
		//$swt_data['liga_name']				= $this->_SWTReadName ($swt, 245, 60);
		$swt_data['liga_name']				= clm_core::$load->utf8encode($this->_SWTReadName ($swt, 245, 60));
		$swt_data['anz_mannschaften']		= $this->_SWTReadInt ($swt, 602);
		$swt_data['anz_bretter']			= $this->_SWTReadInt ($swt, 604);
		$swt_data['anz_spieler']			= $this->_SWTReadInt ($swt, 1327);
		$swt_data['anz_ersatz']				= $swt_data['anz_spieler'] - $swt_data['anz_bretter'];
		$swt_data['anz_runden']				= $this->_SWTReadInt ($swt, 1);
		$swt_data['durchgang']				= $this->_SWTReadInt ($swt, 598);
		$swt_data['anz_durchgaenge']		= $this->_SWTReadInt ($swt, 599);
		$swt_data['heimrecht_vertauscht']	= $this->_SWTReadBool ($swt, 1329);

		//echo '<br>liganame: '.$swt_data['liga_name'];
		//echo '<br>amannschaften: '.$swt_data['anz_mannschaften'];
		//echo '<br>abretter: '.$swt_data['anz_bretter'];
		//echo '<br>aspieler: '.$swt_data['anz_spieler'];
		//echo '<br>aersatz: '.$swt_data['anz_ersatz'];
		//echo '<br>arunden: '.$swt_data['anz_runden'];
		//echo '<br>durchgang: '.$swt_data['durchgang'];
		//echo '<br>adurchg: '.$swt_data['anz_durchgaenge'];
		//echo '<br>hrvtauscht: '.$swt_data['heimrecht_vertauscht'].'<br>';

		if ($mturnier == 1) {
			//echo "mturnier<br/>";
			$swt_data['anz_mannschaften'] = CLMSWT::readInt ($swt, 1332);
			//echo "anz_mannschaften: ";
			//echo $swt_data['anz_mannschaften'];
		}
		
		// Rundenmodus
		$swt_modus = CLMSWT::readInt ($swt, 596);
		$clm_modus = array (3, 1, 4, 4);
		/* SWT -> CLM	(Beschreibung)
			0  ->  3	 Schweizer System
			1  ->  1	 vollrundig nach FIDE
			   ->  2	 vollrundig mit zentr. Endrunde
			2  ->  4	 KO (1. Rnd Schweizer System)
			3  ->  4	 KO (ohne Schweizer System) */
		if ($swt_modus >= count ($clm_modus)) {
			$swt_modus = 1; // vollrundig als Standard
		}
		$swt_data['runden_modus'] = $clm_modus[$swt_modus];
		
		//echo "runden_modus: ".$swt_data['runden_modus'];
		
		// Ergebniswertung
		$swt_einzelwertung = $this->_SWTReadInt ($swt, 1336);
		$swt_data['std_wertung'] = false;
		switch ($swt_einzelwertung) {
			case 0:
				$swt_data['std_wertung'] = true;
				break;
				
			case 1:
				$punkte = array (3, 1, 0, 0);
				break;
				
			case 2: // Remis weiss = 1, schwarz = 1.5
				$punkte = array (3, 1.5, 0, 0); // da im CLM nicht moeglich
				break;
				
			case 3: // Punkte schwarz werden ignoriert, da im CLM nicht moeglich!
				$sieg			= $this->_SWTReadInt ($swt, 5494);
				$remis			= $this->_SWTReadInt ($swt, 5498);
				$verlust		= $this->_SWTReadInt ($swt, 5502);
				$nichtantritt	= $this->_SWTReadInt ($swt, 5506);
				
				$antritt		= $verlust - $nichtantritt;

				$punkte = array ($sieg, $remis, $verlust, $nichtantritt);
				break;
		}
		
		// wenn keine Standard-Wertung, dann Punkte berechnen
		if ($swt_data['std_wertung'] == false) {
			// SWT-Punkte
			$sieg			= $punkte[0];
			$remis			= $punkte[1];
			$verlust		= $punkte[2];
			$nichtantritt	= $punkte[3];
		
			// CLM-Punkte berechnen
			$swt_data['antrittspunkte']	= $antritt;
			$swt_data['verlustpunkte']	= $verlust - $antritt; // = $nichtantritt;
			$swt_data['remispunkte']	= $remis - $antritt;
			$swt_data['siegpunkte']		= $sieg - $antritt;
		}
		
		// Mannschaftsergebniswertung
		$swt_mannschaftswertung = $this->_SWTReadInt ($swt, 1338);
		$swt_data['man_std_wertung'] = false;
		$man_antritt = 0;
		switch ($swt_mannschaftswertung) {
			case 0:
				$swt_data['man_std_wertung'] = true;
				break;
				
			case 1:
				$man_punkte = array (3, 1, 0, 0);
				break;
				
			case 2:
				$man_sieg			= $this->_SWTReadInt ($swt, 5514);
				$man_remis			= $this->_SWTReadInt ($swt, 5518);
				$man_verlust		= $this->_SWTReadInt ($swt, 5522);
				$man_nichtantritt	= $this->_SWTReadInt ($swt, 5526);
				
				$man_antritt		= $man_verlust - $man_nichtantritt;

				$man_punkte = array ($man_sieg, $man_remis, $man_verlust, $man_nichtantritt);
				break;
		}
		
		// wenn keine Standard-Wertung, dann Punkte berechnen
		if ($swt_data['man_std_wertung'] == false) {
			// SWT-Punkte
			$man_sieg			= $man_punkte[0];
			$man_remis			= $man_punkte[1];
			$man_verlust		= $man_punkte[2];
			$man_nichtantritt	= $man_punkte[3];
		
			// CLM-Punkte berechnen
			$swt_data['man_antrittspunkte']	= $man_antritt;
			$swt_data['man_verlustpunkte']	= $man_verlust - $man_antritt; // = $man_nichtantritt;
			$swt_data['man_remispunkte']	= $man_remis - $man_antritt;
			$swt_data['man_siegpunkte']		= $man_sieg - $man_antritt;
		}
		
		// Siegbedingungen
		$sieg_bed = $this->_SWTReadInt ($swt, 623);
		$swt_data['sieg_bed'] = 1; // Mannschaft mit meisten BP gewinnt
		if ($sieg_bed == 255) { // Mindespunktzahl (mehr als die Haelfte der moeglichen Punkte) muss erreicht werden
			$swt_data['sieg_bed'] = 2;
		}
		
		// Feinwertungen
		if ($swt_data['runden_modus'] == 3) { // Schweizer System
			$man_zweit = $this->_SWTReadInt ($swt, 613);
			$man_dritt = $this->_SWTReadInt ($swt, 615);
		}
		else {
			$man_zweit = $this->_SWTReadInt ($swt, 614);
			$man_dritt = $this->_SWTReadInt ($swt, 616);
		}
		/* SWT -> CLM	(Beschreibung)
			 1 ->  9	 Brettpunkte
			 2 ->  5	 Brettpunkte
			 3 ->  1	 Buchholz
			    > 11	 Buchholz (1 Streichergebnis)
			 4 ->  2	 Buchholz-Summe
			( 5 ->  3	 Sonneborn-Berger bis 2014 )
			 5 ->  23	 Sonneborn-Berger
			14 ->  4	 Anzahl der Siege
			15 -> 25     Direkter Vergleich
			16 -> 10	 Berliner Wertung    (alt 6)
			18 -> 10	 Berliner Wertung (dir. Vergleich)   (alt 6) */
		$clm_fein = array (0 => 0, 1 => 9, 2 => 5, 3 => 1, 4 => 2, 5 => 23, 14 => 4, 15 => 25, 16 => 10, 18 => 10);
		
		$swt_data['tiebr1'] = $clm_fein[$man_zweit];
		$swt_data['tiebr2'] = $clm_fein[$man_dritt];
		$swt_data['tiebr3'] = 0;
		
		// Streichwertung bei Buchholz
		$anzStreichwertungen = $this->_SWTReadInt ($swt, 9);
		if ($swt_data['tiebr1'] == 1 AND $anzStreichwertungen == 1) $swt_data['tiebr1'] = 11;
		if ($swt_data['tiebr2'] == 1 AND $anzStreichwertungen == 1) $swt_data['tiebr2'] = 11;

		//Pseudo-DWZ
		$swt_data['pseudo_dwz'] = $this->_SWTReadInt($swt,626,2);

		//Ranglistenkorrektur
		if ($this->_SWTReadInt($swt,675) == 0) $swt_data['optionTiebreakersFideCorrect'] = false; 
		else $swt_data['optionTiebreakersFideCorrect'] = true;

		// Berliner Wertung
		
		if ($man_zweit == 16 || $man_dritt == 16) { // Berliner Wertung
			$swt_data['b_wertung'] = 3;
		}
		elseif ($man_zweit == 18 || $man_dritt == 18) { // Berliner Wertung (dir. Vergleich)
			$swt_data['b_wertung'] = 4;
		}
		else {
			$swt_data['b_wertung'] = 0;
		}
		
		return $swt_data;
		
	}
	
	
	// Listen aus der DB auslesen (SL, Saison, Ranglisten)
	function dbQuery () {
		$db			=JFactory::getDBO ();
		$user		=JFactory::getUser ();
		$option 	= clm_core::$load->request_string( 'option' );
		$section 	= clm_core::$load->request_string( 'section' );
		$mturnier 	= clm_core::$load->request_int('mturnier', 0);
	    $clmAccess = clm_core::$access;
		
		// Der Besitzer des Turniers muss dieses auch Editieren dürfen
		if ($mturnier == 0) 
		{
			$accesspoint = 'BE_league_edit_result';
		}
		else 
		{
			$accesspoint = 'BE_teamtournament_edit_result';
		} 
		$db_data['sllist'] = $clmAccess->userlist($accesspoint,'>0');
		if($db_data['sllist'] === false) {
			$this->setMessage( $db->getErrorMsg(), 'warning' );
			$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
			return;
		}

//		$filter_saison = clm_core::$load->request_int('filter_saison', 0);
		$filter_saison = clm_core::$load->request_int('sid', 0);
		//$sql = 'SELECT id as sid, name FROM #__clm_saison WHERE archiv = 0';
		$sql = 'SELECT id as sid, name FROM #__clm_saison ';
		if(clm_core::$load->request_int('update',0) == 1)
			$sql .= " WHERE id = ".$filter_saison;
		//$db->setQuery ($sql);
		if (!clm_core::$db->query($sql)) {
			$this->setMessage( $db->getErrorMsg(), 'warning' );
			$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
			return;
		}
		$db_data['saisonlist'] = clm_core::$db->loadObjectList($sql);

		// Ranglisten
		$query = ' SELECT id, Gruppe FROM #__clm_rangliste_name ';
		//$db->setQuery($query);
		if (!clm_core::$db->query($query)) {
			$this->setMessage( $db->getErrorMsg(), 'warning' );
			$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
			return;
		}
		$db_data['glist'] = clm_core::$db->loadObjectList($query);
		
		return $db_data;
		
	}
	
	function store () {
	
		// DB-Zugriff
		$db		=JFactory::getDBO ();
		
		// load the row from the db table
		$lid = clm_core::$load->request_string('lid');
		$row 		=JTable::getInstance( 'ligen', 'TableCLM' );	
		$row->load( $lid );

		//Liga-Parameter aufbereiten
		$default['params'] = clm_core::$load->request_string('str_params');
//		$paramsStringArray = explode("\n", $default['params']);
		$paramsStringArray = explode("\n", $row->params);
//		$paramsStringArray = explode(PHP_EOL, $default['params']);
		$default_params = array();
		foreach ($paramsStringArray as $value) {
			$ipos = strpos ($value, '=');
			if ($ipos !==false) {
				$key = substr($value,0,$ipos);
				//if (substr($key,0,2) == "\'") $key = substr($key,2,strlen($key)-4);
				//if (substr($key,0,1) == "'") $key = substr($key,1,strlen($key)-2);
				$default_params[$key] = substr($value,$ipos+1);
			}
		}	
		//Liga-Parameter aktualisieren
		$default_params['anz_sgp'] = clm_core::$load->request_string('anz_sgp');
		$default_params['color_order'] = clm_core::$load->request_string('color_order');
		$default_params['optionTiebreakersFideCorrect'] = clm_core::$load->request_string('optionTiebreakersFideCorrect');
		$default_params['pseudo_dwz'] = clm_core::$load->request_string('pseudo_dwz');
		$default_params['noOrgReference'] = clm_core::$load->request_string('noOrgReference', '0', 'default', 'string');
		$default_params['noBoardResults'] = clm_core::$load->request_string('noBoardResults', '0', 'default', 'string');
		if 	($default_params['noBoardResults'] == '1' AND $default_params['noOrgReference'] == '0') {
			$default_params['noOrgReference'] = '1';
			$_POST['noOrgReference'] = '1';	
		}
		if 	($default_params['noOrgReference'] == '1') $default_params['incl_to_season'] = '0';
		$now_date = date('Y-m-d');
		$default_params['import_date'] = $now_date;
		// Übernahme mit oder ohne DWZ
		$dwz_handling = clm_core::$load->request_string('dwz_handling','0');
		$_POST['dwz_handling'] = (string) $dwz_handling;
		if ($dwz_handling == '1') {
			$default_params['dwz_date'] = $now_date;
		} else {
			$default_params['dwz_date'] = '1970-01-01';
		}
		//Liga-Parameter zusammenfassen
		$paramsStringArray = array();
		foreach ($default_params as $key => $value) {
			//if (substr($key,0,2) == "\'") $key = substr($key,2,strlen($key)-4);
			//if (substr($key,0,1) == "'") $key = substr($key,1,strlen($key)-2);
			$paramsStringArray[] = $key.'='.$value;
		}
		$default['params'] = implode("\n", $paramsStringArray);
		$_POST['params'] = $default['params'];
		$mturnier = clm_core::$load->request_int('mturnier', 0);
		$_POST['liga_mt'] = (string) $mturnier;
		
		$spalten = array ( 'lid', 'name', 'catidEdition', 'catidAlltime', 'sl', 'sid', 'rang', 'teil', 'stamm', 'ersatz', 'runden', 'durchgang', 'runden_modus', 'heim',
		                   'sieg', 'remis', 'nieder', 'antritt', 'man_sieg', 'man_remis', 'man_nieder', 'man_antritt', 'sieg_bed',
						   'b_wertung', 'auf', 'auf_evtl', 'ab', 'ab_evtl', 'mail', 'sl_mail', 'order', 
						   'published', 'ordering', 'bem_int', 'liga_mt', 'tiebr1', 'tiebr2', 'tiebr3', 'ersatz_regel', 'anzeige_ma', 'params' );

		
		$fields = '';
		$values = '';
		foreach ($spalten as $spalte) {
			$fields .= '`'.$spalte.'`,';
			$values .= ' "'.clm_escape(clm_core::$load->request_string($spalte)).'",';
		}
		$fields = substr ($fields, 0, -1);
		$values = substr ($values, 0, -1);
		$insert_query = ' INSERT IGNORE INTO #__clm_swt_liga'
		              . ' ( ' . $fields . ' ) '
		              . ' VALUES ( ' . $values . ' ); ';
		
		//$db->setQuery ($insert_query);
		
		if (clm_core::$db->query($insert_query)) {
			$_POST['swt_id'] = clm_core::$db->insert_id();
			return true;
		}
		else {
			print $db->getErrorMsg ();
			return false;
		}
		
	}
	
	function _SWTReadName($file, $offset, $length){
		$i = 0;
		$name = '';
		//while(ord ($chr = JFile::read($file, false, 1, 8192, $offset+$i)) != 0 && $i < $length){
		while(ord ($chr = file_get_contents ($file, false, null, $offset+$i, 1)) != 0 AND $i < $length){
			$name .= $chr;
			$i++;
		}
		return $name;
	}
	
	function _SWTReadInt ($file, $offset, $length = 1) {
		$value = 0;
		for ($i = 0; $i < $length; $i++) {
			//$cur = ord (JFile::read ($file, false, 1, 8192, $offset+$i));
			$cur = ord(file_get_contents ($file, false, null, $offset+$i, 1));
			$value += $cur * pow (256, $i);
		}
		return $value;
	}
	
	function _SWTReadBool ($file, $offset) {
		if ($this->_SWTReadInt ($file, $offset, 1) == 255) {
			return true;
		}
		elseif ($this->_SWTReadInt ($file, $offset, 1) == 0) {
			return false;
		}
		else { // Standardwert, wenn nicht = 00 oder FF
			return false;
		}
	}
	
}
