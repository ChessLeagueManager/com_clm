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

class CLMModelSWTLigainfo extends JModel {

	function __construct () {
	
		parent::__construct ();
		
		// Konfigurationsparameter auslesen
		$config = &JComponentHelper::getParams( 'com_clm' );
		$rang	= $config->get('rangliste',0);
		$sl_mail= $config->get('sl_mail',0);
		
		// Listen aus der Datenbank auslesen
		$db_data = $this->dbQuery ();
		$db_sllist = $db_data['sllist'];
		$db_saisonlist = $db_data['saisonlist'];
		$db_glist = $db_data['glist'];
		
		// Daten an View weitergeben
		$this->setState ('rang', $rang);
		$this->setState ('sl_mail', $sl_mail);

		$this->setState ('saison_id', JRequest::getVar ('filter_saison', 0, 'default', 'int'));
				
		$this->setState ('db_sllist', $db_sllist);
		$this->setState ('db_saisonlist', $db_saisonlist);
		$this->setState	('db_glist', $db_glist);

	}
	
	function getDefault () {
	
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
		$default['params']					= '';
		//Mit Daten aus Datenbank überschreiben, falls ein Liga geupdated wird
		if(JRequest::getInt('update') == 1) {
			if ($id = JRequest::getInt('liga')) {
				$db		=& JFactory::getDBO ();
				$select_query = '  SELECT * FROM #__clm_liga '
								.' WHERE id = '.$id.'; ';
				$db->setQuery ($select_query);
				$ligaFromDatabase = $db->loadObject();
				//Standardwerte werden überschrieben
				$default['name'] 		= $ligaFromDatabase->name;
				$default['sid'] 		= $ligaFromDatabase->sid;
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
				$default['bem_int'] 	= $ligaFromDatabase->bem_int;
				$default['bemerkungen'] = $ligaFromDatabase->bemerkungen;
				$default['params'] 		= $ligaFromDatabase->params;			
			}
		}
		JRequest::setVar ('params', $default['params']);

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
		
		$mturnier = JRequest::getVar ('mturnier', '0', 'default', 'int');
		
		// Namen und Verzeichnis der SWT-Datei auslesen
		$filename = JRequest::getVar ('swt', '', 'default', 'string');
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'swt' . DIRECTORY_SEPARATOR;
		
		$swt = $path.$filename;
		
		// Allgemeine Turnierdaten
		//$swt_data['liga_name']				= $this->_SWTReadName ($swt, 245, 60);
		$swt_data['liga_name']				= utf8_encode($this->_SWTReadName ($swt, 245, 60));
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
		if ($sieg_bed == 1) { // Mindespunktzahl (mehr als die Haelfte der moeglichen Punkte) muss erreicht werden
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
			 2 ->  5	 Brettpunkte
			 3 ->  1	 Buchholz
			    > 11	 Buchholz (1 Streichergebnis)
			 4 ->  2	 Buchholz-Summe
			( 5 ->  3	 Sonneborn-Berger bis 2014 )
			 5 ->  23	 Sonneborn-Berger
			14 ->  4	 Anzahl der Siege
			15 -> 25     Direkter Vergleich
			16 ->  6	 Berliner Wertung
			18 ->  6	 Berliner Wertung (dir. Vergleich) */
		$clm_fein = array (0 => 0, 2 => 5, 3 => 1, 4 => 2, 5 => 23, 14 => 4, 15 => 25, 16 => 6, 18 => 6);
		
		$swt_data['tiebr1'] = $clm_fein[$man_zweit];
		$swt_data['tiebr2'] = $clm_fein[$man_dritt];
		$swt_data['tiebr3'] = 0;
		
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
		$db			=& JFactory::getDBO ();
		$user		=& JFactory::getUser ();
		$option 	= JRequest::getCmd( 'option' );
		$section 	= JRequest::getVar( 'section' );
		$mturnier 	= JRequest::getVar ('mturnier', '0', 'default', 'int');
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		
		// SL Liste
		//$tql =	' SELECT a.jid,a.name  FROM #__clm_user as a'
		//		.' LEFT JOIN #__clm_saison as s ON s.id = a.sid'
		//		.' WHERE (a.usertype = "sl" OR a.usertype = "dv" OR a.usertype = "dwz" OR a.usertype = "admin" )'
		//		.' AND a.published = 1 AND s.published = 1 AND s.archiv =0'

		//	;
		//$db->setQuery ($tql);
		if ($mturnier == 0) $clmAccess->accesspoint = 'BE_league_edit_result';  //nur wer wenigstens Ergebnisse im BE pflegen darf, kann SL sein
		else $clmAccess->accesspoint = 'BE_teamtournament_edit_result';  		//nur wer wenigstens Ergebnisse im BE pflegen darf, kann SL sein
		$clmAccess->accessvalue = '>0';  										//für alle ligen/mturniere oder nur ausgewählte
		if($clmAccess->userlist() === false) {
//			echo "<br>cl: "; var_dump($clmAccess->userlist()); die('clcl'); }
//		if (!$db->query()) {
			$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
			return JError::raiseWarning( 500, $db->getErrorMsg() );
		}
		
		$db_data['sllist'] = $clmAccess->userlist();
		//$db_data['sllist'] = $db->loadObjectList ();
		// Saisonliste
		$sql = 'SELECT id as sid, name FROM #__clm_saison WHERE archiv = 0';
		$db->setQuery ($sql);
		if (!$db->query()) {
			$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
			return JError::raiseWarning( 500, $db->getErrorMsg() );
		}
		$db_data['saisonlist'] = $db->loadObjectList ();

		// Ranglisten
		$query = ' SELECT id, Gruppe FROM #__clm_rangliste_name ';
		$db->setQuery($query);
		if (!$db->query()) {
			$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
			return JError::raiseWarning( 500, $db->getErrorMsg() );
		}
		$db_data['glist'] = $db->loadObjectList ();
		
		return $db_data;
		
	}
	
	function store () {
	
		// DB-Zugriff
		$db		=& JFactory::getDBO ();
		//$row	=& JTable::getInstance ('ligenSWT', 'TableCLM');
		
		/*
		// Daten aus dem Formular holen
		// allgemeine Ligadaten
		$name					= JRequest::getVar ('name');
		$sl						= JRequest::getVar ('sl');
		$sid					= JRequest::getVar ('sid');
		$rang					= JRequest::getVar ('rang');
		$anz_mannschaften		= JRequest::getVar ('teil');
		$anz_bretter			= JRequest::getVar ('stamm');
		$anz_ersatz				= JRequest::getVar ('ersatz');
		$anz_runden				= JRequest::getVar ('runden');
		$anz_durchgaenge		= JRequest::getVar ('durchgang');
		$runden_modus			= JRequest::getVar ('runden_modus');
		$heimrecht_vertauscht	= JRequest::getVar ('heim');
		
		// Wertung
		$siegpunkte				= JRequest::getVar ('sieg');
		$remispunkte			= JRequest::getVar ('remis');
		$verlustpunkte			= JRequest::getVar ('nieder');
		$antrittspunkte			= JRequest::getVar ('antritt');
		$man_siegpunkte			= JRequest::getVar ('man_sieg');
		$man_remispunkte		= JRequest::getVar ('man_remis');
		$man_verlustpunkte		= JRequest::getVar ('man_nieder');
		$man_antrittspunkte		= JRequest::getVar ('man_antritt');
		$sieg_bed				= JRequest::getVar ('sieg_bed');
		$b_wertung				= JRequest::getVar ('b_wertung');
		$anz_aufsteiger			= JRequest::getVar ('auf');
		$anz_moegl_aufsteiger	= JRequest::getVar ('auf_evtl');
		$anz_absteiger			= JRequest::getVar ('ab');
		$anz_moegl_absteiger	= JRequest::getVar ('ab_evtl');
		
		// Einstellungen
		$mail		= JRequest::getVar ('mail');
		$sl_mail	= JRequest::getVar ('sl_mail');
		$order		= JRequest::getVar ('order');
		$published	= JRequest::getVar ('published');
		
		// Bemerkungen
		$bemerkungen		= JRequest::getVar ('bemerkungen');
		$bemerkungen_int	= JRequest::getVar ('bem_int');
		*/
		
		//Liga-Parameter aufbereiten
		$default['params'] = JRequest::getVar ('params');
		$paramsStringArray = explode("\n", $default['params']);
		$default_params = array();
		foreach ($paramsStringArray as $value) {
			$ipos = strpos ($value, '=');
			if ($ipos !==false) {
				$key = substr($value,0,$ipos);
				if (substr($key,0,2) == "\'") $key = substr($key,2,strlen($key)-4);
				if (substr($key,0,1) == "'") $key = substr($key,1,strlen($key)-2);
				$default_params[$key] = substr($value,$ipos+1);
			}
		}	
		//Liga-Parameter aktualisieren
		$default_params['anz_sgp'] = JRequest::getVar('anz_sgp');
		$default_params['color_order'] = JRequest::getVar('color_order');
		//Liga-Parameter zusammenfassen
		$paramsStringArray = array();
		foreach ($default_params as $key => $value) {
			if (substr($key,0,2) == "\'") $key = substr($key,2,strlen($key)-4);
			if (substr($key,0,1) == "'") $key = substr($key,1,strlen($key)-2);
			$paramsStringArray[] = $key.'='.$value;
		}
		$default['params'] = implode("\n", $paramsStringArray);
		JRequest::setVar ('params', $default['params']);
		$mturnier = JRequest::getVar ('mturnier', 0, 'default', 'int');
		JRequest::setVar ('liga_mt', $mturnier);
		
		$spalten = array ( 'lid', 'name', 'sl', 'sid', 'rang', 'teil', 'stamm', 'ersatz', 'runden', 'durchgang', 'runden_modus', 'heim',
		                   'sieg', 'remis', 'nieder', 'antritt', 'man_sieg', 'man_remis', 'man_nieder', 'man_antritt', 'sieg_bed',
						   'b_wertung', 'auf', 'auf_evtl', 'ab', 'ab_evtl', 'mail', 'sl_mail', 'order', 
						   'published', 'ordering', 'bem_int', 'liga_mt', 'tiebr1', 'tiebr2', 'tiebr3', 'params' );
		
		$fields = '';
		$values = '';
		foreach ($spalten as $spalte) {
			$fields .= '`'.$spalte.'`,';
			$values .= ' "'.clm_escape(JRequest::getVar ($spalte)).'",';
		}
		$fields = substr ($fields, 0, -1);
		$values = substr ($values, 0, -1);
		$insert_query = ' INSERT INTO #__clm_swt_liga'
		              . ' ( ' . $fields . ' ) '
		              . ' VALUES ( ' . $values . ' ); ';
		
		$db->setQuery ($insert_query);
		
		if ($db->query ()) {
			JRequest::setVar ('swt_id', $db->insertid() );
			return true;
		}
		else {
			print $db->getErrorMsg ();
			return false;
		}
		
	}
		
/*		/// aus dem Ligen-Controller

		$option		= JRequest::getCmd('option');
		$section	= JRequest::getVar('section');
		$db 		= & JFactory::getDBO();
		$task 		= JRequest::getVar( 'task');
		$row 		= & JTable::getInstance( 'ligen', 'TableCLM' );
		$msg		= JRequest::getVar( 'id');
		$sid		= JRequest::getVar( 'sid');

	if (!$row->bind(JRequest::get('post'))) {
		JError::raiseError(500, $row->getError() );
	}
	// pre-save checks
	if (!$row->check()) { JError::raiseError(500, $row->getError() ); }

	$teil	= $row->teil;

	// if new item, order last in appropriate group
	$aktion = JText::_( 'LIGEN_AKTION_LEAGUE_EDIT' );
	if (!$row->id) {
	$neu_id = 1;
	$aktion = JText::_( 'LIGEN_AKTION_NEW_LEAGUE' );
		$where = 'sid = ' . (int) $row->sid;
		$row->ordering = $row->getNextOrder( $where );

	// Bei ungerader Anzahl Mannschaften Teilnehmerzahl um 1 erhöhen
	if (($row->teil)%2 != 0) {
		$ungerade_id	= 1;
		$row->teil	= $row->teil+1;
		$tln		= $row->teil;
	JError::raiseWarning(500, JText::_( 'LIGEN_MANNSCH', true ) );
				}
			}
	// save the changes
	if (!$row->store()) {
		JError::raiseError(500, $row->getError() );
		}
	$liga_man	= $row->id;
	$liga_rnd	= $row->runden;
	$liga_dg	= $row->durchgang;
	$publish	= $row->published;

	// Wenn sid gewechselt wurde, alle Daten in neue Saison verschieben
	if ($sid_alt != $sid AND $sid_alt != '') {
	JError::raiseNotice( 6000,  JText::_( 'LIGEN_SAISON_AEND' ));
	$query = ' UPDATE #__clm_mannschaften '
		.' SET sid = '.$sid
		.' WHERE liga = '.$liga_man
		.' AND sid = '.$sid_alt
		;
	$db->setQuery($query);
	$db->query();

	$query = ' UPDATE #__clm_meldeliste_spieler '
		.' SET sid = '.$sid
		.' WHERE lid = '.$liga_man
		.' AND sid = '.$sid_alt
		;
	$db->setQuery($query);
	$db->query();

	$query = ' UPDATE #__clm_rnd_man '
		.' SET sid = '.$sid
		.' WHERE lid = '.$liga_man
		.' AND sid = '.$sid_alt
		;
	$db->setQuery($query);
	$db->query();

	$query = ' UPDATE #__clm_rnd_spl '
		.' SET sid = '.$sid
		.' WHERE lid = '.$liga_man
		.' AND sid = '.$sid_alt
		;
	$db->setQuery($query);
	$db->query();

	$query = ' UPDATE #__clm_runden_termine '
		.' SET sid = '.$sid
		.' WHERE liga = '.$liga_man
		.' AND sid = '.$sid_alt
		;
	$db->setQuery($query);
	$db->query();
	}

	// Bei ungerader Anzahl Mannschaften 'spielfrei' hinzufügen
	if ($ungerade_id == '1') {

	$query = ' INSERT INTO #__clm_mannschaften '
		.' ( `sid`,`name`,`liga`,`zps`,`liste`,`edit_liste`,`man_nr`,`tln_nr`,`mf`) '
		.' VALUES ('$sid','spielfrei','$liga_man','0','0','62','0','$tln','0') '
		;
	$db->setQuery($query);
	$db->query();

	JError::raiseNotice( 6000,  JText::_( 'LIGEN_MANNSCH_1' ));
		}
	// Mannschaftsrunden anlegen
	if ($neu_id == '1') {
		CLMControllerLigen::runden($liga_man);

	// Mannschaften anlegen
	for($x=1; $x< 1+$teil; $x++) {
	$man_name = 'Mannschaft '.$x;
	$man_nr = $liga_man.$x;
	$query = ' INSERT INTO #__clm_mannschaften '
		.' (`sid`,`name`,`liga`,`zps`,`liste`,`edit_liste`,`man_nr`,`tln_nr`,`mf`,`published`) '
		.' VALUES ('$sid','$man_name','$liga_man','1','0','0','$man_nr','$x','0','$publish') '
		;
	$db->setQuery($query);
	$db->query();
				}

	// Runden (Termine) anlegen
	for($y=1; $y< 1+$liga_dg; $y++) {
	for($x=1; $x< 1+$liga_rnd; $x++) {

	$nr	= $x + ($y-1)*$liga_rnd;
	$name	= 'Runde '.$nr;

	$query = ' INSERT INTO #__clm_runden_termine '
		.' (`sid`,`name`,`liga`,`nr`,`meldung`,`sl_ok`,`published` ) '
		.' VALUES ("'.$sid.'","'.$name.'","'.$liga_man.'","'.$nr.'","0","0","'.$publish.'") '
		;
	$db->setQuery($query);
	$db->query();
				}}}

	$row->checkin();

	switch ($task)
	{
		case 'apply':
			$msg = JText::_( 'LIGEN_AENDERN' );
			$link = 'index.php?option='.$option.'&section='.$section.'&task=edit&cid[]='. $row->id ;
			break;
		case 'save':
		default:
			$msg = JText::_( 'LIGEN_LIGA' );
			$link = 'index.php?option='.$option.'&section='.$section;
			break;
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = $aktion;
	$clmLog->params = array('sid' => $row->sid, 'lid' => $row->id);
	$clmLog->write();
	

	$mainframe->redirect( $link, $msg );
	}


// ENDE Ligen-Controller
	
*/

/*	function _getAktuelleSaison() {
		if (empty( $this->_aktuelleSaison )) { 
		
			$query =  ' SELECT 
							id,
							name,
							published
						FROM 
							#__clm_liga 
						WHERE
							published = 1';
			$var = $this->_getList( $query );
		} 
		return $var[0]->id;
	}*/
	
	
	function _SWTReadName($file, $offset, $length){
		$i = 0;
		$name = '';
		while(ord ($chr = JFile::read($file, false, 1, 8192, $offset+$i)) != 0 && $i < $length){
			$name .= $chr;
			$i++;
		}
		return $name;
	}
	
	function _SWTReadInt ($file, $offset, $length = 1) {
		$value = 0;
		for ($i = 0; $i < $length; $i++) {
			$cur = ord (JFile::read ($file, false, 1, 8192, $offset+$i));
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
	
/*
/// aus dem swtliga-model:
	function __construct(){
		parent::__construct();
		$filter_saison	= JRequest::getVar( 'filter_saison' , $this->_getAktuelleSaison() , 'default' , 'int' );

		$this->setState( 'filter_saison' , $filter_saison );
	}
	
	function getSaisons() {
		if (empty( $this->_saisons )) { 
			$query =  ' SELECT 
							id,
							name
						FROM 
							#__clm_saison';
			$this->_saisons = $this->_getList( $query );
		} 
		return $this->_saisons;
	}
	
/// aus dem ligen-controller:

	$db 		=& JFactory::getDBO();
	$user 		=& JFactory::getUser();
	$task 		= JRequest::getVar( 'task');
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );
	$row 		=& JTable::getInstance( 'ligen', 'TableCLM' );
	JArrayHelper::toInteger($cid, array(0));
	
	// load the row from the db table
	$row->load( $cid[0] );


	require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'ligen.php');
	CLMViewLigen::liga( $row, $lists, $option );
	}
*/
