<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

//require JPATH_COMPONENT . DS . 'classes' . DS . 'output.class.php';

class SimpleClmUninstaller {
	
	
	function _getDB() {
		$database = JFactory::getDBO();
		return $database;
	}

	/**
	 

	 */
	function dbuninstall() {
		
		$tables = array(
					'`dwz_spieler`',
					'`dwz_verbaende`', 
					'`dwz_vereine`', 
					'`#__clm_access_points`',
					'`#__clm_categories`',
					'`#__clm_dwz_spieler`',
					'`#__clm_dwz_vereine`',
					'`#__clm_ergebnis`',
					'`#__clm_liga`',
					'`#__clm_log`',
					'`#__clm_mannschaften`',
					'`#__clm_meldeliste_spieler`',
					'`#__clm_params`',
					'`#__clm_rangliste_id`',
					'`#__clm_rangliste_name`',
					'`#__clm_rangliste_spieler`',
					'`#__clm_rnd_man`',
					'`#__clm_rnd_spl`',
					'`#__clm_runden_termine`',
					'`#__clm_saison`',
					'`#__clm_swt_liga`',
					'`#__clm_swt_mannschaften`',
					'`#__clm_swt_meldeliste_spieler`',
					'`#__clm_swt_rnd_man`',
					'`#__clm_swt_rnd_spl`',
					'`#__clm_swt_turniere`',
					'`#__clm_swt_turniere_rnd_spl`',
					'`#__clm_swt_turniere_rnd_termine`',
					'`#__clm_swt_turniere_tlnr`',
					'`#__clm_swt_spl`',
					'`#__clm_swt_man`',
					'`#__clm_swt_spl_nach`',
					'`#__clm_swt_spl_tmp`',
					'`#__clm_termine`',
					'`#__clm_turniere`',
					'`#__clm_turniere_rnd_spl`',
					'`#__clm_turniere_rnd_termine`',
					'`#__clm_turniere_sonderranglisten`',
					'`#__clm_turniere_tlnr`',
					'`#__clm_user`',
					'`#__clm_usertype`',
					'`#__clm_vereine`')
					;
		
		$database = SimpleClmUninstaller::_getDB();
		foreach ($tables as $value) {
			$sql = "DROP TABLE IF EXISTS ".$value.";";
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Löschen der Tabelle ".$value;
				return false;
			} else {
				echo "<br>Erfolgreich gelöscht: Tabelle ".$value;
			}
		}

		return "* Alle DB-Tabellen erfolgreich gelöscht!<br />";
	}	
}


function com_uninstall() {

    $uninstaller = new SimpleClmUninstaller();
	//$config =  SCOutput::config();
	
	// Konfigurationsparameter auslesen
	$config	= &JComponentHelper::getParams( 'com_clm' );
	$dbuninstall		= $config->get('dbuninstall',0);
	$uninstall_fe_language	= $config->get('uninstall_fe_language',0);
	$uninstall_fe_pdf	= $config->get('uninstall_fe_pdf',0);
	$uninstall_fe_css	= $config->get('uninstall_fe_css',0);
	$uninstall_fe_copyright	= $config->get('uninstall_fe_copyright',0);
	$uninstall_be_language	= $config->get('uninstall_be_language',0);

    if ( $dbuninstall=="1" ) {
	//$dbuninstall = $uninstaller->dbuninstall($collation);
	$dbuninstall = $uninstaller->dbuninstall();
			if ( $dbuninstall ) {
			echo "<h3>Dropping tables:</h3><br />";	
			echo $dbuninstall;
			echo "<font color='green'>---> OK!</font><br />";
		} else {
			echo "<h3 style=\"color: red;\">Fehler während Löschen DB<br/></h3>";
			return false;
		}
	}

	$path	= JPATH_ROOT.DS.'administrator'.DS.'components'.DS;
	$backup	= $path.'__backup_clm';

	jimport('joomla.filesystem.file');

	// alte Backups löschen
	//$dateien = JFolder::files($backup);
	//JFile::delete($dateien);
	if (JFolder::exists($backup)){ JFolder::delete($backup); }

	if ( $dbuninstall=="0" ) {

		$db	= & JFactory::getDBO();
		
		// Joomla-Version ermitteln
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if (substr_count($joomlaVersion, '1.5')) {
			$sql = " REPLACE INTO #__clm_params (SELECT params,id FROM #__components "
				." WHERE `option` = 'com_clm')"
				;
		} else {
			$sql = " REPLACE INTO #__clm_params (SELECT params, extension_id FROM #__extensions "
				." WHERE `element` = 'com_clm')"
				;
		}
			
		$db->setQuery( $sql);
		$db->query();
	    
	// Backup Ordner erstellen falls nicht vorhanden
	if (!JFolder::exists($backup)){
		JFolder::create($backup);
		}

	if ( $uninstall_fe_language=="0" ) {
	// Sprachdatei Frontend kopieren
	$path_fe	= JPATH_ROOT.DS.'language'.DS;
	$src_fe		= $path_fe.'de-DE'.DS.'de-DE.com_clm.ini';
	$dest_fe	= $backup.DS.'de-DE__fe__com_clm.ini';
	
	if(JFile::copy($src_fe, $dest_fe)) {
	echo "<br><font color='green'>Backup der Frontend Sprachdatei (de-DE) erstellt !</font>";
	} else {
	echo "<br><font color='red'>Erstellen des Backup der Frontend Sprachdatei (de-DE) fehlgeschlagen !</font>";
	}

	$src_fe		= $path_fe.'en-GB'.DS.'en-GB.com_clm.ini';
	$dest_fe	= $backup.DS.'en-GB__fe__com_clm.ini';
	
	if(JFile::copy($src_fe, $dest_fe)) {
	echo "<br><font color='green'>Backup der Frontend Sprachdatei (en-GB) erstellt !</font>";
	} else {
	echo "<br><font color='red'>Erstellen des Backup der Frontend Sprachdatei (en-GB) fehlgeschlagen !</font>";
	}
	}

	if ( $uninstall_fe_css=="0" ) {
	// CSS Dateien Frontend kopieren
	$path_fe	= JPATH_ROOT.DS.'components'.DS.'com_clm'.DS.'includes'.DS;
	
	JFile::copy($path_fe.'style.css', $backup.DS.'style.css');
	JFile::copy($path_fe.'clm_content.css', $backup.DS.'clm_content.css');

	echo "<br><font color='green'>Backup der Frontend Stylesheets erstellt !</font>";
	}

	if ( $uninstall_fe_pdf=="0" ) {
	// PDF Header und Footer Dateien kopieren
	$path_fe	= JPATH_ROOT.DS.'components'.DS.'com_clm'.DS.'includes'.DS;

	JFile::copy($path_fe.'pdf_header.php', $backup.DS.'pdf_header.php');
	JFile::copy($path_fe.'pdf_footer.php', $backup.DS.'pdf_footer.php');

	echo "<br><font color='green'>Backup der Frontend PDF Styles erstellt !</font>";
	}

	if ( $uninstall_fe_copyright=="0" ) {
	// Copyright Hinweis kopieren
	$path_fe	= JPATH_ROOT.DS.'components'.DS.'com_clm'.DS.'includes'.DS;

	JFile::copy($path_fe.'copy.php', $backup.DS.'copy.php');

	echo "<br><font color='green'>Backup des Frontend Copyright Hinweises erstellt !</font>";
	}

	if ( $uninstall_be_language=="0" ) {
	// Dateien Backend kopieren
	$path_be	= JPATH_ROOT.DS.'administrator'.DS.'language'.DS;
	$src_be		= $path_be.'de-DE'.DS.'de-DE.com_clm.ini';
	$dest_be	= $backup.DS.'de-DE__be__com_clm.ini';

	if(JFile::copy($src_be, $dest_be)){
	echo "<br><font color='green'>Backup der Backend Sprachdatei (de-DE) erstellt !</font>";
	} else {
	echo "<br><font color='red'>Erstellen des Backup der Frontend Sprachdatei (de-DE) fehlgeschlagen !</font>";
	}

	$src_be		= $path_be.'en-GB'.DS.'en-GB.com_clm.ini';
	$dest_be	= $backup.DS.'en-GB__be__com_clm.ini';

	if(JFile::copy($src_be, $dest_be)){
	echo "<br><font color='green'>Backup der Backend Sprachdatei (en-GB) erstellt !</font>";
	} else {
	echo "<br><font color='red'>Erstellen des Backup der Frontend Sprachdatei (en-GB) fehlgeschlagen !</font>";
	}


	}
	} else {
	// Backup Ordner löschen
	if (JFolder::exists($backup)){
		JFolder::delete($backup);
		}}
		
		
	echo "<h3>CLM erfolgreich entfernt!</h3><br />";
	return true;
}

?>