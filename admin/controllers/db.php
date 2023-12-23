<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
class CLMControllerDB extends JControllerLegacy {
	/**
	 * Constructor
	 */
	function __construct($config = array()) {
		parent::__construct($config);
		// Register Extra tasks
		$this->registerTask('upload', 'upload');
		$this->registerTask('apply', 'save');
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('update_clm', 'update_clm');
	}
	function display($cachable = false, $urlparams = array()) {
		parent::display();
	}
	public static function endsWith($haystack, $needle) {
		return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
	}
	public static function saison() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
		$db = JFactory::getDBO();
		$option = clm_core::$load->request_string('option');
		$section = clm_core::$load->request_string('section');
		$sql = " SELECT id,name FROM #__clm_saison " . " WHERE archiv = 0 ORDER BY id ASC ";
		$db->setQuery($sql);
		$sid = $db->loadObjectList();
		return $sid;
	}
	public static function delete() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		defined('_JEXEC') or die('Invalid Token');
		$option = clm_core::$load->request_string('option');
		$section = clm_core::$load->request_string('section');
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$datei = clm_core::$load->request_string('sql_datei');
		$export = clm_core::$load->request_string('delete_export');
		$filesDir = 'components' . DS . $option . DS . 'upload';
		jimport('joomla.filesystem.file');
		if (!$datei AND !$export) {
			$mainframe->enqueueMessage(JText::_('DB_K_DATEI'),'warning');
			$mainframe->redirect('index.php?option=com_clm&view=db');
		}
		if ($datei AND $datei != "all") {
			JFile::delete($filesDir . DS . $datei);
		}
		if ($datei == "all") {
			$datei_del = CLMControllerDB::files();
			for ($x = 0;$x < count($datei_del);$x++) {
				JFile::delete($filesDir . DS . $datei_del[$x]);
			}
		}
		if ($export AND $export != "all") {
			JFile::delete($filesDir . DS . $export);
		}
		if ($export == "all") {
			$export_files = CLMControllerDB::export_files();
			for ($x = 0;$x < count($export_files);$x++) {
				JFile::delete($filesDir . DS . $export_files[$x]);
			}
		}
		$msg = JText::_('DB_DEL_SUCCESS');
		$mainframe->enqueueMessage($msg,'message');
		$mainframe->redirect('index.php?option=com_clm&view=db');
	}
	public static function upload() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		defined('_JEXEC') or die('Invalid Token');
		$option = clm_core::$load->request_string('option');
		$section = clm_core::$load->request_string('section');
		$task = clm_core::$load->request_string('task');
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$file = clm_core::$load->request_file('datei'); //JRequest::getVar('datei', '', 'files', 'array');
		// erlaubte Dateitypen
		$allowed = array('text/plain', 'application/octet-stream');
		if (!in_array($file['type'], $allowed) || !CLMControllerDB::endsWith($file['name'], ".clm")) {
			$mainframe->enqueueMessage(JText::_('DB_F_DATEITYP'),'warning');
			$mainframe->enqueueMessage(JText::_('DB_K_DATEITYP'),'notice');
			$mainframe->redirect('index.php?option=com_clm&view=db');
		}
		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$destDir = JPath::clean(JPATH_ADMINISTRATOR . DS . 'components' . DS . $option . DS . 'upload');
		$dest = JPath::clean($destDir . DS . $file['name']);
		// ggf. Verzeichnis erstellen
		if (!file_exists($destDir)) {
			jimport('joomla.filesystem.folder');
			JFolder::create($destDir);
		}
		// Dateien hochladen
		if (!JFile::upload($file['tmp_name'], $dest)) {
			$msg = JText::_('DB_NO_UPLOAD');
		} else {
			$msg = JText::_('DB_UPLOAD') . ' ' . $file['size'] . ' Byte ' . $file['type'];
		}
		// Dateirecht 644 setzen !
		if (!chmod($dest, 0644)) {
			$mainframe->enqueueMessage(JText::_('DB_DATEIRECHTE'),'warning');
			$mainframe->enqueueMessage(JText::_('DB_FTP'),'notice');
		}
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = "SQL upload";
		$clmLog->params = array('cids' => $file['size']);
		$clmLog->write();
		$mainframe->enqueueMessage($msg,'message');
		$mainframe->redirect('index.php?option=com_clm&view=db');
	}
	public static function files() {
		$mainframe = JFactory::getApplication();
		$option = clm_core::$load->request_string('option');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$filesDir = 'components' . DS . $option . DS . 'upload';
		$files = JFolder::files($filesDir, '.', true, true);
		$count = count($files);
		$sql = array();
		for ($x = 0;$x < $count;$x++) {
			if (basename($files[$x]) != "index.html") {
				$sql[] = basename($files[$x]);
			}
		}
		return $sql;
	}
	function export() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		defined('_JEXEC') or die('Invalid Token');
		$db = JFactory::getDBO();
		$option = clm_core::$load->request_string('option');
		$section = clm_core::$load->request_string('section');
		$clm_cuser = clm_core::$load->request_string('clm_user_exp');
		$clm_juser = clm_core::$load->request_string('clm_joomla_exp');
		$clm_sql = clm_core::$load->request_string('clm_sql');
		$liga = clm_core::$load->request_string('liga_export');
		$info = clm_core::$load->request_string('bem');
		if ($liga == "all") {
			$mainframe->enqueueMessage(JText::_('DB_LOESCH'),'warning');
			$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
			//$this->setRedirect('index.php?option=com_clm&view=db');
		}
		if ($liga == "0") {
			$mainframe->enqueueMessage(JText::_('DB_LIGA_EXPORT'),'warning');
			$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
		}
		// Konstante definieren für User-jid bei SQL Export
		// Kontrolle von vorhandenen Usern ist bei SQL Import nicht möglich, daher diese Maßnahme !!
		DEFINE('JID', '1');
		$sql = " SELECT a.* FROM #__clm_liga as a" . " LEFT JOIN #__clm_saison as s ON s.id = a.sid" . " WHERE s.archiv = 0 AND a.id = " . $liga;
		$db->setQuery($sql);
		$liga_name = $db->loadObjectList();
		// Dateinamen zusammensetzen
		$date = JFactory::getDate();
		$now = $date->toSQL();
		$datum = JHTML::_('date', $now, JText::_('d_m_Y'));
		// Dateivariante
		if ($clm_cuser == "1" AND $clm_juser == "1") {
			$var = "I";
			$variante = "Importvariante mit CLM Verwaltern und Joomla Usern";
		}
		if ($clm_cuser == "1" AND $clm_juser == "") {
			$var = "V";
			$variante = "Importvariante mit CLM Verwaltern";
		}
		if (($clm_cuser == "" AND $clm_juser == "") OR $clm_sql == "1") {
			$var = "R";
			$variante = "Read-Only Variante ohne Verwalter";
		}
		if ($clm_sql == "1") {
			$endung = 'sql';
		} else {
			$endung = 'clm';
		}
		// Slashes aus Namen filtern
		$datei_name = preg_replace("[/]", "_", $liga_name[0]->name);
		$file = $datei_name . '__' . $var . '__S' . $liga_name[0]->sid . '__L' . $liga . '__' . $datum . '.' . $endung;
		$file 	= clm_core::$load->file_name($file);
		$path = JPath::clean(JPATH_ADMINISTRATOR . DS . 'components' . DS . $option . DS . 'upload');
		$write = $path . DS . $file;
		$_surch = array("\r\n", "\r", "\n");
		$replace = chr(127);
		// Inhalt der Datei
		// Kommentar
		// Ersteller holen
		$user = JFactory::getUser();
		$jid = $user->get('id');
		$sql = " SELECT name FROM #__clm_user " . " WHERE sid = " . $liga_name[0]->sid . " AND jid =" . $jid;
		$db->setQuery($sql);
		$ersteller = $db->loadResult();
		// Versionsdaten aus XML holen
		//$Dir = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_clm';
		//$data = JApplicationHelper::parseXMLInstallFile($Dir . DS . 'clm.xml');
		$config = clm_core::$db->config();
		$data['version'] = clm_core::$load->version();
		$data['version'] = $data['version'][0];
		$comment = clm_core::$load->utf8decode("-- Erstellt mit dem ChessLeagueManager Version " . $data['version']
			. " \n-- Der CLM ist freie, kostenlose Software veröffentlicht unter der GNU/GPL Lizenz !" . " \n-- Projektseite : https://chessleaguemanager.de" 
			. " \n-- Autoren      : Andreas Dorn (webmaster@sbbl.org) und Thomas Schwietert (fishpoke@fishpoke.de)" . " \n\n-- *Datum     * " . $datum 
			. " \n-- *Ersteller * " . $ersteller . " \n-- *Herkunft  * " . JURI::root() . " \n-- *Datei     * " . $variante . " \n-- *Info      * " . $info . " \n\n");
		// Ligadaten
		if ($clm_cuser != "1") {
			$liga_name[0]->sl = JID;
		}
		if ($clm_sql == "1") {
			$ligadaten = "INSERT INTO `#__clm_liga` ( `id`, `name`, `sid`, `teil`, `stamm`, `ersatz`,`rang`, `sl`, `runden`, `durchgang`, `heim`, `mail`, `sl_mail`";
			$ligadaten .= ", `sieg_bed`, `runden_modus`, `man_sieg`, `man_remis`, `man_nieder`, `man_antritt`, `sieg`, `remis`, `nieder`, `antritt`";
			$ligadaten .= ", `order`, `rnd`, `published`, `bemerkungen`, `bem_int`, `checked_out_time`, `ordering`, `b_wertung`, `liga_mt`";
			$ligadaten .= ", `tiebr1`, `tiebr2`, `tiebr3`, `ersatz_regel`, `anzeige_ma`, `params`) VALUES ";
			$ligadaten = $ligadaten . "\n('" . $liga_name[0]->id . "','" . clm_core::$load->utf8decode($liga_name[0]->name) . "','" . $liga_name[0]->sid . "','" . $liga_name[0]->teil . "','" . $liga_name[0]->stamm . "','" . $liga_name[0]->ersatz . "','" . $liga_name[0]->rang . "','" . JID . "','" . $liga_name[0]->runden . "','" . $liga_name[0]->durchgang . "','" . $liga_name[0]->heim . "','" . $liga_name[0]->mail . "','" . $liga_name[0]->sl_mail;
			$ligadaten .= "','" . $liga_name[0]->sieg_bed . "','" . $liga_name[0]->runden_modus . "','" . $liga_name[0]->man_sieg . $liga_name[0]->man_remis . "','" . $liga_name[0]->man_nieder . "','" . $liga_name[0]->man_antritt . $liga_name[0]->sieg . "','" . $liga_name[0]->remis . "','" . $liga_name[0]->nieder . $liga_name[0]->antritt;
			$ligadaten .= "','" . $liga_name[0]->order . "','" . $liga_name[0]->rnd . "','" . $liga_name[0]->published . "','";
			$neu = str_replace($replace, ' ', $liga_name[0]->bemerkungen);
			$neu = str_replace($_surch, '\r\n', $neu);
			$ligadaten = $ligadaten . clm_core::$load->utf8decode($neu) . "'";
			$neu = str_replace($replace, ' ', $liga_name[0]->bem_int);
			$neu = str_replace($_surch, '\r\n', $neu);
			$ligadaten .= ",'" . clm_core::$load->utf8decode($neu) . "','1970-01-01 00:00:00','" . $liga_name[0]->ordering . "','" . $liga_name[0]->b_wertung . "','" . $liga_name[0]->liga_mt;
			$ligadaten .= "','" . $liga_name[0]->tiebr1 . "','" . $liga_name[0]->tiebr2 . "','" . $liga_name[0]->tiebr3 . "','" . $liga_name[0]->ersatz_regel . "','" . $liga_name[0]->anzeige_ma . "','" . $liga_name[0]->params . "');";
		} else {
			if ($clm_cuser != "1") {
				$liga_name[0]->sl = JID;
			}
			$ligadaten = "#1#" . "\n('" . clm_core::$load->utf8decode($liga_name[0]->name) . "','" . $liga_name[0]->sid . "','" . $liga_name[0]->teil . "','" . $liga_name[0]->stamm . "','" . $liga_name[0]->ersatz . "','" . $liga_name[0]->rang . "','" . $liga_name[0]->sl . "','" . $liga_name[0]->runden . "','" . $liga_name[0]->durchgang . "','" . $liga_name[0]->heim . "','" . $liga_name[0]->mail . "','" . $liga_name[0]->sl_mail;
			$ligadaten .= "','" . $liga_name[0]->sieg_bed . "','" . $liga_name[0]->runden_modus . "','" . $liga_name[0]->man_sieg . "','" . $liga_name[0]->man_remis . "','" . $liga_name[0]->man_nieder . "','" . $liga_name[0]->man_antritt . "','" . $liga_name[0]->sieg . "','" . $liga_name[0]->remis . "','" . $liga_name[0]->nieder . "','" . $liga_name[0]->antritt;
			$ligadaten .= "','" . $liga_name[0]->order . "','" . $liga_name[0]->rnd . "','" . $liga_name[0]->published . "','";
			$neu = str_replace($replace, ' ', $liga_name[0]->bemerkungen);
			$neu = str_replace($_surch, '\r\n', $neu);
			$neu = str_replace(';', ':', $neu);
			$ligadaten = $ligadaten . clm_core::$load->utf8decode($neu) . "'";
			$neu = str_replace($replace, ' ', $liga_name[0]->bem_int);
			$neu = str_replace($_surch, '\r\n', $neu);
			$neu = str_replace(';', ':', $neu);
			$ligadaten .= ",'" . clm_core::$load->utf8decode($neu) . "','1970-01-01 00:00:00','" . $liga_name[0]->ordering . "','" . $liga_name[0]->b_wertung . "','" . $liga_name[0]->liga_mt;
			$ligadaten .= "','" . $liga_name[0]->tiebr1 . "','" . $liga_name[0]->tiebr2 . "','" . $liga_name[0]->tiebr3 . "','" . $liga_name[0]->ersatz_regel . "','" . $liga_name[0]->anzeige_ma . "','" . $liga_name[0]->params . "');";
		}
	// DWZ Daten Vereine
	$sql = " SELECT s.* FROM #__clm_meldeliste_spieler as a"
		." LEFT JOIN #__clm_dwz_vereine AS s ON s.ZPS = a.zps AND s.sid = a.sid "
		." WHERE a.lid = ".$liga
		." AND s.zps IS NOT NULL "
		." GROUP BY a.zps "
		." ORDER BY a.zps ASC "
		;
		$db->setQuery($sql);
		$ver_dwz = $db->loadObjectList();
		if (count($ver_dwz) > 0) {
			if ($clm_sql == "1") {
				$dwz_verein = "\n\nINSERT INTO `#__clm_dwz_vereine` ( `sid`, `ZPS`, `LV`, `Verband`, `Vereinname`) VALUES ";
				$end = ",";
				for ($x = 0;$x < count($ver_dwz);$x++) {
					if ($x + 1 == count($ver_dwz)) {
						$end = ";";
					}
					$dwz_verein = $dwz_verein."\n('".$ver_dwz[$x]->sid."','".$ver_dwz[$x]->ZPS."','".$ver_dwz[$x]->LV."','".clm_core::$load->utf8decode($ver_dwz[$x]->Verband)."','"
			.clm_core::$load->utf8decode($ver_dwz[$x]->Vereinname)."')".$end
			;
				}
			} else {
				$dwz_verein = "\n\n#" . count($ver_dwz) . "#";
				for ($x = 0;$x < count($ver_dwz);$x++) {
					$dwz_verein = $dwz_verein."\n('".$ver_dwz[$x]->sid."','".$ver_dwz[$x]->ZPS."','".$ver_dwz[$x]->LV."','".clm_core::$load->utf8decode($ver_dwz[$x]->Verband)."','"
			.clm_core::$load->utf8decode($ver_dwz[$x]->Vereinname)."');"
			;
				}
			}
		}
		// Mannschaftsdaten
		$sql = " SELECT * FROM #__clm_mannschaften " . " WHERE liga = " . $liga . " ORDER BY tln_nr ";
		$db->setQuery($sql);
		$man = $db->loadObjectList();
		if (count($man) > 0) {
			if ($clm_sql == "1") {
//				$mannschaft = "\n\nINSERT INTO `#__clm_mannschaften` (`sid`, `name`, `liga`, `zps`, `liste`, `edit_liste`, `man_nr`, `tln_nr`, `mf`, `datum`, `edit_datum`, `lokal`, `bemerkungen`, `bem_int`,`published`, `ordering`) VALUES ";
				$mannschaft = "\n\nINSERT INTO `#__clm_mannschaften` (`sid`, `name`, `liga`, `zps`, `liste`, `edit_liste`, `man_nr`, `tln_nr`, `mf`, `sg_zps`, `datum`, `edit_datum`, "; 
				$mannschaft .= "`lokal`, `bemerkungen`, `bem_int`,`published`, `ordering`, `rankingpos`,`abzug`, `bpabzug`) VALUES ";
				$end = ",";
				for ($x = 0;$x < count($man);$x++) {
					if ($x + 1 == count($man)) {
						$end = ";";
					}
					if ($clm_cuser != "1") {
						$man[$x]->mf = JID;
					}
//					$mannschaft .= "\n('" . $man[$x]->sid . "','" . clm_core::$load->utf8decode($man[$x]->name) . "','" . $man[$x]->liga . "','" . $man[$x]->zps . "','" . $man[$x]->liste . "','" . JID . "','" . $man[$x]->man_nr . "','" . $man[$x]->tln_nr . "','" . JID . "','" . $man[$x]->datum . "','" . $man[$x]->edit_datum . "'";
					$mannschaft .= "\n('" . $man[$x]->sid . "','" . clm_core::$load->utf8decode($man[$x]->name) . "','" . $man[$x]->liga . "','" . $man[$x]->zps . "','" . $man[$x]->liste . "','" . JID; 
					$mannschaft .= "','" . $man[$x]->man_nr . "','" . $man[$x]->tln_nr . "','" . JID . "','" . $man[$x]->sg_zps . "','" . $man[$x]->datum . "','" . $man[$x]->edit_datum . "'";
					$neu = str_replace($replace, ' ', $man[$x]->lokal);
					$neu = str_replace($_surch, '\r\n', $neu);
					$mannschaft = $mannschaft . ",'" . clm_core::$load->utf8decode($neu) . "'";
					$neu = str_replace($replace, ' ', $man[$x]->bemerkungen);
					$neu = str_replace($_surch, '\r\n', $neu);
					$mannschaft = $mannschaft . ",'" . clm_core::$load->utf8decode($neu) . "'";
					$neu = str_replace($replace, ' ', $man[$x]->bem_int);
					$neu = str_replace($_surch, '\r\n', $neu);
					$mannschaft = $mannschaft . ",'" . clm_core::$load->utf8decode($neu) . "','" . $man[$x]->published . "','" . $man[$x]->ordering . "','" . $man[$x]->rankingpos . "','" . $man[$x]->abzug . "','" . $man[$x]->bpabzug . "')" . $end;
				}
			} else {
				$mannschaft = "\n\n#" . count($man) . "#";
				for ($x = 0;$x < count($man);$x++) {
					if ($clm_cuser != "1") {
						$man[$x]->mf = JID;
					}
					$mannschaft .= "\n('" . $man[$x]->sid . "','" . clm_core::$load->utf8decode($man[$x]->name) . "','" . $man[$x]->liga . "','" . $man[$x]->zps . "','" . $man[$x]->liste . "','" . $man[$x]->edit_liste . "','" . $man[$x]->man_nr . "','" . $man[$x]->tln_nr . "','" . $man[$x]->mf . "','" . $man[$x]->sg_zps . "','" . $man[$x]->datum . "','" . $man[$x]->edit_datum . "'";
					$neu = str_replace($replace, ' ', $man[$x]->lokal);
					$neu = str_replace($_surch, '\r\n', $neu);
					$neu = str_replace(';', ':', $neu);
					$mannschaft = $mannschaft . ",'" . clm_core::$load->utf8decode($neu) . "'";
					$neu = str_replace($replace, ' ', $man[$x]->bemerkungen);
					$neu = str_replace($_surch, '\r\n', $neu);
					$neu = str_replace(';', ':', $neu);
					$mannschaft = $mannschaft . ",'" . clm_core::$load->utf8decode($neu) . "'";
					$neu = str_replace($replace, ' ', $man[$x]->bem_int);
					$neu = str_replace($_surch, '\r\n', $neu);
					$neu = str_replace(';', ':', $neu);
					$mannschaft .= ",'" . clm_core::$load->utf8decode($neu) . "','" . $man[$x]->published . "','" . $man[$x]->ordering . "','" . $man[$x]->rankingpos . "','" . $man[$x]->abzug . "','" . $man[$x]->bpabzug . "');";
				}
			}
		}
		// DWZ Daten Spieler
		$sql = " SELECT s.* FROM #__clm_meldeliste_spieler as a" . " LEFT JOIN #__clm_dwz_spieler AS s ON s.ZPS = a.zps AND s.Mgl_Nr = a.mgl_nr AND s.sid = a.sid " . " WHERE a.lid = " . $liga . " AND a.mgl_nr > 0 " . " AND s.zps IS NOT NULL " . " ORDER BY a.zps ASC , a.snr ASC ";
		$db->setQuery($sql);
		$dwz_spl = $db->loadObjectList();
		if (count($dwz_spl) > 0) {
			if ($clm_sql == "1") {
				$spl_dwz = "\n\nINSERT INTO `#__clm_dwz_spieler` ( `sid`, `PKZ`, `ZPS`, `Mgl_Nr`, `Status`, `Spielername`, `Spielername_G`, `Geschlecht`, `Spielberechtigung`, `Geburtsjahr`, `Letzte_Auswertung`, `DWZ`, `DWZ_Index`, `FIDE_Elo`, `FIDE_Titel`, `FIDE_ID`, `FIDE_Land`) VALUES ";
				$end = ",";
				for ($x = 0;$x < count($dwz_spl);$x++) {
					if ($x + 1 == count($dwz_spl)) {
						$end = ";";
					}
					$spl_dwz = $spl_dwz . "\n('" . $dwz_spl[$x]->sid . "','" . $dwz_spl[$x]->PKZ . "','" . $dwz_spl[$x]->ZPS . "','" . $dwz_spl[$x]->Mgl_Nr . "','" . $dwz_spl[$x]->Status . "','" . clm_core::$load->utf8decode($dwz_spl[$x]->Spielername) . "','" . clm_core::$load->utf8decode($dwz_spl[$x]->Spielername_G) . "','" . $dwz_spl[$x]->Geschlecht . "','" . $dwz_spl[$x]->Spielberechtigung . "','" . $dwz_spl[$x]->Geburtsjahr . "','" . $dwz_spl[$x]->Letzte_Auswertung . "','" . $dwz_spl[$x]->DWZ . "','" . $dwz_spl[$x]->DWZ_Index . "','" . $dwz_spl[$x]->FIDE_Elo . "','" . $dwz_spl[$x]->FIDE_Titel . "','" . $dwz_spl[$x]->FIDE_ID . "','" . $dwz_spl[$x]->FIDE_Land . "')" . $end;
				}
			} else {
				$spl_dwz = "\n\n#" . count($dwz_spl) . "#";
				for ($x = 0;$x < count($dwz_spl);$x++) {
					$spl_dwz = $spl_dwz . "\n('" . $dwz_spl[$x]->sid . "','" . $dwz_spl[$x]->PKZ . "','" . $dwz_spl[$x]->ZPS . "','" . $dwz_spl[$x]->Mgl_Nr . "','" . $dwz_spl[$x]->Status . "','" . clm_core::$load->utf8decode($dwz_spl[$x]->Spielername) . "','" . clm_core::$load->utf8decode($dwz_spl[$x]->Spielername_G) . "','" . $dwz_spl[$x]->Geschlecht . "','" . $dwz_spl[$x]->Spielberechtigung . "','" . $dwz_spl[$x]->Geburtsjahr . "','" . $dwz_spl[$x]->Letzte_Auswertung . "','" . $dwz_spl[$x]->DWZ . "','" . $dwz_spl[$x]->DWZ_Index . "','" . $dwz_spl[$x]->FIDE_Elo . "','" . $dwz_spl[$x]->FIDE_Titel . "','" . $dwz_spl[$x]->FIDE_ID . "','" . $dwz_spl[$x]->FIDE_Land . "');";
				}
			}
		}
		// Meldelisten
		$sql = " SELECT * FROM #__clm_meldeliste_spieler " . " WHERE lid = " . $liga . " ORDER BY zps ASC , snr ASC ";
		$db->setQuery($sql);
		$ml = $db->loadObjectList();
		if (count($ml) > 0) {
			if ($clm_sql == "1") {
				$liste = "\n\nINSERT INTO `#__clm_meldeliste_spieler` (`sid`, `lid`, `mnr`, `snr`, `mgl_nr`, `zps`, `status`, `ordering`, `start_dwz`, `start_I0`, `FIDEelo`,";
				$liste .= " `DWZ`, `I0`, `Punkte`, `Partien`, `We`, `Leistung`, `EFaktor`, `Niveau`,`sum_saison`) VALUES ";
				$end = ",";
				for ($x = 0;$x < count($ml);$x++) {
					if ($x + 1 == count($ml)) {
						$end = ";";
					}
					$liste .= "\n('" . $ml[$x]->sid . "','" . $ml[$x]->lid . "','" . $ml[$x]->mnr . "','" . $ml[$x]->snr . "','" . $ml[$x]->mgl_nr;
					$liste .= "','" . $ml[$x]->zps . "','" . $ml[$x]->status . "','" . $ml[$x]->ordering . "','" . $ml[$x]->start_dwz . "','" . $ml[$x]->start_I0 . "','" . $ml[$x]->FIDEelo;
					$liste .= "','" . $ml[$x]->DWZ . "','" . $ml[$x]->I0 . "','" . $ml[$x]->Punkte . "','" . $ml[$x]->Partien . "','" . $ml[$x]->We . "','" . $ml[$x]->Leistung;
					$liste .= "','" . $ml[$x]->EFaktor . "','" . $ml[$x]->Niveau . "','" . $ml[$x]->sum_saison . "')" . $end;
				}
			} else {
				$liste = "\n\n#" . count($ml) . "#";
				for ($x = 0;$x < count($ml);$x++) {
					$liste .= "\n('" . $ml[$x]->sid . "','" . $ml[$x]->lid . "','" . $ml[$x]->mnr . "','" . $ml[$x]->snr . "','" . $ml[$x]->mgl_nr;
					$liste .= "','" . $ml[$x]->zps . "','" . $ml[$x]->status . "','" . $ml[$x]->ordering . "','" . $ml[$x]->start_dwz . "','" . $ml[$x]->start_I0 . "','" . $ml[$x]->FIDEelo;
					$liste .= "','" . $ml[$x]->DWZ . "','" . $ml[$x]->I0 . "','" . $ml[$x]->Punkte . "','" . $ml[$x]->Partien . "','" . $ml[$x]->We . "','" . $ml[$x]->Leistung;
					$liste .= "','" . $ml[$x]->EFaktor . "','" . $ml[$x]->Niveau . "','" . $ml[$x]->sum_saison . "');";
				}
			}
		}
		// Ranglisten
		// Rangliste ID
		$sql = " SELECT r.* FROM #__clm_liga as a " . " LEFT JOIN #__clm_rangliste_id as r ON r.gid = a.rang AND r.sid = a.sid " . " WHERE a.id = " . $liga . " AND r.sid IS NOT NULL ";
		$db->setQuery($sql);
		$rang_id = $db->loadObjectList();
		if ($clm_sql == "1" AND count($rang_id) > 0) {
			$id_rang = "\n\nINSERT INTO `#__clm_rangliste_id` (`gid`, `sid`, `zps`, `rang`, `published`, `bemerkungen`, `bem_int`, `ordering` ) VALUES ";
			$end = ",";
			for ($x = 0;$x < count($rang_id);$x++) {
				if ($x + 1 == count($rang_id)) {
					$end = ";";
				}
				$id_rang = $id_rang . "\n('" . $rang_id[$x]->gid . "','" . $rang_id[$x]->sid . "','" . $rang_id[$x]->zps . "','" . $rang_id[$x]->rang . "','" . $rang_id[$x]->published . "','" . $rang_id[$x]->bemerkungen . "','" . $rang_id[$x]->bem_int . "','" . $rang_id[$x]->ordering . "')" . $end;
			}
		}
		if ($clm_sql != "1") {
			$id_rang = "\n\n#" . count($rang_id) . "#";
			for ($x = 0;$x < count($rang_id);$x++) {
				$id_rang = $id_rang . "\n('" . $rang_id[$x]->gid . "','" . $rang_id[$x]->sid . "','" . $rang_id[$x]->zps . "','" . $rang_id[$x]->rang . "','" . $rang_id[$x]->published . "','" . $rang_id[$x]->bemerkungen . "','" . $rang_id[$x]->bem_int . "','" . $rang_id[$x]->ordering . "');";
			}
		}
		// Rangliste Name
		$sql = " SELECT r.* FROM #__clm_liga as a " . " LEFT JOIN #__clm_rangliste_name as r ON r.id = a.rang AND r.sid = a.sid " . " WHERE a.id = " . $liga . " AND r.sid IS NOT NULL ";
		$db->setQuery($sql);
		$rang_name = $db->loadObjectList();
		if ($clm_sql == "1" AND count($rang_name) > 0) {
			$name_rang = "\n\nINSERT INTO `#__clm_rangliste_name` (`Gruppe`, `Meldeschluss`, `geschlecht`, `alter_grenze`, `alter`, `status`, `sid`, `user`, `bemerkungen`, `bem_int`, `ordering`, `published` ) VALUES ";
			$end = ",";
			for ($x = 0;$x < count($rang_name);$x++) {
				if ($x + 1 == count($rang_name)) {
					$end = ";";
				}
				$name_rang = $name_rang . "\n('" . $rang_name[$x]->Gruppe . "','" . $rang_name[$x]->Meldeschluss . "','" . $rang_name[$x]->geschlecht . "','" . $rang_name[$x]->alter_grenze . "','" . $rang_name[$x]->alter . "','". $rang_name[$x]->status . "','" . $rang_name[$x]->sid . "','" . $rang_name[$x]->user . "','" . $rang_name[$x]->bemerkungen . "','" . $rang_name[$x]->bem_int . "','" . $rang_name[$x]->ordering . "','" . $rang_name[$x]->published . "')" . $end;
			}
		}
		if ($clm_sql != "1") {
			$name_rang = "\n\n#" . count($rang_name) . "#";
			for ($x = 0;$x < count($rang_name);$x++) {
				$name_rang = $name_rang . "\n('" . $rang_name[$x]->Gruppe . "','" . $rang_name[$x]->Meldeschluss . "','" . $rang_name[$x]->geschlecht . "','" . $rang_name[$x]->alter_grenze . "','" . $rang_name[$x]->alter . "','". $rang_name[$x]->status . "','" . $rang_name[$x]->sid . "','" . $rang_name[$x]->user . "','" . $rang_name[$x]->bemerkungen . "','" . $rang_name[$x]->bem_int . "','" . $rang_name[$x]->ordering . "','" . $rang_name[$x]->published . "');";
			}
		}
		// Rangliste Spieler
		$sql = " SELECT r.* FROM #__clm_liga as a " . " LEFT JOIN #__clm_rangliste_spieler as r ON r.Gruppe = a.rang AND r.sid = a.sid " . " WHERE a.id = " . $liga . " AND r.sid IS NOT NULL ";
		$db->setQuery($sql);
		$rang_spl = $db->loadObjectList();
		if ($clm_sql == "1" AND count($rang_spl) > 0) {
			$spl_rang = "\n\nINSERT INTO `#__clm_rangliste_spieler` (`Gruppe`, `ZPS`, `Mgl_Nr`, `PKZ`, `Rang`, `man_nr`, `sid`) VALUES ";
			$end = ",";
			for ($x = 0;$x < count($rang_spl);$x++) {
				if ($x + 1 == count($rang_spl)) {
					$end = ";";
				}
				$spl_rang = $spl_rang . "\n('" . $rang_spl[$x]->Gruppe . "','" . $rang_spl[$x]->ZPS . "','" . $rang_spl[$x]->Mgl_Nr . "','" . $rang_spl[$x]->PKZ . "','" . $rang_spl[$x]->Rang . "','" . $rang_spl[$x]->man_nr . "','" . $rang_spl[$x]->sid . "')" . $end;
			}
		}
		if ($clm_sql != "1") {
			$spl_rang = "\n\n#" . count($rang_spl) . "#";
			for ($x = 0;$x < count($rang_spl);$x++) {
				$spl_rang = $spl_rang . "\n('" . $rang_spl[$x]->Gruppe . "','" . $rang_spl[$x]->ZPS . "','" . $rang_spl[$x]->Mgl_Nr . "','" . $rang_spl[$x]->PKZ . "','" . $rang_spl[$x]->Rang . "','" . $rang_spl[$x]->man_nr . "','" . $rang_spl[$x]->sid . "');";
			}
		}
		// Mannschaftsrunden
		$sql = " SELECT * FROM #__clm_rnd_man " . " WHERE lid = " . $liga . " ORDER BY dg ASC, runde ASC , paar ASC, heim ASC ";
		$db->setQuery($sql);
		$mrnd = $db->loadObjectList();
		if (count($mrnd) > 0) {
			if ($clm_sql == "1") {
				$man_rnd = "\n\nINSERT INTO `#__clm_rnd_man` (`sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner`, `brettpunkte`, `manpunkte`, `bp_sum`, `mp_sum`, `gemeldet`, `editor`, `dwz_editor`, `zeit`, `edit_zeit`, `dwz_zeit`, `published`, `ordering`) VALUES ";
				$end = ",";
				for ($x = 0;$x < count($mrnd);$x++) {
					if ($x + 1 == count($mrnd)) {
						$end = ";";
					}
					if ($mrnd[$x]->brettpunkte == "") {
						$bp = "NULL";
					} else {
						$bp = $mrnd[$x]->brettpunkte;
					}
					if ($mrnd[$x]->manpunkte == "") {
						$mp = "NULL";
					} else {
						$mp = $mrnd[$x]->manpunkte;
					}
					if ($mrnd[$x]->bp_sum == "") {
						$bp_sum = "NULL";
					} else {
						$bp_sum = $mrnd[$x]->bp_sum;
					}
					if ($mrnd[$x]->mp_sum == "") {
						$mp_sum = "NULL";
					} else {
						$mp_sum = $mrnd[$x]->mp_sum;
					}
					if ($mrnd[$x]->gemeldet == "") {
						$gemeldet = "NULL";
					} else {
						$gemeldet = JID;
					}
					if ($mrnd[$x]->editor == "") {
						$editor = "NULL";
					} else {
						$editor = JID;
					}
					if ($mrnd[$x]->dwz_editor == "") {
						$dwz_editor = "NULL";
					} else {
						$dwz_editor = JID;
					}
					$man_rnd = $man_rnd . "\n('" . $mrnd[$x]->sid . "','" . $mrnd[$x]->lid . "','" . $mrnd[$x]->runde . "','" . $mrnd[$x]->paar . "','" . $mrnd[$x]->dg . "','" . $mrnd[$x]->heim . "','" . $mrnd[$x]->tln_nr . "','" . $mrnd[$x]->gegner . "','" . $bp . "','" . $mp . "','" . $bp_sum . "','" . $mp_sum . "','" . $gemeldet . "','" . $editor . "','" . $dwz_editor . "','" . $mrnd[$x]->zeit . "','" . $mrnd[$x]->edit_zeit . "','" . $mrnd[$x]->dwz_zeit . "','" . $mrnd[$x]->published . "','" . $mrnd[$x]->ordering . "')" . $end;
				}
			} else {
				$man_rnd = "\n\n#" . count($mrnd) . "#";
				for ($x = 0;$x < count($mrnd);$x++) {
					if ($mrnd[$x]->brettpunkte == "") {
						$bp = "NULL";
					} else {
						$bp = $mrnd[$x]->brettpunkte;
					}
					if ($mrnd[$x]->manpunkte == "") {
						$mp = "NULL";
					} else {
						$mp = $mrnd[$x]->manpunkte;
					}
					if ($mrnd[$x]->bp_sum == "") {
						$bp_sum = "NULL";
					} else {
						$bp_sum = $mrnd[$x]->bp_sum;
					}
					if ($mrnd[$x]->mp_sum == "") {
						$mp_sum = "NULL";
					} else {
						$mp_sum = $mrnd[$x]->mp_sum;
					}
					if ($mrnd[$x]->gemeldet == "") {
						$gemeldet = "NULL";
					} else {
						if ($clm_cuser != "1") {
							$gemeldet = JID;
						} else {
							$gemeldet = $mrnd[$x]->gemeldet;
						}
					}
					if ($mrnd[$x]->editor == "") {
						$editor = "NULL";
					} else {
						if ($clm_cuser != "1") {
							$editor = JID;
						} else {
							$editor = $mrnd[$x]->editor;
						}
					}
					if ($mrnd[$x]->dwz_editor == "") {
						$dwz_editor = "NULL";
					} else {
						if ($clm_cuser != "1") {
							$dwz_editor = JID;
						} else {
							$dwz_editor = $mrnd[$x]->dwz_editor;
						}
					}
					$man_rnd = $man_rnd . "\n('" . $mrnd[$x]->sid . "','" . $mrnd[$x]->lid . "','" . $mrnd[$x]->runde . "','" . $mrnd[$x]->paar . "','" . $mrnd[$x]->dg . "','" . $mrnd[$x]->heim . "','" . $mrnd[$x]->tln_nr . "','" . $mrnd[$x]->gegner . "','" . $bp . "','" . $mp . "','" . $bp_sum . "','" . $mp_sum . "','" . $gemeldet . "','" . $editor . "','" . $dwz_editor . "','" . $mrnd[$x]->zeit . "','" . $mrnd[$x]->edit_zeit . "','" . $mrnd[$x]->dwz_zeit . "','" . $mrnd[$x]->published . "','" . $mrnd[$x]->ordering . "');";
				}
			}
		}
		// Spielerrunden
		$sql = " SELECT * FROM #__clm_rnd_spl " . " WHERE lid = " . $liga . " ORDER BY dg ASC, runde ASC , paar ASC, brett ASC ";
		$db->setQuery($sql);
		$srnd = $db->loadObjectList();
		if (!is_null($srnd) AND count($srnd) > 0) {
			if ($clm_sql == "1") {
				$spl_rnd = "\n\nINSERT INTO `#__clm_rnd_spl` (`sid`, `lid`, `runde`, `paar`, `dg`, `tln_nr`, `brett`, `heim`, `weiss`, `spieler`, `zps`, `gegner`, `gzps`, `ergebnis`, `kampflos`, `punkte`, `gemeldet`, `dwz_edit`, `dwz_editor`) VALUES ";
				$end = ",";
				for ($x = 0;$x < count($srnd);$x++) {
					if ($x + 1 == count($srnd)) {
						$end = ";";
					}
					if ($srnd[$x]->dwz_edit == "") {
						$dwz_edit = "NULL";
					} else {
						$dwz_edit = $srnd[$x]->dwz_edit;
					}
					if ($srnd[$x]->dwz_editor == "") {
						$dwz_editor = "NULL";
					} else {
						$dwz_editor = JID;
					}
					if ($srnd[$x]->gemeldet == "") {
						$gemeldet = "NULL";
					} else {
						$gemeldet = JID;
					}
					$spl_rnd = $spl_rnd . "\n('" . $srnd[$x]->sid . "','" . $srnd[$x]->lid . "','" . $srnd[$x]->runde . "','" . $srnd[$x]->paar . "','" . $srnd[$x]->dg . "','" . $srnd[$x]->tln_nr . "','" . $srnd[$x]->brett . "','" . $srnd[$x]->heim . "','" . $srnd[$x]->weiss . "','" . $srnd[$x]->spieler . "','" . $srnd[$x]->zps . "','" . $srnd[$x]->gegner . "','" . $srnd[$x]->gzps . "','" . $srnd[$x]->ergebnis . "','" . $srnd[$x]->kampflos . "','" . $srnd[$x]->punkte . "','" . $gemeldet . "','" . $dwz_edit . "','" . $dwz_editor . "')" . $end;
				}
			} else {
				$spl_rnd = "\n\n#" . count($srnd) . "#";
				for ($x = 0;$x < count($srnd);$x++) {
					if ($srnd[$x]->dwz_edit == "") {
						$dwz_edit = "NULL";
					} else {
						$dwz_edit = $srnd[$x]->dwz_edit;
					}
					if ($srnd[$x]->dwz_editor == "") {
						$dwz_editor = "NULL";
					} else {
						if ($clm_cuser != "1") {
							$dwz_editor = JID;
						} else {
							$dwz_editor = $srnd[$x]->dwz_editor;
						}
					}
					if ($srnd[$x]->gemeldet == "") {
						$gemeldet = "NULL";
					} else {
						if ($clm_cuser != "1") {
							$gemeldet = JID;
						} else {
							$gemeldet = $srnd[$x]->gemeldet;
						}
					}
					$spl_rnd = $spl_rnd . "\n('" . $srnd[$x]->sid . "','" . $srnd[$x]->lid . "','" . $srnd[$x]->runde . "','" . $srnd[$x]->paar . "','" . $srnd[$x]->dg . "','" . $srnd[$x]->tln_nr . "','" . $srnd[$x]->brett . "','" . $srnd[$x]->heim . "','" . $srnd[$x]->weiss . "','" . $srnd[$x]->spieler . "','" . $srnd[$x]->zps . "','" . $srnd[$x]->gegner . "','" . $srnd[$x]->gzps . "','" . $srnd[$x]->ergebnis . "','" . $srnd[$x]->kampflos . "','" . $srnd[$x]->punkte . "','" . $gemeldet . "','" . $dwz_edit . "','" . $dwz_editor . "');";
				}
			}
		} else {
			if ($clm_sql != "1") {
				$spl_rnd = "\n\n#0#";
				$spl_rnd = $spl_rnd . "\n  ";
			}
		}
		// Rundentermine
		$sql = " SELECT * FROM #__clm_runden_termine " . " WHERE liga = " . $liga . " ORDER BY nr ASC ";
		$db->setQuery($sql);
		$trnd = $db->loadObjectList();
		if (count($trnd) > 0) {
			if ($clm_sql == "1") {
				$ter_rnd = "\n\nINSERT INTO `#__clm_runden_termine` ( `sid`, `name`, `liga`, `nr`, `datum`, `meldung`, `sl_ok`, `published`, `bemerkungen`, `bem_int`,`gemeldet`,`editor`,`zeit`,`edit_zeit`, `ordering`) VALUES ";
				$end = ",";
				for ($x = 0;$x < count($trnd);$x++) {
					if ($x + 1 == count($trnd)) {
						$end = ";";
					}
					if ($trnd[$x]->gemeldet == "") {
						$gemeldet = "NULL";
					} else {
						$gemeldet = JID;
					}
					if ($trnd[$x]->editor == "") {
						$editor = "NULL";
					} else {
						$editor = JID;
					}
					$ter_rnd = $ter_rnd . "\n('" . $trnd[$x]->sid . "','" . $trnd[$x]->name . "','" . $trnd[$x]->liga . "','" . $trnd[$x]->nr . "','" . $trnd[$x]->datum . "','" . $trnd[$x]->meldung . "','" . $trnd[$x]->sl_ok . "','" . $trnd[$x]->published . "'";
					$neu = str_replace($replace, ' ', $trnd[$x]->bemerkungen);
					$neu = str_replace($_surch, '\r\n', $neu);
					$ter_rnd = $ter_rnd . ",'" . clm_core::$load->utf8decode($neu) . "'";
					$neu = str_replace($replace, ' ', $trnd[$x]->bem_int);
					$neu = str_replace($_surch, '\r\n', $neu);
					$ter_rnd = $ter_rnd . ",'" . clm_core::$load->utf8decode($neu) . "','" . $gemeldet . "','" . $editor . "','" . $trnd[$x]->zeit . "','" . $trnd[$x]->edit_zeit . "','" . $trnd[$x]->ordering . "')" . $end;
				}
			} else {
				$ter_rnd = "\n\n#" . count($trnd) . "#";
				for ($x = 0;$x < count($trnd);$x++) {
					if ($trnd[$x]->gemeldet == "") {
						$gemeldet = "NULL";
					} else {
						if ($clm_cuser != "1") {
							$gemeldet = JID;
						} else {
							$gemeldet = $trnd[$x]->gemeldet;
						}
					}
					if ($trnd[$x]->editor == "") {
						$editor = "NULL";
					} else {
						if ($clm_cuser != "1") {
							$editor = JID;
						} else {
							$editor = $trnd[$x]->gemeldet;
						}
					}
					$ter_rnd = $ter_rnd . "\n('" . $trnd[$x]->sid . "','" . $trnd[$x]->name . "','" . $trnd[$x]->liga . "','" . $trnd[$x]->nr . "','" . $trnd[$x]->datum . "','" . $trnd[$x]->meldung . "','" . $trnd[$x]->sl_ok . "','" . $trnd[$x]->published . "'";
					$neu = str_replace($replace, ' ', $trnd[$x]->bemerkungen);
					$neu = str_replace($_surch, '\r\n', $neu);
					$neu = str_replace(';', ':', $neu);										
					$ter_rnd = $ter_rnd . ",'" . clm_core::$load->utf8decode($neu) . "'";
					$neu = str_replace($replace, ' ', $trnd[$x]->bem_int);
					$neu = str_replace($_surch, '\r\n', $neu);
					$neu = str_replace(';', ':', $neu);										
					$ter_rnd = $ter_rnd . ",'" . clm_core::$load->utf8decode($neu) . "','" . $gemeldet . "','" . $editor . "','" . $trnd[$x]->zeit . "','" . $trnd[$x]->edit_zeit . "','" . $trnd[$x]->ordering . "');";
				}
			}
		}
		// CLM User
		if (($clm_cuser == "1" OR $clm_juser == "1") AND $clm_sql != "1") {
			$sql = " SELECT u.jid FROM #__clm_mannschaften as a " . " LEFT JOIN #__clm_user as u ON u.jid = a.mf AND u.sid = a.sid" . " WHERE a.liga = $liga AND a.mf > 0 " . " AND a.sid = " . $liga_name[0]->sid . " AND u.jid IS NOT NULL ";
			$db->setQuery($sql);
			$clm = $db->loadObjectList();
			// Array mit jid zur Erstellung der Joomla User schreiben
			$juser_jid = array();
			$sl = 0;
			for ($x = 0;$x < count($clm);$x++) {
				if ($clm[$x]->jid != "") {
					$juser_jid[$clm[$x]->jid] = $clm[$x]->jid;
					if ($liga_name[0]->sl == $clm[$x]->jid) {
						$sl = "1";
					}
				}
			}
			if ($sl != "1") {
				$sql = " SELECT jid FROM #__clm_user " . " WHERE jid = " . $liga_name[0]->sl . " AND sid = " . $liga_name[0]->sid . " AND jid IS NOT NULL ";
				$db->setQuery($sql);
				$clm = $db->loadObjectList();
				if ($clm[0]->jid != "") {
					$juser_jid[$clm[0]->jid] = $clm[0]->jid;
				}
			}
			$jids = implode(',', $juser_jid);
			$sql = " SELECT * FROM #__clm_user " . " WHERE sid = " . $liga_name[0]->sid . " AND jid IN ($jids)";
			$db->setQuery($sql);
			$clm = $db->loadObjectList();
			for ($x = 0;$x < count($juser_jid);$x++) {
				$clm_user = $clm_user . "\n('" . $clm[$x]->sid . "','" . $clm[$x]->jid . "','" . clm_core::$load->utf8decode($clm[$x]->name) . "','" . clm_core::$load->utf8decode($clm[$x]->username) . "','" . $clm[$x]->aktive . "','" . clm_core::$load->utf8decode($clm[$x]->email) . "','" . $clm[$x]->usertype . "','" . clm_core::$load->utf8decode($clm[$x]->tel_fest) . "','" . clm_core::$load->utf8decode($clm[$x]->tel_mobil) . "','" . $clm[$x]->user_clm . "','" . $clm[$x]->zps . "','" . $clm[$x]->published . "'";
				$neu = str_replace($replace, ' ', $clm[$x]->bemerkungen);
				$neu = str_replace($_surch, '\r\n', $neu);
				$neu = str_replace(';', ':', $neu);
				$clm_user = $clm_user . ",'" . clm_core::$load->utf8decode($neu) . "'";
				$neu = str_replace($replace, ' ', $clm[$x]->bem_int);
				$neu = str_replace($_surch, '\r\n', $neu);
				$neu = str_replace(';', ':', $neu);
				$clm_user = $clm_user . ",'" . clm_core::$load->utf8decode($neu) . "');";
			}
			$clm_user = "\n\n#" . count($juser_jid) . "#" . $clm_user;
		}
		// Joomla User
		if ($clm_juser == "1" AND $clm_sql != "1") {
			$sql = " SELECT * FROM #__users " . " WHERE id IN ($jids)" . " AND id IS NOT NULL ";
			$db->setQuery($sql);
			$clm = $db->loadObjectList();
			for ($x = 0;$x < count($clm);$x++) {
				$juser = $juser . "\n('" . clm_core::$load->utf8decode($clm[$x]->name . "','" . $clm[$x]->username . "','" . $clm[$x]->email . "','" . $clm[$x]->password . "','" . $clm[$x]->usertype . "','" . $clm[$x]->block . "','" . $clm[$x]->sendEmail . "','" . $clm[$x]->gid . "','" . $clm[$x]->registerDate . "','" . $clm[$x]->lastvisitDate . "','" . $clm[$x]->activation . "','");
				// evtl. Standardparameter verwenden -> Sicherheit !!
				$neu = str_replace($replace, ' ', $clm[$x]->params);
				$neu = str_replace($_surch, '\r\n', $neu);
				$neu = str_replace(';', ':', $neu);
				$juser = $juser . clm_core::$load->utf8decode($neu) . "');";
			}
			$juser = "\n\n#" . count($clm) . "#" . $juser;
		}
		// ggf. Verzeichnis erstellen
		if (!file_exists($path)) {
			jimport('joomla.filesystem.folder');
			JFolder::create($path);
		}
		// Datenblöcke zusammensetzen
		$buffer = $comment . $ligadaten . $dwz_verein . $mannschaft . $spl_dwz . $liste . $name_rang . $id_rang . $spl_rang . $man_rnd . $spl_rnd . $ter_rnd . $clm_user . $juser;
		// Datei schreiben ggf. Fehlermeldung absetzen
		jimport('joomla.filesystem.file');
		if (!JFile::write($write, $buffer)) {
			$mainframe->enqueueMessage(JText::_('DB_FEHLER_SCHREIB'),'warning');
		}
		if ($clm_sql == "1") {
			$msg = JText::_('DB_SQL');
		} else {
			$msg = JText::_('DB_CLM');
		}
		$mainframe->enqueueMessage($msg,'message');
		$mainframe->redirect('index.php?option=com_clm&view=db');
	}
	public static function liga() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
		$db = JFactory::getDBO();
		$option = clm_core::$load->request_string('option');
		$section = clm_core::$load->request_string('section');
		$sql = " SELECT id FROM #__clm_saison " . " WHERE published  = 1 AND archiv = 0";
		$db->setQuery($sql);
		$sid = $db->loadObjectList();
		$sql = " SELECT a.id,a.name FROM #__clm_liga as a " . " LEFT JOIN #__clm_saison as s ON s.id = a.sid " . " WHERE s.archiv = 0 " . " and s.published = 1" . " and a.published = 1" . " ORDER BY s.id ASC, a.id ASC ";
		$db->setQuery($sql);
		$liga = $db->loadObjectList();
		return $liga;
	}
	public static function import() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		defined('_JEXEC') or die('Invalid Token');
		$db = JFactory::getDBO();
		$option = clm_core::$load->request_string('option');
		$section = clm_core::$load->request_string('section');
		$datei = clm_core::$load->request_string('import');
		$saison = clm_core::$load->request_string('saison_import');
		$liga = clm_core::$load->request_string('liga_import');
		$clm_user = clm_core::$load->request_string('imp_user');
		$pub_user = clm_core::$load->request_string('imp_pub');
		$override = clm_core::$load->request_string('override');
		if ($pub_user == "1") {
			$publish = 0;
			$block = 1;
		} else {
			$publish = 1;
			$block = 0;
		}
		if (!$datei) {
			$mainframe->enqueueMessage(JText::_('DB_NO_DATEI_IMPORT'),'warning');
			$mainframe->redirect('index.php?option=com_clm&view=db');
		}
		if (!$saison) {
			$mainframe->enqueueMessage(JText::_('DB_NO_SAISON_IMPORT'),'warning');
			$mainframe->redirect('index.php?option=com_clm&view=db');
		}
		if (!$liga) {
			$mainframe->enqueueMessage(JText::_('DB_NO_LIGA_IMPORT'),'warning');
			$mainframe->redirect('index.php?option=com_clm&view=db');
		}
		jimport('joomla.filesystem.file');
		$path = JPath::clean(JPATH_ADMINISTRATOR . DS . 'components' . DS . $option . DS . 'upload');
		//$content = JFile::read($path . DS . $datei);
		$content = file_get_contents($path . DS . $datei);
		$content = html_entity_decode($content);
		// Inhalt in Teile zerlegen
		$part = explode("#", $content);
		// Zählmarken holen. Bspl.: $part[1] ist Zählmarke der Liga, dann ist $part[2] der Datenteil usw.
		$cnt_liga = $part[1];
		$cnt_dwz_ver = $part[3];
		$cnt_man = $part[5];
		$cnt_dwz_spl = $part[7];
		$cnt_spl = $part[9];
		$rang_name = $part[11];
		$rang_id = $part[13];
		$rang_spl = $part[15];
		$cnt_rnd_man = $part[17];
		$cnt_rnd_spl = $part[19];
		$cnt_rnd_ter = $part[21];
		if (isset($part[23])) $cnt_user = $part[23]; else $cnt_user = "0";
		if (isset($part[25])) $cnt_juser = $part[25]; else $cnt_juser = "0";
		// Einzeldaten zerlegen
		$liga_daten = explode("','", $part[2]);
		$ver_dwz = explode(";", $part[4]);
		$man_daten = explode(";", $part[6]);
		$spl_dwz = explode(";", $part[8]);
		$melde_daten = explode(";", $part[10]);
		$rang_name_daten = explode(";", $part[12]);
		$rang_id_daten = explode(";", $part[14]);
		$rang_spl_daten = explode(";", $part[16]);
		$man_rnd_daten = explode(";", $part[18]);
		$spl_rnd_daten = explode(";", $part[20]);
		$rnd_ter_daten = explode(";", $part[22]);
		if (isset($part[24])) $clm_dat = explode(";", $part[24]); else $clm_dat = array();
		if (isset($part[26])) $jos_dat = explode(";", $part[26]); else $jos_dat = array();
		if ($override != "1") {
			// Sicherheitscheck ob Datei manipuliert wurde
			$fehler = 0;
			if ($cnt_man + 1 > count($man_daten)) {
				$mainframe->enqueueMessage(JText::_('DB_ANZAHL_KLEIN'),'warning');
				$fehler++;
			}
			if ($cnt_man + 1 < count($man_daten)) {
				$mainframe->enqueueMessage(JText::_('DB_ANZAHL_GROSS'),'warning');
				$fehler++;
			}
			if ($cnt_man != $liga_daten[2] OR count($man_daten) - 1 != $liga_daten[2]) {
				$mainframe->enqueueMessage(JText::_('DB_ANZAHL_FALSCH'),'warning');
				$fehler++;
			}
			if ($cnt_spl + 1 > count($melde_daten)) {
				$mainframe->enqueueMessage(JText::_('DB_ANZAHL_SP_KLEIN'),'warning');
				$fehler++;
			}
			if ($cnt_spl + 1 < count($melde_daten)) {
				$mainframe->enqueueMessage(JText::_('DB_ANZAHL_SP_GROSS'),'warning');
				$fehler++;
			}
			if ($cnt_rnd_man + 1 > count($man_rnd_daten)) {
				$mainframe->enqueueMessage(JText::_('DB_ANZAHL_MR_KLEIN'),'warning');
				$fehler++;
			}
			if ($cnt_rnd_man + 1 < count($man_rnd_daten)) {
				$mainframe->enqueueMessage(JText::_('DB_ANZAHL_MR_GROSS'),'warning');
				$fehler++;
			}
			$runden_modus = $liga_daten[13];
			if ($runden_modus == 4 OR $runden_modus == 5) {
				$rnd_anz = 0;
				for ($i = 1;$i < ($liga_daten[7]+1);$i++) {
					$rnd_anz += pow(2,$i);
				}
				if ($runden_modus == 5) $rnd_anz += 2;
//				if (($cnt_rnd_man != $rnd_anz) OR (count($man_rnd_daten) - 1 != $rnd_anz)) {
				if (($cnt_rnd_man != (0.5 * $rnd_anz)) OR (count($man_rnd_daten) - 1 != (0.5 * $rnd_anz))) {
					$mainframe->enqueueMessage(JText::_('DB_ANZAHL_MR_FALSCH'),'warning');
					$fehler++;
				}
			} else {
				if (($cnt_rnd_man != ($liga_daten[2] * $liga_daten[7] * $liga_daten[8])) OR (count($man_rnd_daten) - 1 != ($liga_daten[2] * $liga_daten[7] * $liga_daten[8]))) {
					$mainframe->enqueueMessage(JText::_('DB_ANZAHL_MR_FALSCH'),'warning');
					$fehler++;
				}
			}
			if ($cnt_rnd_spl + 1 > count($spl_rnd_daten)) {
				$mainframe->enqueueMessage(JText::_('DB_ANZAHL_SR_KLEIN'),'warning');
				$fehler++;
			}
			if ($cnt_rnd_spl + 1 < count($spl_rnd_daten)) {
				$mainframe->enqueueMessage(JText::_('DB_ANZAHL_SR_GROSS'),'warning');
				$fehler++;
			}
			if ($cnt_rnd_ter + 1 > count($rnd_ter_daten)) {
				$mainframe->enqueueMessage(JText::_('DB_ANZAHL_RU_KLEIN'),'warning');
				$fehler++;
			}
			if ($cnt_rnd_ter + 1 < count($rnd_ter_daten)) {
				$mainframe->enqueueMessage(JText::_('DB_ANZAHL_RU_GROSS'),'warning');
				$fehler++;
			}
			if ($cnt_user > (1 + $liga_daten[2])) {
				$mainframe->enqueueMessage(JText::_('DB_CLM_NUTZER'),'warning');
				$fehler++;
			}
			if ($cnt_juser > (1 + $liga_daten[2])) {
				$mainframe->enqueueMessage(JText::_('DB_JOOMLA_NUTZER'),'warning');
				$fehler++;
			}
			if ($fehler > 0) {
//				$mainframe->enqueueMessage(JText::_('DB_IMPORT'),'notice');
//				$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
				$msg = JText::_('DB_IMPORT') . ' ' . $datei . ' <b>nicht</b> ' . JText::_('DB_ERFOLGREICH');
				$mainframe->enqueueMessage($msg,'error');
				$mainframe->redirect('index.php?option=com_clm&view=db');
			}
		}
		// User anlegen
		// Arrays verknüpfen email => id (#_clm_user) // email => group_id // email => registered // email => alte jid
		$email_clmid = array();
		$email_gid = array();
		$email_reg = array();
		$email_altjid = array();
		// Sollen User importiert werden ?
		if ($clm_user != "1") {
			// Schleife zum erstellen der CLM User
			for ($x = 0;$x < $cnt_user;$x++) {
				$user = explode("','", $clm_dat[$x]);
				$email = clm_core::$load->utf8encode($user[5]);
				// User schon vorhanden ?
				$sql = " SELECT id,jid FROM #__clm_user " . " WHERE email = '$email' " . " AND sid =" . $saison;
				$db->setQuery($sql);
				$clm_exist = $db->loadObjectList();
				// User existiert nicht -> anlegen
				if (!$clm_exist) {
					$row = JTable::getInstance('users', 'TableCLM');
					$row->sid = $saison;
					$row->jid = $user[1];
					$row->name = clm_core::$load->utf8encode($user[2]);
					$row->username = clm_core::$load->utf8encode($user[3]);
					$row->aktive = $user[4];
					$row->email = $email;
					$row->usertype = clm_core::$load->utf8encode($user[6]);
					$row->tel_fest = clm_core::$load->utf8encode($user[7]);
					$row->tel_mobil = clm_core::$load->utf8encode($user[8]);
					$row->user_clm = clm_core::$load->utf8encode($user[9]);
					$row->zps = $user[10];
					$row->published = $publish; //$user[11];
					$row->bemerkungen = clm_core::$load->utf8encode($user[12]);
					$row->bem_int = clm_core::$load->utf8encode(substr($user[13], 0, -2));
					// CLM User erstellen
					$row->store();
					// Informationen für Joomla User in Arrays schreiben
					if ($row->user_clm > 69) {
						$email_reg[$email] = 'Manager';
						$email_gid[$email] = '23';
					} else {
						$email_reg[$email] = 'Registered';
						$email_gid[$email] = '18';
					}
					$email_clmid[$email] = $row->id;
				}
				// User existiert -> Array jid => sid schreiben
				else {
					$email_clmid[$email] = $clm_exist[0]->id;
				}
				$email_altjid[$email] = $user[1];
			}
			// Joomla Userdaten vorhanden ?
			if ($cnt_juser != "0") {
				// Userdaten zerlegen
				// Array verknüpft email => id (#_user)
				$email_josid = array();
				$emails = array();
				// Schleife zum erstellen der Joomla User aus vorhandenen Daten
				for ($x = 0;$x < $cnt_juser;$x++) {
					$user = explode("','", $jos_dat[$x]);
					$email = clm_core::$load->utf8encode($user[2]);
					$emails[] = $email;
					// in Joomla DB nach User suchen
					$sql = " SELECT id FROM #__users " . " WHERE email = '$email' ";
					$db->setQuery($sql);
					$jos_exist = $db->loadObjectList();
					// Joomla User existiert nicht -> anlegen
					if (!$jos_exist) {
						$jos_user = JTable::getInstance('jos_users', 'TableCLM');
						$jos_user->name = clm_core::$load->utf8encode(substr($user[0], 3));
						$jos_user->username = clm_core::$load->utf8encode($user[1]);
						$jos_user->email = $email;
						$jos_user->password = clm_core::$load->utf8encode($user[3]);
						//$jos_user->password	= "";
						$jos_user->usertype = $user[4];
						$jos_user->block = $block; //$user[5];
						$jos_user->sendEmail = $user[6];
						$jos_user->gid = $user[7];
						$jos_user->registerDate = $user[8];
						$jos_user->lastvisitDate = $user[9];
						$jos_user->activation = $user[10];
						$jos_user->params = clm_core::$load->utf8encode('admin_language=de-DE\nlanguage=de-DE\neditor=none\nhelpsite=\ntimezone=0\n\n');
						// User erstellen
						$jos_user->store();
						// In Tabelle core_acl_aro schreiben
						$acl_aro = JTable::getInstance('acl_aro', 'TableCLM');
						$acl_aro->section_value = 'users';
						$acl_aro->value = $jos_user->id;
						$acl_aro->name = $jos_user->name;
						// speichern
						$acl_aro->store();
						// In Tabelle core_acl_groups_aro_map schreiben
						$core_aro = JTable::getInstance('core_aro', 'TableCLM');
						$core_aro->group_id = $jos_user->gid;
						$core_aro->aro_id = $acl_aro->id;
						// speichern
						$core_aro->store();
						$email_josid[$email] = $jos_user->id;
					}
					// Joomla User existiert -> Array jid => sid schreiben
					else {
						$email_josid[$email] = $jos_exist[0]->id;
					}
				}
			}
			// Wenn Anzahl CLM User > Anzahl Jos User dann diese Schleife durchlaufen -> Es fehlen Accounts
			if ($cnt_user > $cnt_juser) {
				//if($clm_juser =="1"){
				if ($cnt_juser != "0") {
					$mainframe->enqueueMessage(JText::_('DB_USER'),'warning');
					$mainframe->enqueueMessage(JText::_('DB_DUMMY'),'notice');
				} else {
					$mainframe->enqueueMessage(JText::_('DB_USER_WENIG'),'warning');
					$mainframe->enqueueMessage(JText::_('DB_NUTZER'),'notice');
				}
				//}
				// Schleife zum erstellen der Joomla User aus vorhandenen CLM Daten
				for ($x = 0;$x < $cnt_user;$x++) {
					$user = explode("','", $clm_dat[$x]);
					$email = clm_core::$load->utf8encode($user[5]);
					$emails[] = $email;
					// in Joomla DB nach User suchen
					$sql = " SELECT id FROM #__users " . " WHERE email = '$email' ";
					$db->setQuery($sql);
					$jos_exist = $db->loadObjectList();
					// Joomla User existiert nicht -> anlegen
					if (!$jos_exist) {
						$jos_user = JTable::getInstance('jos_users', 'TableCLM');
						$jos_user->name = clm_core::$load->utf8encode($user[2]);
						$jos_user->username = clm_core::$load->utf8encode($user[3]);
						$jos_user->email = $email;
						$jos_user->usertype = $email_reg[$email];
						$jos_user->block = $block; //$user[11];
						$jos_user->sendEmail = '0';
						$jos_user->gid = $email_gid[$email];
						$jos_user->params = clm_core::$load->utf8encode('admin_language=de-DE\nlanguage=de-DE\neditor=none\nhelpsite=\ntimezone=0\n\n');
						// User erstellen
						$jos_user->store();
						// In Tabelle core_acl_aro schreiben
						$acl_aro = JTable::getInstance('acl_aro', 'TableCLM');
						$acl_aro->section_value = 'users';
						$acl_aro->value = $jos_user->id;
						$acl_aro->name = $jos_user->name;
						// speichern
						$acl_aro->store();
						// In Tabelle core_acl_groups_aro_map schreiben
						$core_aro = JTable::getInstance('core_aro', 'TableCLM');
						$core_aro->group_id = $jos_user->gid;
						$core_aro->aro_id = $acl_aro->id;
						// speichern
						$core_aro->store();
						$email_josid[$email] = $jos_user->id;
					}
					// Joomla User existiert -> Array jid => sid schreiben
					else {
						$email_josid[$email] = $jos_exist[0]->id;
					}
				}
//			}
				// Update der CLM User mit den richtigen JID
				$jids = implode(',', $emails);
				// Array alte_jid => neue_jid
				$jid_new = array();
				$jid_new['NULL'] = 'NULL';
				foreach ($emails as $emails) {
					$query = "UPDATE #__clm_user " . " SET jid = " . $email_josid[$emails] . " , published = " . $publish . " WHERE email = '" . $emails . "'" . " AND sid =" . $saison . " AND user_clm < 80 ";
					//$db->setQuery($query);
					//$db->query();
					clm_core::$db->query($query);	
					$query = "UPDATE #__users " . " SET block =" . $block . " WHERE email = '" . $emails . "'" . " AND gid < 24 ";
					//$db->setQuery($query);
					//$db->query();
					clm_core::$db->query($query);	
					$jid_new[$email_altjid[$emails]] = $email_josid[$emails];
				}
			}
		}
		// User "Spielfrei" in Array
		$jid_new[1] = 1;
		$import = 0;
		// Falls keine User in Datei existieren, einen User "CLM-Import" anlegen
		if (($cnt_user == "0" AND $cnt_juser == "0") OR ($clm_user == "1")) {
			$sql = " SELECT id,jid FROM #__clm_user " . " WHERE email = 'import@clm.de' " . " AND sid =" . $saison;
			$db->setQuery($sql);
			$clm_exist = $db->loadObjectList();
			// User existiert nicht -> anlegen
			if (!$clm_exist) {
				$row = JTable::getInstance('users', 'TableCLM');
				$row->sid = $saison;
				$row->jid = '9999';
				$row->name = 'CLM-Import';
				$row->username = 'CLM-Import Saison ' . $saison;
				$row->aktive = "1";
				$row->email = "import@clm.de";
				$row->usertype = "sl";
				//$row->user_clm = "70";
				$row->zps = "1";
				$row->published = "1";
				$row->bemerkungen = "Dieser User ist nur für Importzwecke gedacht !";
				$row->bem_int = "Dieser User ist nur für Importzwecke gedacht !";
				// CLM User erstellen
				$row->store();
			}
			$import = "9999";
		}
		// Liga anlegen
		$row = JTable::getInstance('ligen', 'TableCLM');
		// Wenn ein andere User als die importierten gemeldet hat wird der SL verwendet !!
		$sl = $jid_new[(int)$liga_daten[6]];
		if ($liga != "new") {
			$row->load($liga);
		}
		$row->name = clm_core::$load->utf8encode(substr($liga_daten[0], 3));
		if (substr($row->name,0,1) == "'") $row->name = substr($row->name, 1);
		$row->sid = $saison;
		$row->teil = $liga_daten[2];
		$row->stamm = $liga_daten[3];
		$row->ersatz = $liga_daten[4];
		$row->rang = $liga_daten[5];
		if ($import > 1) {
			$row->sl = $import;
			$sl = $import;
		} else {
			$row->sl = $jid_new[(int)$liga_daten[6]];
		}
		$row->runden = $liga_daten[7];
		$row->durchgang = $liga_daten[8];
		$row->heim = $liga_daten[9];
		$row->mail = $liga_daten[10];
		$row->sl_mail = $liga_daten[11];
		$row->sieg_bed = $liga_daten[12];
		$row->runden_modus = $liga_daten[13];
		$row->man_sieg = $liga_daten[14];
		$row->man_remis = $liga_daten[15];
		$row->man_nieder = $liga_daten[16];
		$row->man_antritt = $liga_daten[17];
		$row->sieg = $liga_daten[18];
		$row->remis = $liga_daten[19];
		$row->nieder = $liga_daten[20];
		$row->antritt = $liga_daten[21];
		
		$row->order = $liga_daten[22];
		$row->rnd = $liga_daten[23];
		$row->published = $liga_daten[24];
		$bem = clm_core::$load->utf8encode($liga_daten[25]);
		$neu = str_replace('\r\n', "\n", $bem);
		$row->bemerkungen = $neu;
		$bem = clm_core::$load->utf8encode($liga_daten[26]);
		$neu = str_replace('\r\n', "\n", $bem);
		$row->bem_int = $neu;
		$row->checked_out_time = $liga_daten[27];
		$row->ordering = 0;  //substr($liga_daten[28], 0, -4);
		$row->b_wertung = $liga_daten[29];
		$row->liga_mt = $liga_daten[30];
		$row->tiebr1 = $liga_daten[31];
		$row->tiebr2 = $liga_daten[32];
		$row->tiebr3 = $liga_daten[33];
		$row->ersatz_regel = $liga_daten[34];
		$row->anzeige_ma = $liga_daten[35];
		$row->params = $liga_daten[36];
 		if (!$row->store()) {
			$mainframe->enqueueMessage($row->getError(),'warning');
			return;
		}
		// Neue Liga ID holen
		$lid = $row->id;
		// ggf. Vereine anlegen
		for ($x = 0;$x < $cnt_dwz_ver;$x++) {
			$verein = explode("','", $ver_dwz[$x]);
			$zps = $verein[1];
			$lv = $verein[2];
			$verb = clm_core::$load->utf8encode($verein[3]);
			$name = clm_core::$load->utf8encode(substr($verein[4], 0, -2));
			$sql = " SELECT ZPS FROM #__clm_dwz_vereine " . " WHERE sid = " . $saison . " AND ZPS ='$zps'";
			$db->setQuery($sql);
			$dwz_exist = $db->loadObjectList();
			// falls kein Verein mit ZPS und sid existiert -> anlegen
			if (!$dwz_exist OR $dwz_exist[0]->ZPS = "") {
				$sql = " INSERT INTO #__clm_dwz_vereine (`sid`,`ZPS`, `LV`,`Verband`,`Vereinname`) VALUES"
			." ('$saison','$zps','$lv','$verb','$name')"
			;
				//$db->setQuery($sql);
				//$db->query();
				clm_core::$db->query($sql);	
			}
		}
		// Mannschaften anlegen
		// Neue Liga -> alte Daten löschen
		if ($liga != "new") {
			$sql = " DELETE FROM #__clm_mannschaften WHERE liga = $liga AND sid = $saison";
			//$db->setQuery($sql);
			//$db->query();
			clm_core::$db->query($sql);	
		}
		for ($x = 0;$x < $cnt_man;$x++) {
			$man_einzel = explode("','", substr($man_daten[$x],0,-2));
			$row = JTable::getInstance('mannschaften', 'TableCLM');
			$row->sid = $saison;
			$row->name = clm_core::$load->utf8encode($man_einzel[1]);
			if ($liga == "new") {
				$row->liga = $lid;
			} else {
				$row->liga = $liga;
			}
			$row->zps = $man_einzel[3];
			$row->liste = $man_einzel[4];
			$row->edit_liste = $man_einzel[5];
			$row->man_nr = $man_einzel[6];
			$row->tln_nr = $man_einzel[7];
			if ($import > 0) {
				$row->mf = $import;
			} else {
				$row->mf = $jid_new[(int)$man_einzel[8]];
			}
			$row->sg_zps = $man_einzel[9];
			$row->datum = $man_einzel[10];
			$row->edit_datum = $man_einzel[11];
			$bem = clm_core::$load->utf8encode($man_einzel[12]);
			$neu = str_replace('\r\n', "\n", $bem);
			$row->lokal = $neu;
			$bem = clm_core::$load->utf8encode($man_einzel[13]);
			$neu = str_replace('\r\n', "\n", $bem);
			$row->bemerkungen = $neu;
			$bem = clm_core::$load->utf8encode($man_einzel[14]);
			$neu = str_replace('\r\n', "\n", $bem);
			$row->bem_int = $neu;
			$row->published = $man_einzel[15];
			$row->ordering = 0; //substr($man_einzel[16], 0, -1);
			$row->rankingpos = $man_einzel[17];
			$row->abzug = $man_einzel[18];
			$row->bpabzug = $man_einzel[19];
			if (!$row->store()) {
				$mainframe->enqueueMessage($row->getError(),'warning');
				return;
			}
		}
		// ggf. Spieler anlegen
		for ($x = 0;$x < $cnt_dwz_spl;$x++) {
			$spieler = explode("','", $spl_dwz[$x]);
			$zps = $spieler[2];
			$mgl = $spieler[3];
			$sql = " SELECT ZPS FROM #__clm_dwz_spieler " . " WHERE sid = " . $saison . " AND ZPS ='$zps'" . " AND Mgl_Nr =" . $mgl;
			$db->setQuery($sql);
			$dwz_exist = $db->loadObjectList();
			// falls kein Verein mit ZPS und sid existiert -> anlegen
			if (!$dwz_exist OR $dwz_exist[0]->ZPS = "") {
				$pkz = $spieler[1];
				$status = $spieler[4];
				$name = clm_core::$load->utf8encode($spieler[5]);
				$name_g = $spieler[6];
				$gesch = $spieler[7];
				$berech = $spieler[8];
				$geb_j = $spieler[9];
				$ausw = $spieler[10];
				if ($ausw == '') $ausw = 0;
				$dwz = $spieler[11];
				if ($dwz == '') $dwz = 0;
				$dwz_i = $spieler[12];
				if ($dwz_i == '') $dwz_i = 0;
				$elo = $spieler[13];
				if ($elo == '') $elo = 0;
				$titel = $spieler[14];
				$f_id = $spieler[15];
				if ($f_id == '') $f_id = 0;
				$f_land = clm_core::$load->utf8encode(substr($spieler[16], 0, -2));
				$sql = " INSERT INTO `#__clm_dwz_spieler` ( `sid`, `PKZ`, `ZPS`, `Mgl_Nr`, `Status`, `Spielername`, `Spielername_G`, `Geschlecht`, `Spielberechtigung`, `Geburtsjahr`, `Letzte_Auswertung`, `DWZ`, `DWZ_Index`, `FIDE_Elo`, `FIDE_Titel`, `FIDE_ID`, `FIDE_Land`) VALUES " . " ('$saison','$pkz','$zps','$mgl','$status','$name','$name_g','$gesch','$berech','$geb_j','$ausw','$dwz','$dwz_i','$elo','$titel','$f_id','$f_land')";
				//$db->setQuery($sql);
				//$db->query();
				clm_core::$db->query($sql);	
			}
		}
		// Meldelisten anlegen
		// Neue Liga -> alte Daten löschen
		if ($liga != "new") {
			$sql = " DELETE FROM #__clm_meldeliste_spieler WHERE lid = $liga AND sid = $saison";
			//$db->setQuery($sql);
			//$db->query();
			clm_core::$db->query($sql);	
		}
		for ($x = 0;$x < $cnt_spl;$x++) {
			$spl_einzel = explode("','", substr($melde_daten[$x],0,-2));

			$row 		= JTable::getInstance( 'meldelisten', 'TableCLM' );
			$row->sid		= $saison;
			if ($liga =="new") {	$row->lid = $lid; }
				else {		$row->lid = $liga; }
			$row->mnr		= $spl_einzel[2];
			$row->snr		= $spl_einzel[3];
			$row->mgl_nr		= $spl_einzel[4];
			$row->zps		= $spl_einzel[5];
			$row->status		= $spl_einzel[6];
			$row->ordering		= $spl_einzel[7];
			$row->start_dwz		= $spl_einzel[8];
			$row->start_I0		= $spl_einzel[9];
			$row->FIDEelo		= $spl_einzel[10];
			$row->DWZ		= $spl_einzel[11];
			$row->I0		= $spl_einzel[12];
			$row->Punkte		= $spl_einzel[13];
			$row->Partien		= $spl_einzel[14];
			$row->We		= $spl_einzel[15];
			$row->Leistung		= $spl_einzel[16];
			$row->EFaktor		= $spl_einzel[17];
			$row->Niveau		= $spl_einzel[18];
			$row->sum_saison	= $spl_einzel[19];
			
			if (!$row->store()) {
				$mainframe->enqueueMessage($row->getError(),'warning');
				return;
			}
		}

		// Mannschaftsrunden anlegen
		if ($liga != "new") {
			$sql = " DELETE FROM #__clm_rnd_man WHERE lid = $liga AND sid = $saison ";
			//$db->setQuery($sql);
			//$db->query();
			clm_core::$db->query($sql);	
		}
		for ($x = 0;$x < $cnt_rnd_man;$x++) {
			$man_rnd = explode("','", $man_rnd_daten[$x]);
			$row = JTable::getInstance('rnd_man', 'TableCLM');
			$row->sid = $saison;
			if ($liga == "new") {
				$row->lid = $lid;
			} else {
				$row->lid = $liga;
			}
			$row->runde = $man_rnd[2];
			$row->paar = $man_rnd[3];
			$row->dg = $man_rnd[4];
			$row->heim = $man_rnd[5];
			$row->tln_nr = $man_rnd[6];
			$row->gegner = $man_rnd[7];
			if ($man_rnd[8] == 'NULL') $row->brettpunkte = NULL;
			else $row->brettpunkte = $man_rnd[8];
			if ($man_rnd[9] == 'NULL') $row->manpunkte = NULL;
			else $row->manpunkte = $man_rnd[9];
			if ($man_rnd[10] == 'NULL') $row->bp_sum = NULL;
			else $row->bp_sum = $man_rnd[10];
			if ($man_rnd[11] == 'NULL') $row->mp_sum = NULL;
			else $row->mp_sum = $man_rnd[11];
			if ($man_rnd[12] != "NULL" AND $jid_new[$man_rnd[12]] != "") {
				$row->gemeldet = $jid_new[$man_rnd[12]];
				if ($import > 1) {
					$row->gemeldet = $import;
				}
			}
			if ($man_rnd[12] != "NULL" AND $jid_new[$man_rnd[12]] == "") {
				$row->gemeldet = $sl;
				if ($import > 1) {
					$row->gemeldet = $import;
				}
			}
			if ($man_rnd[13] != "NULL" AND $jid_new[$man_rnd[13]] != "") {
				$row->editor = $jid_new[$man_rnd[13]];
				if ($import > 1) {
					$row->editor = $import;
				}
			}
			if ($man_rnd[13] != "NULL" AND $jid_new[$man_rnd[13]] == "") {
				$row->editor = $sl;
				if ($import > 1) {
					$row->editor = $import;
				}
			}
			if ($man_rnd[14] != "NULL" AND $jid_new[$man_rnd[14]] != "") {
				$row->dwz_editor = $jid_new[$man_rnd[14]];
				if ($import > 1) {
					$row->dwz_editor = $import;
				}
			}
			if ($man_rnd[14] != "NULL" AND $jid_new[$man_rnd[14]] == "") {
				$row->dwz_editor = $sl;
				if ($import > 1) {
					$row->dwz_editor = $import;
				}
			}
			$row->zeit = $man_rnd[15];
			$row->edit_zeit = $man_rnd[16];
			$row->dwz_zeit = $man_rnd[17];
			$row->published = $man_rnd[18];
			$row->ordering = substr($man_rnd[19], 0, -2);
			if (!$row->store()) {
				$mainframe->enqueueMessage($row->getError(),'warning');
				return;
			}
		}
		// Spielerrunden anlegen
		if ($liga != "new") {
			$sql = " DELETE FROM #__clm_rnd_spl WHERE lid = $liga AND sid = $saison ";
			//$db->setQuery($sql);
			//$db->query();
			clm_core::$db->query($sql);	
		}
		for ($x = 0;$x < $cnt_rnd_spl;$x++) {
			$spl_rnd = explode("','", $spl_rnd_daten[$x]);
			$row = JTable::getInstance('rnd_spl', 'TableCLM');
			$row->sid = $saison;
			if ($liga == "new") {
				$row->lid = $lid;
			} else {
				$row->lid = $liga;
			}
			$row->runde = $spl_rnd[2];
			$row->paar = $spl_rnd[3];
			$row->dg = $spl_rnd[4];
			$row->tln_nr = $spl_rnd[5];
			$row->brett = $spl_rnd[6];
			$row->heim = $spl_rnd[7];
			$row->weiss = $spl_rnd[8];
			$row->spieler = $spl_rnd[9];
			$row->zps = $spl_rnd[10];
			$row->gegner = $spl_rnd[11];
			$row->gzps = $spl_rnd[12];
			if ($spl_rnd[13] != "NULL") {
				$row->ergebnis = $spl_rnd[13];
			}
			$row->kampflos = $spl_rnd[14];
			$row->punkte = $spl_rnd[15];
			if ($spl_rnd[16] != "NULL" AND $jid_new[$spl_rnd[16]] != "") {
				$row->gemeldet = $jid_new[$spl_rnd[16]];
				if ($import > 0) {
					$row->gemeldet = $import;
				}
			}
			if ($spl_rnd[16] != "NULL" AND $jid_new[$spl_rnd[16]] == "") {
				$row->gemeldet = $sl;
				if ($import > 0) {
					$row->gemeldet = $import;
				}
			}
			if ($spl_rnd[17] != "NULL") {
				$row->dwz_edit = $spl_rnd[17];
			}
			if (substr($spl_rnd[18], 0, -2) != "NULL" AND $jid_new[substr($spl_rnd[18], 0, -2) ] != "") {
				$row->dwz_editor = $jid_new[substr($spl_rnd[18], 0, -2) ];
				if ($import > 1) {
					$row->dwz_editor = $import;
				}
			}
			if (substr($spl_rnd[18], 0, -2) != "NULL" AND $jid_new[substr($spl_rnd[18], 0, -2) ] == "") {
				$row->dwz_editor = $sl;
				if ($import > 1) {
					$row->dwz_editor = $import;
				}
			}
			if (!$row->store()) {
				$mainframe->enqueueMessage($row->getError(),'warning');
				return;
			}
		}
		// Rundentermine anlegen
		if ($liga != "new") {
			$sql = " DELETE FROM #__clm_runden_termine WHERE liga = $liga AND sid = $saison ";
			//$db->setQuery($sql);
			//$db->query();
			clm_core::$db->query($sql);	
		}
		for ($x = 0;$x < $cnt_rnd_ter;$x++) {
			$spl_rnd = explode("','", $rnd_ter_daten[$x]);
			$row = JTable::getInstance('runden', 'TableCLM');
			$row->sid = $saison;
			if ($liga == "new") {
				$row->liga = $lid;
			} else {
				$row->liga = $liga;
			}
			$row->name = $spl_rnd[1];
			$row->nr = $spl_rnd[3];
			$row->datum = $spl_rnd[4];
			$row->meldung = $spl_rnd[5];
			$row->sl_ok = $spl_rnd[6];
			$row->published = $spl_rnd[7];
			$bem = clm_core::$load->utf8encode($spl_rnd[8]);
			$neu = str_replace('\r\n', "\n", $bem);
			$row->bemerkungen = $neu;
			$bem = clm_core::$load->utf8encode($spl_rnd[9]);
			$neu = str_replace('\r\n', "\n", $bem);
			$row->bem_int = $neu;
			if ($spl_rnd[10] != "NULL" AND $jid_new[$spl_rnd[10]] != "") {
				$row->gemeldet = $jid_new[$spl_rnd[10]];
				if ($import > 1) {
					$row->gemeldet = $import;
				}
			}
			if ($spl_rnd[10] != "NULL" AND $jid_new[$spl_rnd[10]] == "") {
				$row->gemeldet = $sl;
				if ($import > 1) {
					$row->gemeldet = $import;
				}
			}
			if ($spl_rnd[11] != "NULL" AND $jid_new[$spl_rnd[11]] != "") {
				$row->editor = $jid_new[$spl_rnd[11]];
				if ($import > 1) {
					$row->editor = $import;
				}
			}
			if ($spl_rnd[11] != "NULL" AND $jid_new[$spl_rnd[11]] == "") {
				$row->editor = $sl;
				if ($import > 1) {
					$row->editor = $import;
				}
			}
			$row->zeit = $spl_rnd[12];
			$row->edit_zeit = $spl_rnd[13];
			$row->ordering = substr($spl_rnd[14], 0, -2);
			if (!$row->store()) {
				$mainframe->enqueueMessage($row->getError(),'warning');
				return;
			}
		}
		// Ranglistengruppe anlegen
		// Ranglistendaten auslesen
	  if ($rang_name > "0") {
		$rang_dat = explode("','", $rang_name_daten[0]);
		$gruppe = clm_core::$load->utf8encode(substr($rang_dat[0], 3));
		$melde = $rang_dat[1];
		$gesch = clm_core::$load->utf8encode($rang_dat[2]);
		$grenze = $rang_dat[3];
		$alter = $rang_dat[4];
		$status = $rang_dat[5];
		$ruser = $rang_dat[7];
		$bem = $rang_dat[8];
		$ibem = $rang_dat[9];
		$order = $rang_dat[10];
		$publish = substr($rang_dat[11], 0, -2);;
		// existiert Rangliste schon ?!?
		$query = " SELECT id FROM #__clm_rangliste_name " . " WHERE Gruppe = '" . $gruppe . "'" . " AND Meldeschluss = '" . $melde . "'";
		$query .= " AND geschlecht  = '" . $gesch . "'" . " AND alter_grenze = " . $grenze . " AND `alter` = " . $alter . " AND `status` = '" . $status . "' AND sid = " . $saison . " AND user = " . $ruser;
		$db->setQuery($query);
		$res = $db->loadResult();
		if (!$res OR $res == "0") {
			$row = JTable::getInstance('ranggruppe', 'TableCLM');
			$row->Gruppe = $gruppe;
			$row->Meldeschluss = $melde;
			$row->geschlecht = $gesch;
			$row->alter_grenze = $grenze;
			$row->alter = $alter;
			$row->status = $status;
			$row->sid = $saison;
			$row->user = $ruser;
			$row->bemerkungen = $bem;
			$row->bem_int = $ibem;
			$row->ordering = $order;
			$row->published = $publish;
			if (!$row->store()) {
				$mainframe->enqueueMessage($row->getError(),'warning');
				return;
			}
			$rang_res = $row->id;
		} else {
		}
		// Ranglisten_id anlegen
		// Wenn kein Ranglistenname existiert einfügen der Daten
		if (!$res OR $res == "0") {
			for ($x = 0;$x < $rang_id;$x++) {
				$rid = explode("','", $rang_id_daten[$x]);
				$row = JTable::getInstance('ranglisten', 'TableCLM');
				$row->gid = $rang_res;
				$row->sid = $saison;
				$row->zps = $rid[2];
				$row->rang = $rid[3];
				$row->published = $rid[4];
				$row->bemerkungen = $rid[5];
				$row->bem_int = $rid[6];
				$row->ordering = substr($rid[7], 0, -2);
				if (!$row->store()) {
					$mainframe->enqueueMessage($row->getError(),'warning');
					return;
				}
			}
		}
		// Wenn Ranglistenname existiert dann abgleichen
		else {
			for ($x = 0;$x < $rang_id;$x++) {
				$rid = explode("','", $rang_id_daten[$x]);
				$zps = $rid[2];
				$rang = $rid[3];
				$published = $rid[4];
				$bemerkungen = $rid[5];
				$bem_int = $rid[6];
				$ordering = substr($rid[7], 0, -2);
				$query = " SELECT id FROM #__clm_rangliste_id " . " WHERE gid = '" . $res . "'" . " AND sid = " . $saison . " AND zps = '$zps'";
				$db->setQuery($query);
				$res_id = $db->loadResult();
				if (!$res_id OR $res_id == "0") {
					$row = JTable::getInstance('ranglisten', 'TableCLM');
					$row->gid = $res;
					$row->sid = $saison;
					$row->zps = $rid[2];
					$row->rang = $rid[3];
					$row->published = $rid[4];
					$row->bemerkungen = $rid[5];
					$row->bem_int = $rid[6];
					$row->ordering = substr($rid[7], 0, -2);
					if (!$row->store()) {
						$mainframe->enqueueMessage($row->getError(),'warning');
						return;
					}
				}
			}
		}
		// Ranglisten_spieler anlegen
		// Wenn kein Ranglistenname existiert einfügen der Daten
		if (!$res OR $res == "0") {
			for ($x = 0;$x < $rang_spl;$x++) {
				$rspl = explode("','", $rang_spl_daten[$x]);
				$row = JTable::getInstance('rangspieler', 'TableCLM');
				$row->Gruppe = $rang_res;
				$row->ZPS = $rspl[1];
				$row->Mgl_Nr = $rspl[2];
				$row->PKZ = $rspl[3];
				$row->Rang = $rspl[4];
				$row->man_nr = $rspl[5];
				$row->sid = $saison;
				if (!$row->store()) {
					$mainframe->enqueueMessage($row->getError(),'warning');
					return;
				}
			}
		}
		// Wenn Ranglistenname existiert dann abgleichen
		else {
			for ($x = 0;$x < $rang_spl;$x++) {
				$rspl = explode("','", $rang_spl_daten[$x]);
				$row = JTable::getInstance('rangspieler', 'TableCLM');
				$row->Gruppe = $res;
				$row->ZPS = $rspl[1];
				$row->Mgl_Nr = $rspl[2];
				$row->PKZ = $rspl[3];
				$row->Rang = $rspl[4];
				$row->man_nr = $rspl[5];
				$row->sid = $saison;
				$query = " SELECT Rang FROM #__clm_rangliste_spieler " . " WHERE Gruppe = '" . $res . "'" . " AND ZPS = '$row->ZPS'" . " AND Mgl_Nr = '$row->Mgl_Nr'";
				$db->setQuery($query);
				$res_spl = $db->loadResult();
				if (!$res_spl OR $res_spl == "0") {
					$row = JTable::getInstance('rangspieler', 'TableCLM');
					$row->Gruppe = $res;
					$row->ZPS = $rspl[1];
					$row->Mgl_Nr = $rspl[2];
					$row->PKZ = $rspl[3];
					$row->Rang = $rspl[4];
					$row->man_nr = $rspl[5];
					$row->sid = $saison;
					if (!$row->store()) {
						$mainframe->enqueueMessage($row->getError(),'warning');
						return;
					}
				}
			}
		}
	  }
		$msg = JText::_('DB_IMPORT') . ' ' . $datei . ' ' . JText::_('DB_ERFOLGREICH');
		$mainframe->enqueueMessage($msg,'message');
		$mainframe->redirect('index.php?option=com_clm&view=db');
	}
} ?>
