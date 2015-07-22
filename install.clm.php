<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

class SimpleClmInstaller {

	/**
	 * gibt die Datenbank zurück
	*/
	function _getDB() {
		$database = JFactory::getDBO();
		return $database;
	
	}	
	/**
	 * gibt den Inhalt einer Tabelle zum Debuggen zurück
	*/
	function _debugDB($table) {
		echo '<br/>';
		foreach ($table as $key => $value) {
			echo $key.'-';
		}
		echo '<br/>';
	}	
	
	/**
	 * Ermittelt die Collation der DB (UTF8 oder nicht)
	 *
	 */
	function _isUtf8($collation) {
		$utf8 = '';
		if ( substr($collation, 0, 4) == 'utf8' ) {
			$utf8 = " CHARACTER SET `utf8` COLLATE `" . $collation . "`";
		}
		return $utf8;
	}
	
	/**
	 * Installation der Datenbank
	 * Nur erforderlich bei der ersten Installation
	 * Die Erstinstallation erfolgt auf den jeweils aktuellen Stand
	 * 
	 */
	function dbinstall($collation) {

		//echo "<br>dbinstall01"; die('  inst01');
		include './components/com_clm/includes/sql_dbinstall.php';			
		//echo "<br>dbinstall02"; die('  inst02');
		
		return "* Tabellen erfolgreich angelegt!<br />";
	}
		
	/**
	 * Aktualiserung der Datenbank auf die neueste Version
	 * Dadurch wird die DB konsistent gehalten
	 *
	 * Alle Änderungen ab 1.2.0
	 * @return gibt Fehler
	 *
	 */
	function dbupgrade($collation) {

	//	echo "<br>dbupdate11"; die('  updt11');
		include './components/com_clm/includes/sql_dbupdate.php';			
	//	echo "<br>dbupdate12"; die('  uptt12');

		

		return $string;
	}
}	
	
	function com_install() {
	
	
	$installer = new SimpleClmInstaller();
	$database = $installer->_getDB();
	$collation = $database->getCollation();
	// Prüfung ob Neuinstallation oder Upgrade anhand des Vorhandensein von Tabelle clm_user
	$database->setQuery ("SHOW COLUMNS FROM #__clm_user");
	$fields = $database->loadObjectList();
	//echo "<br>fields: "; var_dump($fields); die('  com_install');
	if ( !count($fields) ) {
		// Neuinstallation laufen lassen
		$dbinstall = $installer->dbinstall($collation);
		if ( $dbinstall ) {
			echo "<h3>Anlegen Tabellen:</h3><br />";
			echo $dbinstall;
			echo "<font color='green'>---> OK!</font><br />";
		} else {
			echo "<h3 style=\"color: red;\">Fehler während Anlegen Datenbank..<br/></h3>";
			return false;
		}
	}
	// Die Upgrade-Funktion läuft immer auch wenn die DB gerade angelegt wurde
	// Hiermit werden die DB-Tabellen auf die letzte Version gebracht
	$dbupgrade = $installer->dbupgrade($collation);
	if ( $dbupgrade ) {
		echo "<h3>Update DB-Tabellen:</h3><br />";
		echo $dbupgrade;
		echo "<font color='green'>----> OK!</font><br />";
	} else {
		echo "<h3 style=\"color: red;\">Fehler während Update Datenbank..<br/></h3>";
		return false;
	}

	jimport('joomla.filesystem.file');

	$path	= JPATH_ROOT.DS.'administrator'.DS.'components'.DS;
	$backup	= $path.'__backup_clm';

	// Backup Ordner suchen und ggf. Backup Dateien einspielen
	if (JFolder::exists($backup)){
	echo "<h3>Backup der userspezifischen Dateien einspielen :</h3>";

	////////////////////////////
	// Parameter zurückschreiben 
	// - funktioniert nicht im J!2.5, da Komponente zu diesem Zeitpunkt noch nicht in Tabelle extension angelegt ist 
	/* - verlagert in scriptfile
	$db	=& JFactory::getDBO();
	// Backup Paramter holen
	$sql = " SELECT params FROM #__clm_params ";
	$db->setQuery( $sql);
	$param_clm = $db->loadObjectList();

	// Joomla-Version ermitteln
	$version = new JVersion();
	$joomlaVersion = $version->getShortVersion();
	if (substr_count($joomlaVersion, '1.5')) {
		// Parameter schreiben
		$sql = " UPDATE #__components SET `params` = '".$param_clm[0]->params."'"
			." WHERE `option` = 'com_clm'"
			;
	} else {
		// Parameter schreiben
		$sql = " UPDATE #__extensions SET `params` = '".$param_clm[0]->params."'"
			." WHERE `element` = 'com_clm'"
			;
	}
	$db->setQuery( $sql);
	$db->query();

	// Parameter löschen
	$sql = " TRUNCATE TABLE #__clm_params ";
	$db->setQuery( $sql);
	$db->query();
	*/
	// Ende Parameter
	/////////////////
 
	// Sprachdatei Frontend kopieren
	$path_fe	= JPATH_ROOT.DS.'language'.DS;
	$src_fe		= $path_fe.'de-DE'.DS.'.com_clm.ini';
	$dest_fe	= $backup.DS.'de-DE__fe__com_clm.ini';

	if(JFile::exists($dest_fe)){
	JFile::copy($dest_fe, $src_fe);
	echo "<br><font color='green'>Backup der Frontend Sprachdatei (de-DE) erfolgreich kopiert !</font>";
	} else {
	echo "<br><font color='red'>Backup der Frontend Sprachdatei (de-DE) existiert nicht !</font>";
	}

	$src_fe		= $path_fe.'en-GB'.DS.'.com_clm.ini';
	$dest_fe	= $backup.DS.'en-GB__fe__com_clm.ini';

	if(JFile::exists($dest_fe)){
	JFile::copy($dest_fe, $src_fe);
	echo "<br><font color='green'>Backup der Frontend Sprachdatei (en-GB) erfolgreich kopiert !</font>";
	} else {
	echo "<br><font color='red'>Backup der Frontend Sprachdatei (en-GB) existiert nicht !</font>";
	}

	// CSS Dateien Frontend kopieren
	$path_fe	= JPATH_ROOT.DS.'components'.DS.'com_clm'.DS.'includes'.DS;
	if(JFile::exists($backup.DS.'style.css')){
	JFile::copy($backup.DS.'style.css', $path_fe.'style.css');
	JFile::copy($backup.DS.'clm_content.css', $path_fe.'clm_content.css');
	echo "<br><font color='green'>Backup der Frontend Stylesheetdateien erfolgreich kopiert !</font>";
	} else {
	echo "<br><font color='red'>Backup der Frontend Stylesheetdateien existiert nicht !</font>";
	}

	// PDF Header und Footer Dateien kopieren
	if(JFile::exists($backup.DS.'pdf_header.php')){
	JFile::copy($backup.DS.'pdf_header.php', $path_fe.'pdf_header.php');
	JFile::copy($backup.DS.'pdf_footer.php', $path_fe.'pdf_footer.php');
	echo "<br><font color='green'>Backup der Frontend PDF Styles erfolgreich kopiert !</font>";
	} else {
	echo "<br><font color='red'>Backup der Frontend PDF Styles existiert nicht !</font>";
	}

	// Copyright Hinweis kopieren
	if(JFile::exists($backup.DS.'copy.php')){
	JFile::copy($backup.DS.'copy.php', $path_fe.'copy.php');
	echo "<br><font color='green'>Backup des Frontend Copyright Hinweises erfolgreich kopiert !</font>";
	} else {
	echo "<br><font color='red'>Backup des Frontend Copyright Hinweises existiert nicht !</font>";
	}

	// Dateien Backend kopieren
	$path_be	= JPATH_ROOT.DS.'administrator'.DS.'language'.DS;
	$src_be		= $path_be.'de-DE'.DS.'com_clm.ini';
	$dest_be	= $backup.DS.'de-DE__be__com_clm.ini';

	if(JFile::exists($dest_be)){
	JFile::copy($dest_be, $src_be);
	echo "<br><font color='green'>Backup der Backend Sprachdatei (de-DE) erfolgreich kopiert !</font>";
	} else {
	echo "<br><font color='red'>Backup der Backend Sprachdatei (de-DE) existiert nicht !</font>";
	}

	$src_be		= $path_be.'en-GB'.DS.'com_clm.ini';
	$dest_be	= $backup.DS.'en-GB__be__com_clm.ini';

	if(JFile::exists($dest_be)){
	JFile::copy($dest_be, $src_be);
	echo "<br><font color='green'>Backup der Backend Sprachdatei (en-GB) erfolgreich kopiert !</font>";
	} else {
	echo "<br><font color='red'>Backup der Backend Sprachdatei (en-GB) existiert nicht !</font>";
	}
		} else { echo "<br><font color='red'>Es existiert kein Backup Ordner, daher wurden keine Backups installiert !</font>"; }
	/*
	echo "<h3>Installation erfolgreich beendet!</h3><br />";
	
	echo "<br /><br />
			<b><font color='red'>Achtung</font></b>: Es wurde die Hauptkomponente des ChessLeagueManagers installiert. <br>
			Auf unserer Projekt-Seite www.chessleaguemanager.de unter Schnellstart finden Sie erste Hinweise zum Setup. <br/><br>
			Auch möchten wir auf die nötigen Module zur Darstellung im Frontend aufmerksam machen: <br/>
			- Darstellungsmodul mod_clm zur Darstellung von Ligen und/oder Mannschaftsturniere <br/>
			  (falls beides: den Eintrag in der Modultabelle kopieren) <br/>
			- Login-Modul mod_clm_log, wenn die Ergebnisse durch die Mannschaftsleiter über das Frontend eingegeben werden. <br/>
			  (ein sehr häufiger Ansatz) <br/>
			- Einzelturnier-Modul mod_clm_turmultiple zur Darstellung von Einzelturnieren <br/>
			- Termin-Modul mod_clm_termine zur Darstellung der Spiel- und Veranstaltungstermine im Kalender <br/>
			- Archiv-Modul mod_clm_archiv zur Darstellung der Ligen und Mannschaftsturniere der Vorjahre <br/>
			  (also erst ab zweiter Saison sinnvoll) <br/>
			";
	*/		
	return true;
}
?>